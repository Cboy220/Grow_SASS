<?php

namespace App\Http\Controllers\Landlord\Webhooks\Paypal;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Paypal extends Controller {

    protected $paypal_endpoint;
    protected $paypal_url;
    protected $paypal_access_token;
    protected $paypal_client_id;
    protected $paypal_secret_key;

    public function __construct() {

        //parent
        parent::__construct();

        $this->middleware('guest');

        Log::info("a paypal webhook has been received - starting to process", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get settings
        $settings = $settings = \App\Models\Landlord\Settings::on('landlord')->Where('settings_id', 'default')->first();

        // set the env mode
        if ($settings->settings_paypal_mode == 'live') {
            $this->paypal_client_id = $settings->settings_paypal_live_client_id;
            $this->paypal_secret_key = $settings->settings_paypal_live_secret_key;
            $this->paypal_mode = 'live';
            $this->paypal_endpoint = 'https://api-m.paypal.com';
            $this->paypal_url = 'https://paypal.com';
            $this->paypal_verify_url = 'https://api.paypal.com/v1/notifications/verify-webhook-signature';
        } else {
            $this->paypal_client_id = $settings->settings_paypal_sandbox_client_id;
            $this->paypal_secret_key = $settings->settings_paypal_sandbox_secret_key;
            $this->paypal_mode = 'sandbox';
            $this->paypal_endpoint = 'https://api-m.sandbox.paypal.com';
            $this->paypal_url = 'https://sandbox.paypal.com';
            $this->paypal_verify_url = 'https://api.sandbox.paypal.com/v1/notifications/verify-webhook-signature';
        }

        //validate webhook
        if (!$data = $this->validateWebhook()) {
            //exit
            return false;
        }

        //process webhook
        $this->processWebhook($data);

        //status 200

    }

    /**
     * make sure the webhook came from paypal
     *
     */
    public function index() {
        //nothing here
    }

    /**
     * make sure the webhook came from paypal
     *
     */
    public function validateWebhook() {

        Log::info("validating the webhook with paypal - started", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        // get the data and parse
        $data = json_decode(request()->getContent(), true);
        return $data;

        //get access token
        if (!$paypal_access_token = $this->getAccessToken()) {
            Log::info("validating the webhook with paypal - failed - unable to get an access token", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get headers
        $header = [
            'Webhook-API-Version' => request()->header('PayPal-Version'),
            'Paypal-Transmission-Id' => request()->header('Paypal-Transmission-Id'),
            'Paypal-Transmission-Time' => request()->header('Paypal-Transmission-Time'),
            'Paypal-Cert-Url' => request()->header('Paypal-Cert-Url'),
            'Paypal-Auth-Algo' => request()->header('Paypal-Auth-Algo'),
            'Paypal-Transmission-Sig' => request()->header('Paypal-Transmission-Sig'),
        ];

        //log the payload
        Log::info("validating the webhook with paypal - started", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //add this in the namespace at the top - ( use Exception; )
        try {

            // verify the request is from PayPal by making a callback to paypal
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paypal_access_token,
                'Content-Type' => 'application/json',
            ])->post($this->paypal_verify_url, [
                'transmission_id' => $header['Paypal-Transmission-Id'],
                'transmission_time' => $header['Paypal-Transmission-Time'],
                'cert_url' => $header['Paypal-Cert-Url'],
                'auth_algo' => $header['Paypal-Auth-Algo'],
                'transmission_sig' => $header['Paypal-Transmission-Sig'],
            ]);

            if ($response->status() === 200) {
                Log::info("validating the webhook has - completed", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
                return true;
            } else {
                $error_message = $response->body();
                Log::error("the webhook could not be confirmed by paypal - will now exit: $error_message", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            Log::error("unable to verify webhook with paypal - connection error: $error_message", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the data payload
        return $data;

    }

    public function processWebhook($data = []) {

        Log::info("webhook payload", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);

        //check if this is not a duplicate webhoo
        if ($data['id'] != '') {
            if (\App\Models\Landlord\Webhook::On('landlord')->Where('webhooks_gateway_id', $data['id'])->exists()) {
                Log::info("webhook [" . $data['event_type'] . "] is a duplicate (" . $data['id'] . ") - will skip it", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                //friendly response to gateway
                return response('Webhook Handled', 200);
            }
        }

        // Handle the event based on the event type
        switch ($data['event_type']) {
        case 'PAYMENT.SALE.COMPLETED':
            $this->subscriptionPaid($data);
            break;
        case 'BILLING.SUBSCRIPTION.ACTIVATED':
            $this->subscriptionActivated($data);
            break;
        case 'BILLING.SUBSCRIPTION.CANCELLED':
            $this->subscriptionCancelled($data);
            break;
        case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
            //we will ignore this webhook and use the crm's grace period to eventually cancel the subscription if no payment comes
            break;
        default:
            Log::info("webhook [" . $data['event_type'] . "] is not on expected list - will ignore and exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }
    }

    /** -----------------------------------------------------------------------------------------------
     * This webhook is used for a [initial] and [renewal] payments. It will be used later as follows
     *    - record a new payment
     *    - updates the 'next due date' for the subscription
     *    - sets the subscription as active (landlord and tenant)
     *    - send thank you email to tenant
     *    - send new payment email to the admin
     * -----------------------------------------------------------------------------------------------*/
    protected function subscriptionPaid($data) {

        Log::info("webhook [" . $data['event_type'] . "] is for a subscription [payment/renewal] - will now process", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_gateway_id', $data['id'])
            ->Where('webhooks_crm_reference', 'subscription-payment')
            ->whereNotNull('webhooks_gateway_id')
            ->exists()) {
            Log::info("this webhook has already been recorded  - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //check if we do not have duplicate payment
        if (\App\Models\Payment::On('landlord')
            ->Where('payment_transaction_id', $data['resource']['id'])
            ->whereNotNull('payment_transaction_id')
            ->exists()) {
            Log::info("the payment for this webhook has already been processed - will ignore'", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //validation
        if (!isset($data['resource']['billing_agreement_id'])) {
            Log::error("the webhook is missing the (subscription id) - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //friendly response to gateway
            return response('Webhook Handled', 200);
        }

        //get the data
        Log::info("webhook is for a subscription id (" . $data['resource']['billing_agreement_id'] . ")", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //retrieve the actual subscription from paypal (using REST API)
        if (!$subscription = $this->getSubscription($data['resource']['billing_agreement_id'])) {
            Log::info("webhook [" . $data['event_type'] . "] has failed to process - unable to retrieve the subscriotion from paypal - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //we will return an error status, so that the payment gateway will try this webhook again later
            return response('Webhook Handled', 409);
        }

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'paypal';
        $webhook->webhooks_gateway_id = $data['id'];
        $webhook->webhooks_type = 'PAYMENT.SALE.COMPLETED';
        $webhook->webhooks_crm_reference = 'subscription-payment';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_amount = $data['resource']['amount']['total'];
        $webhook->webhooks_currency = $data['resource']['amount']['currency'];
        $webhook->webhooks_transaction_id = $data['resource']['id'];
        $webhook->webhooks_gateway_reference = $data['resource']['billing_agreement_id'];
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payment_date = \Carbon\Carbon::parse($data['resource']['create_time'])->format('Y-m-d');
        $webhook->webhooks_next_due_date = isset($subscription['billing_info']['next_billing_time']) ? \Carbon\Carbon::parse($subscription['billing_info']['next_billing_time'])->format('Y-m-d') : '';
        $webhook->webhooks_payload = json_encode($data);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //friendly response to gateway
        return response('Webhook Handled', 200);
    }

    /** -------------------------------------------------------------------------
     * This webhook is used for a [new] or [reactivated] subscriptions
     *    - records that the subscription has been activated at the gateway
     *    - updates the 'next due date' for the subscription
     *
     * [notes]
     * This webhook is used mostly as a backup because the subscription is also
     * verified and the above tasks are done at the 'thank you page'
     * but we do it here incase the thank you page did not load
     * -------------------------------------------------------------------------*/
    protected function subscriptionActivated($data) {

        Log::info("webhook [" . $data['event_type'] . "] is for a subscription [activation] - will now process", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_gateway_id', $data['id'])
            ->Where('webhooks_crm_reference', 'subscription-activated')
            ->whereNotNull('webhooks_gateway_id')->exists()) {
            Log::info("this webhook has already been recorded  - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //validation
        if (!isset($data['resource']['id'])) {
            Log::error("the webhook is missing the (subscription id) - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return;
        }

        //get the data
        Log::info("webhook is for a subscription id (" . $data['resource']['id'] . ")", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //retrieve the actual subscription from paypal (using REST API)
        if (!$subscription = $this->getSubscription($data['resource']['id'])) {
            Log::info("webhook [" . $data['event_type'] . "] has failed to process - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //we will return an error status, so that paypal will try this webhook again later
            return response('Webhook Handled', 409);
        }

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'paypal';
        $webhook->webhooks_gateway_id = $data['id'];
        $webhook->webhooks_type = 'BILLING.SUBSCRIPTION.ACTIVATED';
        $webhook->webhooks_crm_reference = 'subscription-activated';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_gateway_reference = $data['resource']['id'];
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_next_due_date = \Carbon\Carbon::parse($subscription['billing_info']['next_billing_time'])->format('Y-m-d');
        $webhook->webhooks_payload = json_encode($data);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-activated] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //friendly response to gateway
        return response('Webhook Handled', 200);
    }

    /** -------------------------------------------------------------------------
     * This webhook captures a cancelled subscription and will do the following
     *
     *   - cancel the subscription in the landlord database
     *   - cancel the subscription in the customers database
     *   - deactivate the customers account
     * -------------------------------------------------------------------------*/
    protected function subscriptionCancelled($data) {

        Log::info("webhook [" . $data['event_type'] . "] is for a subscription [cancellation] - will now process", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_gateway_id', $data['id'])
            ->Where('webhooks_crm_reference', 'subscription-cancelled')
            ->whereNotNull('webhooks_gateway_id')->exists()) {
            Log::info("this webhook has already been recorded  - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //validation
        if (!isset($data['resource']['id'])) {
            Log::error("the webhook is missing the (subscription id) - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return;
        }

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'paypal';
        $webhook->webhooks_gateway_id = $data['id'];
        $webhook->webhooks_type = 'BILLING.SUBSCRIPTION.CANCELLED';
        $webhook->webhooks_crm_reference = 'subscription-cancelled';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_gateway_reference = $data['resource']['id'];
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payload = json_encode($data);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-activated] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //friendly response to gateway
        return response('Webhook Handled', 200);
    }

    /**
     * get an access token from Paypal to use with REST requests
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function getAccessToken() {

        Log::info("getting authentication token - started", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //request an access token
        try {

            $response = Http::asForm()
                ->withBasicAuth($this->paypal_client_id, $this->paypal_secret_key)
                ->post($this->paypal_endpoint . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            //process response & return the token
            if ($response->successful()) {
                $payload = $response->json();
                if (isset($payload['access_token'])) {
                    Log::info("getting authentication token - completed", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'access_token' => $payload['access_token']]);
                    return $payload['access_token'];
                }
            }

        } catch (exception $e) {
            Log::error("getting authentication token failed: " . $e->getMessage(), ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::error("getting authentication token failed", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /**
     * get the subscription from Paypal using the REST API
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function getSubscription($subscription_id = '') {

        Log::info("retrieving subscription ($subscription_id) from Paypal - started", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validate
        if ($subscription_id == '') {
            Log::error("retrieving subscription from paypal failed - no subscription id was provided - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        // Retrieve the access token from PayPal API
        if (!$access_token = $this->getAccessToken()) {
            Log::error("retrieving subscription ($subscription_id) from paypal - unable to get an access token - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //retrieve the subscription from paypal
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
            ])->get($this->paypal_endpoint . "/v1/billing/subscriptions/{$subscription_id}");

            if ($response->ok()) {
                $subscription = $response->json();
                Log::info("retrieving subscription ($subscription_id) from paypal - succeeded", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $subscription]);
                return $subscription;
            } else {
                $payload = $response->json();
                Log::error("retrieving subscription ($subscription_id) from paypal - failed - will now exit", ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);
                return false;
            }
        } catch (Exception $e) {
            Log::error("retrieving subscription ($subscription_id) from paypal - failed - " . $e->getMessage(), ['process' => '[landlord][paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}
