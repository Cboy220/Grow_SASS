<?php

/** --------------------------------------------------------------------------------
 * This controller receives and processes webhook calls from Stripe
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Webhooks\Razorpay;
use App\Http\Controllers\Controller;
use App\Repositories\Landlord\CheckoutRepository;
use App\Repositories\Landlord\RazorpayRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Razorpay extends Controller {

    public $razorpayrepo;
    public $checkoutrepo;
    public $settings;

    public function __construct(CheckoutRepository $checkoutrepo, RazorpayRepository $razorpayrepo) {

        //parent
        parent::__construct();

        $this->middleware('guest');

        $this->checkoutrepo = $checkoutrepo;
        $this->razorpayrepo = $razorpayrepo;

        //get settings
        $this->settings = \App\Models\Settings::On('landlord')->Where('settings_id', 'default')->first();

    }

    /**
     * Receive and process razorpay webhook
     * @return null
     */
    public function index() {

        // Verify webhook signature
        if (!$this->verifyWebhook()) {
            return response('Invalid Razorpay signature', 400);
        }

        //get the payload data
        $payload = json_decode(request()->getContent());
        Log::info("webhook received [$payload->event] - will now review and process'", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        // Route to the correct method based on event name
        switch ($payload->event) {
        case 'subscription.charged':
            $this->subscriptionPaid($payload);
            return;
        case 'subscription.cancelled':
            $this->subscriptionCancelled($payload);
            return;
        default:
            Log::info("webhook [$payload->event] is not on the expected list - will now exit'", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('No expected webhook was found', 200);
        }

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
        Log::info("webhook [$payload->event] received - started'", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_source', 'razorpay')
            ->Where('webhooks_gateway_id', $payload->created_at)
            ->Where('webhooks_crm_reference', 'subscription-payment')
            ->whereNotNull('webhooks_gateway_id')
            ->exists()) {
            Log::info("this webhook has already been recorded  - will now exit", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //check if we do not have duplicate payment
        if (\App\Models\Payment::On('landlord')
            ->Where('payment_transaction_id', $payload->payload->payment->entity->id)
            ->whereNotNull('payment_transaction_id')
            ->exists()) {
            Log::info("the payment for this webhook has already been processed - will ignore'", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'razorpay';
        $webhook->webhooks_gateway_id = $payload->created_at; //razorpay events do not have a unique id, so will use the timestamp
        $webhook->webhooks_type = $payload->event;
        $webhook->webhooks_crm_reference = 'subscription-payment';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_amount = $payload->payload->payment->entity->amount / 100;
        $webhook->webhooks_currency = $payload->payload->payment->entity->currency;
        $webhook->webhooks_transaction_id = $payload->payload->payment->entity->id;
        $webhook->webhooks_gateway_reference = $payload->payload->subscription->entity->id;
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payment_date = \Carbon\Carbon::createFromTimestamp($payload->payload->payment->entity->created_at)->format('Y-m-d');
        $webhook->webhooks_next_due_date = \Carbon\Carbon::createFromTimestamp($payload->payload->subscription->entity->current_end)->format('Y-m-d');
        $webhook->webhooks_payload = json_encode($payload);
        $webhook->webhooks_status = 'new';
        $webhook->save();

        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
        Log::info("webhook [$payload->event] received - will now process directly'", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        //avoid duplicates
        if (\App\Models\Landlord\Webhook::On('landlord')
            ->Where('webhooks_source', 'razorpay')
            ->Where('webhooks_gateway_id', $payload->created_at)
            ->Where('webhooks_crm_reference', 'subscription-cancelled')
            ->whereNotNull('webhooks_gateway_id')
            ->exists()) {
            Log::info("this webhook has already been recorded  - will now exit", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return response('Webhook Handled', 200);
        }

        //get the data
        Log::info("webhook is for a subscription id (" . $payload->payload->subscription->entity->id . ")", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //record webhook for cron processing
        $webhook = new \App\Models\Landlord\Webhook();
        $webhook->setConnection('landlord');
        $webhook->webhooks_source = 'razorpay';
        $webhook->webhooks_gateway_id = $payload->created_at; //razorpay events do not have a unique id, so will use the timestamp
        $webhook->webhooks_type = $payload->event;
        $webhook->webhooks_crm_reference = 'subscription-cancelled';
        $webhook->webhooks_transaction_type = 'subscription';
        $webhook->webhooks_gateway_reference = $payload->payload->subscription->entity->id;
        $webhook->webhooks_gateway_reference_type = 'gateway.subscription.id';
        $webhook->webhooks_payload = json_encode($payload);
        $webhook->webhooks_status = 'new';
        $webhook->save();
        Log::info("Webhook for [subscription-payment] has been scheduled for cronjob processing - will now exit", ['process' => '[landlord][razorpay-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //friendly response to gateway
        return response('Webhook Handled', 200);

    }

    /** -------------------------------------------------------------------------
     * Razorpay does not have this webhook. We deal with all activation tasks
     * in the subscriptionPaid() method
     * -------------------------------------------------------------------------*/
    protected function subscriptionActivated($data) {
        //nothing here
    }

    /**
     * Verify Razorpay webhook signature
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function verifyWebhook() {

        //TEMP
        return true;
        // Retrieve the signature and timestamp from the headers
        $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'];
        $timestamp = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE_TIMESTAMP'];

        // Verify the signature using your Razorpay webhook secret
        $webhook_secret = $this->settings->settings_razorpay_webhooks_secret;
        $expected_signature = hash_hmac('sha256', $timestamp . '.' . $payload, $webhook_secret);

        if (!hash_equals($signature, $expected_signature)) {
            // Invalid signature, handle accordingly
            abort(403, 'Invalid signature.');
        }
    }

}