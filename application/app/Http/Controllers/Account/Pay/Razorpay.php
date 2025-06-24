<?php

/** --------------------------------------------------------------------------------
 * The contraller generates the paynow buttons for each payment gateway
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account\Pay;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\Pay\Razorpay\InitiatePaymentResponse;
use App\Http\Responses\Account\Pay\Razorpay\PayNowButtonResponse;
use App\Http\Responses\Account\Pay\ThankYouResponse;
use App\Repositories\Landlord\CheckoutRepository;
use App\Repositories\Landlord\RazorpayRepository;
use Log;

class Razorpay extends Controller {

    public function __construct(
        RazorpayRepository $razorpayrepo,
        CheckoutRepository $checkoutrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->razorpayrepo = $razorpayrepo;
        $this->checkoutrepo = $checkoutrepo;

    }

    /**
     * show the first step for razorpay (the paynow button)
     * We have done it this way, to avoid creating a new subscriotion at Razorpay each time
     * a user selects it as a payment option.
     *
     * This is because the Razorpay checkout flow always create a new subscription at the gateway
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payNowButton($id) {

        $payload = [
            'subscription_uniqueid' => $id,
        ];

        //create the pay now button
        return new PayNowButtonResponse($payload);
    }

    /**
     * The paynow button has been clicked
     *  1. Create a checkout session (same as other gateways)
     *  2. Render a page that automatically triggers that ppoup payment button
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function initiatePayment(RazorpayRepository $razorpayrepo, $id) {

        //get the payment payload data
        if (!$payment_data = $this->checkoutrepo->getPaymentData($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the payment data
        $subscription = $payment_data['subscription'];
        $package = $payment_data['package'];
        $settings = $payment_data['settings'];

        //payment payload
        $data = [
            'package' => $package,
            'package_id' => $package->package_id,
            'settings_razorpay_api_key' => $settings->settings_razorpay_api_key,
            'settings_razorpay_secret_key' => $settings->settings_razorpay_secret_key,
            'currency' => $settings->settings_system_currency_code,
            'tenant_id' => config('system.settings_saas_tenant_id'),
            'subscription_id' => $subscription->subscription_id,
            'billing_cycle' => $subscription->subscription_gateway_billing_cycle,
            'cancel_url' => url('app/settings/account/notices'),
        ];

        //create a new razorpay session
        if (!$checkout_session = $razorpayrepo->initiateSubscriptionPayment($data)) {
            Log::error("unable to create a razorpay checkout session", ['process' => '[permissions]', config('razorpay-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //update subscription with razorpay data
        $subscription->subscription_gateway_plan_id = $checkout_session['plan_id'];
        $subscription->save();

        $payload = [
            'page' => $this->pageSettings(),
            'checkout_session' => $checkout_session,
            'landlord_settings' => $settings,
            'public_key' => $settings->settings_razorpay_api_key,
            'logo_url' => runtimeLogoLarge(),
            'plan_name' => $package['package_name'],
        ];

        //create the pay now button
        return new InitiatePaymentResponse($payload);

    }

    /**
     * Show the thank you page and update/activate the customer's subscription
     * @return blade view | ajax view
     */
    public function thankYouPage() {

        //page settinf
        $page = $this->pageSettings('index');

        //error processing payment
        if (request('payment_status') == 'error') {
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //basic validation
        if (!request()->filled('checkout_session_id') || !request()->filled('payment_id')) {
            Log::error("thank you page error - required information from the payment gateway is missing", ['process' => '[thank-you-page]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //get landlord settings
        if (!$settings = \App\Models\Landlord\Settings::on('landlord')
            ->Where('settings_id', 'default')
            ->first()) {
            Log::error("unable to get the landlord settings table", ['process' => '[checkout-payment]', config('razorpay-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //get completion data from razorpay
        if (!$checkout = $this->razorpayrepo->getMatchingSubscription([
            'settings_razorpay_api_key' => $settings->settings_razorpay_api_key,
            'settings_razorpay_secret_key' => $settings->settings_razorpay_secret_key,
            'checkout_session_id' => request('checkout_session_id'),
        ])) {
            Log::error("thank you page error - failed to to retrieve the matching subscription at the gateway, or it was not marked as completed", ['process' => '[pay]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //complete the checkout session
        if (!$this->checkoutrepo->completeCheckoutSession([
            'checkout_session_id' => request('checkout_session_id'),
            'gateway_subscription_id' => request('checkout_session_id'),
            'gateway_name' => 'razorpay',
            'gateway_subscription_status' => 'completed',

            //optional data
            'subscription_checkout_reference' => request('checkout_session_id'),
            'subscription_checkout_reference_2' => request('payment_id'),
            'subscription_checkout_reference_3' => '',
            'subscription_checkout_reference_4' => '',
            'subscription_checkout_reference_5' => '',

            'subscription_checkout_payload' => json_encode($checkout),
        ])) {
            Log::error("thank you page error - unable to complete the checkout session", ['process' => '[thank-you-page]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //show success
        return new ThankYouResponse([
            'page' => $page,
            'status' => 'success',
        ]);

    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.my_account'),
                __('lang.payments'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.billing'),
            'heading' => __('lang.billing'),
        ];

        return $page;
    }

}