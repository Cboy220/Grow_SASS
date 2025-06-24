<?php

/** --------------------------------------------------------------------------------
 * This controller receives and processes webhook calls from Stripe
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Webhooks\Paystack;
use App\Http\Controllers\Controller;
use App\Repositories\Landlord\CheckoutRepository;
use App\Repositories\Landlord\PaystackRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Paystack extends Controller {

    public $paystackrepo;
    public $checkoutrepo;
    public $settings;

    public function __construct(CheckoutRepository $checkoutrepo, PaystackRepository $paystackrepo) {

        //parent
        parent::__construct();

        $this->middleware('guest');

        $this->checkoutrepo = $checkoutrepo;
        $this->paystackrepo = $paystackrepo;

        //get settings
        $this->settings = \App\Models\Settings::On('landlord')->Where('settings_id', 'default')->first();

    }

    /**
     * Receive and process paystack webhook
     * @return null
     */
    public function index() {

        // Verify webhook signature
        if (!$this->verifyWebhook()) {
            return response('Invalid Paystack signature', 400);
        }

        //get the payload data
        $payload = json_decode(request()->getContent());

        // Route to the correct method based on event name
        switch ($payload->event) {
        case 'invoice.update':
            $this->subscriptionPaid($payload);
            return;
        case 'charge.success':
            $this->checkoutSessionCompleted($payload);
            return;
        case 'subscription.not_renew':
            $this->subscriptionCancelled($payload);
            return;
        default:
            Log::info("webhook [$payload->event] is not on the expected list - will now exit'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('No expected webhook was found', 200);
        }

    }

    /**
     * This event is a backup to the 'Thank you page' prcess. We will not queue the webhook it but will action it now
     * We do this just incase there was an error on the thank you page.
     * It does the following tasks, usisng the same process as the 'Thank you pahe" e.i. the $checkoutrepo->completeCheckoutSession()
     *
     *    - fetches a matching subscription for this payment
     *    - completes the checkout and marks the subscription and customers database as active
     *    - adds the payment gateway subscription id to the subscription
     *
     * [notes]
     *    - this webhook will only process the initial payment for the subscription
     *    - we will know this by looking for the 'our_checkout_session_id' in the metadata (this was added during checkout)
     *
     * @param string $payload webhook payload from gateway
     * @return bool
     */
    private function checkoutSessionCompleted($payload) {

        //log
        Log::info("webhook [$payload->event] received - will now process directly'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        //check if this is for an intial checkout (we will look for 'our_checkout_session_id' in the metadata)
        if (isset($payload->data->metadata) && property_exists($payload->data->metadata, 'our_checkout_session_id')) {
            $checkout_session_id = $payload->data->metadata->our_checkout_session_id;
        } else {
            Log::info("webhook [$payload->event] is not for an initial checkout session - will ignore'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //get completion data from paystack
        if (!$checkout = $this->paystackrepo->getMatchingSubscription([
            'settings_paystack_secret_key' => $this->settings->settings_paystack_secret_key,
            'transaction_id' => $payload->data->reference,
        ])) {
            Log::error("webhook [$payload->event] failed - unable to retrieve the checkout session from the payment gateway", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //exit with error so that the gateway will try again
            return response('Webhook Error', 400);
        }

        //complete the checkout session
        if (!$this->checkoutrepo->completeCheckoutSession([
            'checkout_session_id' => $checkout_session_id,
            'gateway_subscription_id' => $checkout['subscription_id'],
            'gateway_name' => 'paystack',
            'gateway_subscription_status' => 'completed',

            //optional data
            'subscription_checkout_reference' => $checkout_session_id,
            'subscription_checkout_reference_2' => $checkout['authorization_code'],
            'subscription_checkout_reference_3' => $checkout['plan_id'],
            'subscription_checkout_reference_4' => $checkout['customer_id'],
            'subscription_checkout_reference_5' => $checkout['email_token'],
            'subscription_checkout_payload' => json_encode($checkout),
        ])) {
            Log::error("webhook [$payload->event] failed - unable to complete the checkout session", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //exit with error so that the gateway will try again
            return response('Webhook Error', 400);
        }

        //record webhook for the payment
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'paystack';
        $webhook->webhooks_gateway_id = $payload->data->reference;
        $webhook->webhooks_type = $payload->event;
        $webhook->webhooks_crm_reference = 'subscription-payment';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_amount = $payload->data->amount / 100;
        $webhook->webhooks_currency = $payload->data->currency;
        $webhook->webhooks_transaction_id = $payload->data->reference;
        $webhook->webhooks_gateway_reference = $checkout['subscription_id'];
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payment_date = \Carbon\Carbon::parse($payload->data->paid_at)->format('Y-m-d');
        $webhook->webhooks_next_due_date = \Carbon\Carbon::parse($checkout['next_payment_date'])->format('Y-m-d');
        $webhook->webhooks_payload = json_encode($payload);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        Log::info("webhook [$payload->event] - completed'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //all ok - edit
        return response('Webhook Handled', 200);
    }

    /** -----------------------------------------------------------------------------------------------
     * This webhook is used for [renewal] payments.
     *
     *    - record the payment
     *    - updates the 'next due date' for the subscription
     *    - sets the subscription as active (landlord and tenant)
     *    - send thank you email to tenant
     *    - send new payment email to the admin
     *
     * @param string $payload webhook payload from gateway
     * @return bool
     * -----------------------------------------------------------------------------------------------*/
    protected function subscriptionPaid($payload) {

        //log
        Log::info("webhook [$payload->event] received - started'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        //check if this is for a subscription renewal
        if (!isset($payload->data->subscription->subscription_code)) {
            Log::error("webhook [$payload->event] is not for a subscription - will ignore'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_gateway_id', $payload->data->transaction->reference)
            ->Where('webhooks_crm_reference', 'subscription-payment')
            ->whereNotNull('webhooks_gateway_id')
            ->exists()) {
            Log::info("this webhook has already been recorded  - will now exit", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //check if we do not have duplicate payment
        if (\App\Models\Payment::On('landlord')
            ->Where('payment_transaction_id', $payload->data->transaction->reference)
            ->whereNotNull('payment_transaction_id')
            ->exists()) {
            Log::info("the payment for this webhook has already been processed - will ignore'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'paystack';
        $webhook->webhooks_gateway_id = $payload->data->transaction->reference;
        $webhook->webhooks_type = $payload->event;
        $webhook->webhooks_crm_reference = 'subscription-payment';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_amount = $payload->data->transaction->amount / 100;
        $webhook->webhooks_currency = $payload->data->transaction->currency;
        $webhook->webhooks_transaction_id = $payload->data->transaction->reference;
        $webhook->webhooks_gateway_reference = $payload->data->subscription->subscription_code;
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payment_date = \Carbon\Carbon::parse($payload->data->paid_at)->format('Y-m-d');
        $webhook->webhooks_next_due_date = \Carbon\Carbon::parse($payload->data->subscription->next_payment_date)->format('Y-m-d');
        $webhook->webhooks_payload = json_encode($payload);
        $webhook->webhooks_status = 'new';
        $webhook->save();

        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return response('Webhook Handled', 200);

    }

    /** -----------------------------------------------------------------------------------------------
     * This webhook captures a cancelled subscription and will do the following
     *
     *   - cancel the subscription in the landlord database
     *   - cancel the subscription in the customers database
     *   - deactivate the customers account
     *
     * @param string $payload webhook payload from gateway
     * @return bool
     * -----------------------------------------------------------------------------------------------*/
    protected function subscriptionCancelled($payload) {

        //log
        Log::info("webhook [$payload->event] received - will now process directly'", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        //validation
        if (!isset($payload->data->subscription_code)) {
            Log::error("the webhook is missing the (subscription id) - will now exit", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_gateway_id', $payload->data->subscription_code)
            ->Where('webhooks_crm_reference', 'subscription-cancelled')
            ->whereNotNull('webhooks_gateway_id')
            ->exists()) {
            Log::info("this webhook has already been recorded  - will now exit", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //get the data
        Log::info("webhook is for a subscription id (" . $payload->data->subscription_code . ")", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'paystack';
        $webhook->webhooks_gateway_id = $payload->data->subscription_code;
        $webhook->webhooks_type = $payload->event;
        $webhook->webhooks_crm_reference = 'subscription-cancelled';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_gateway_reference = $payload->data->subscription_code;
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payload = json_encode($payload);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][paystack-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //friendly response to gateway
        return response('Webhook Handled', 200);

    }

    /** -------------------------------------------------------------------------
     * Paystack does not have this webhook. We deal with all activation tasks
     * in the subscriptionPaid() method
     * -------------------------------------------------------------------------*/
    protected function subscriptionActivated($data) {
        //nothing here
    }

    /**
     * Verify Paystack webhook signature
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function verifyWebhook() {

        //TEMP
        return true;

        Log::info("paystack webhook verification - started", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        // only a post with paystack signature header gets our attention
        if (request()->isMethod('post') && request()->header('X-PAYSTACK-SIGNATURE')) {
            $payload = request()->getContent();
            if (request()->header('X-PAYSTACK-SIGNATURE') === hash_hmac('sha512', $payload, $this->settings->settings_paystack_secret_key)) {
                Log::info("paystack webhook verification - completed", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return true;
            }
        }

        Log::error("paystack webhook verification - failed", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}