<?php

/** --------------------------------------------------------------------------------
 * This controller receives and processes webhook calls from Stripe
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Webhooks\Stripe;
use App\Http\Controllers\Controller;
use App\Repositories\Landlord\CheckoutRepository;
use Exception;
use Log;

class Stripe extends Controller {

    public $api_version;
    public $checkoutrepo;

    public function __construct(CheckoutRepository $checkoutrepo) {

        //parent
        parent::__construct();

        $this->middleware('guest');

        //[IMPORTANT] do not change this 'exact' API version date. The expected webhooks data will not match.
        $this->api_version = '2022-11-15';

        $this->checkoutrepo = $checkoutrepo;

    }

    /**
     * Receive and process stripe webhook
     * @return null
     */
    public function index() {

        //get the payload body
        $payload = @file_get_contents('php://input');

        //log
        //Log::info("received a new webhook call from Stripe", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        //attempt to process the webhook
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $_SERVER['HTTP_STRIPE_SIGNATURE'], config('system.settings_stripe_webhooks_key')
            );
        } catch (\UnexpectedValueException$e) {
            Log::error("stripe webhook data is invalid", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);
            http_response_code(400);
            die('Stripe payload is invalid');
        } catch (\Stripe\Exception\SignatureVerificationException$e) {
            Log::critical("Stripe signing id (signature) does not match the one in database", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);
            http_response_code(400);
            die('Signing signature does not match');
        }

        // Handle the event based on the event type
        switch ($event->type) {
        case 'invoice.paid':
            $this->subscriptionPaid($event);
            break;
        case 'checkout.session.completed':
            $this->checkoutSessionCompleted($event);
            break;
        case 'customer.subscription.deleted':
            $this->subscriptionCancelled($event);
            break;
        case 'invoice.payment_failed':
            //we will ignore this webhook and use the crm's grace period to eventually cancel the subscription if no payment comes
            break;
        default:
            Log::info("webhook [$event->type] is not on expected list - will ignore and exit", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }
    }

    /**
     * This event is a backup to the 'Thank you page' prcess. We will not queue the webhook it but will action it now
     * We do this just incase there was an error on the thank you page. (also, Stripe fires its webhooks before the thank you have has even loaded)
     * It does the following tasks, usisng the same process as the 'Thank you pahe" e.i. the $checkoutrepo->completeCheckoutSession()
     *
     *    - Completes the checkout and marks the subscription and customers database as active
     *    - adds the payment gateway subscription id to the subscription
     *
     * @param object $event stripe webhook event
     * @return null
     */
    private function checkoutSessionCompleted($event) {

        //log
        Log::info("webhook [$event->type] received - will now process directly'", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'event' => $event]);

        //complete the checkout session
        if (!$this->checkoutrepo->completeCheckoutSession([
            'checkout_session_id' => $event->data->object->id,
            'gateway_subscription_id' => $event->data->object->subscription,
            'gateway_name' => 'stripe',
            'gateway_subscription_status' => (isset($event->data->object->payment_status) && $event->data->object->payment_status == 'paid') ? 'completed' : 'pending',
        ])) {
            Log::error("webhook [$event->type] failed - unable to complete checkout session", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            http_response_code(419);
            exit('Webhook Processing Error');
        }

        //inform stripe "all ok"
        http_response_code(200);
        exit('Webhook Received Ok');
    }

    /** -----------------------------------------------------------------------------------------------
     * This webhook is used for a [initial] and [renewal] payments.
     *    - record a new payment
     *    - updates the 'next due date' for the subscription
     *    - sets the subscription as active (landlord and tenant)
     *    - send thank you email to tenant
     *    - send new payment email to the admin
     * -----------------------------------------------------------------------------------------------*/
    protected function subscriptionPaid($event) {

        Log::info("webhook [" . $event->type . "] is for a subscription [payment/renewal] - will now process", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_gateway_id', $event->data->object->id)
            ->Where('webhooks_crm_reference', 'subscription-payment')
            ->whereNotNull('webhooks_gateway_id')->exists()) {
            Log::info("this webhook has already been recorded - will now exit", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //check if we do not have duplicate payment
        if (\App\Models\Payment::On('landlord')
            ->Where('payment_transaction_id', $event->data->object->charge)
            ->whereNotNull('payment_transaction_id')
            ->exists()) {
            Log::info("the payment for this webhook has already been processed - will ignore'", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //validation
        if (!isset($event->data->object->subscription)) {
            Log::error("the webhook is missing the (subscription id) - will now exit", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //get the data
        Log::info("webhook is for a subscription id (" . $event->data->object->subscription . ")", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'stripe';
        $webhook->webhooks_gateway_id = $event->data->object->id;
        $webhook->webhooks_type = $event->type;
        $webhook->webhooks_crm_reference = 'subscription-payment';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_amount = $event->data->object->amount_paid / 100;
        $webhook->webhooks_currency = $event->data->object->currency;
        $webhook->webhooks_transaction_id = $event->data->object->charge;
        $webhook->webhooks_gateway_reference = $event->data->object->subscription;
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payment_date = date('Y-m-d', $event->data->object->created);
        $webhook->webhooks_next_due_date = isset($event->data->object->lines->data[0]->period->end) ? date('Y-m-d', $event->data->object->lines->data[0]->period->end) : '';
        $webhook->webhooks_payload = json_encode($event);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //friendly response to gateway
        return response('Webhook Handled', 200);
    }

    /** -----------------------------------------------------------------------------------------------
     * This webhook captures a cancelled subscription and will do the following
     *
     *   - cancel the subscription in the landlord database
     *   - cancel the subscription in the customers database
     *   - deactivate the customers account
     * -----------------------------------------------------------------------------------------------*/
    protected function subscriptionCancelled($event) {

        Log::info("webhook [" . $event->type . "] is for a subscription [cancellation] - will now process", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validation
        if (!isset($event->data->object->id)) {
            Log::error("the webhook is missing the (subscription id) - will now exit", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_gateway_id', $event->data->object->id)
            ->Where('webhooks_crm_reference', 'subscription-cancelled')
            ->whereNotNull('webhooks_gateway_id')->exists()) {
            Log::info("this webhook has already been recorded - will now exit", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //get the data
        Log::info("webhook is for a subscription id (" . $event->data->object->id . ")", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'stripe';
        $webhook->webhooks_gateway_id = $event->data->object->id;
        $webhook->webhooks_type = $event->type;
        $webhook->webhooks_crm_reference = 'subscription-cancelled';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_gateway_reference = $event->data->object->id;
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payload = json_encode($event);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //friendly response to gateway
        return response('Webhook Handled', 200);
    }

    /**
     * get a subscription from stripe
     * @param string $subscription_stripe_id the unique stripe id
     * @return mixed error message or true
     */
    protected function getSubscription($subscription_stripe_id) {

        Log::info("retrieving a subscription from stripe - started", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id' => $subscription_stripe_id]);

        //set stripe key
        try {
            \Stripe\Stripe::setApiKey(config('system.settings_stripe_secret_key'));
            \Stripe\Stripe::setApiVersion($this->api_version);
        } catch (Exception $e) {
            Log::error("unable to initialize and connect to stripe", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $e->getMessage()]);
            return false;
        }

        //validation
        if ($subscription_stripe_id == '') {
            Log::error("retrieving a subscription from stripe failed - a subscription id was not provided", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id' => $subscription_stripe_id]);
            return false;
        }

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_webhooks_key'));
            $subscription = $stripe->subscriptions->retrieve(
                $subscription_stripe_id,
                []
            );
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("retrieving a subscription from stripe failed - unable to authenticate with Stripe. Check your API keys", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id' => $subscription_stripe_id]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("retrieving a subscription from stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id' => $subscription_stripe_id]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id' => $subscription_stripe_id]);
            return false;
        }

        //final check
        if (!is_object($subscription)) {
            Log::error("unable to retrieve the subscription from stripe", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id' => $subscription_stripe_id]);
            return false;
        }

        //return the subscription
        Log::info("retrieving a subscription from stripe - completed", ['process' => '[landlord][stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription' => $subscription]);
        return $subscription;
    }

}