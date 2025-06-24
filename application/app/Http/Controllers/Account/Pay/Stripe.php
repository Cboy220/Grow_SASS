<?php

/** --------------------------------------------------------------------------------
 * The contraller generates the paynow buttons for each payment gateway
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account\Pay;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\Pay\Stripe\PayNowButtonResponse;
use App\Http\Responses\Account\Pay\ThankYouResponse;
use App\Repositories\Landlord\CheckoutRepository;
use App\Repositories\Landlord\StripeRepository;
use Log;

class Stripe extends Controller {

    public function __construct(
        StripeRepository $striperepo,
        CheckoutRepository $checkoutrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->striperepo = $striperepo;
        $this->checkoutrepo = $checkoutrepo;

    }

    /**
     * Creates a pay now button for the intial checkout (i.e. paying for subscription first time)
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function payNowButton(StripeRepository $striperepo, $id) {

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
            'settings_stripe_secret_key' => $settings->settings_stripe_secret_key,
            'currency' => $settings->settings_system_currency_code,
            'tenant_id' => config('system.settings_saas_tenant_id'),
            'subscription_id' => $subscription->subscription_id,
            'billing_cycle' => $subscription->subscription_gateway_billing_cycle,
            'cancel_url' => url('app/settings/account/notices'),
        ];

        //create a new stripe session
        if (!$checkout_session_id = $striperepo->initiateSubscriptionPayment($data)) {
            Log::error("unable to create a stripe checkout session", ['process' => '[permissions]', config('stripe-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //update subscription with stripe data
        $subscription->subscription_gateway_plan_id = ($subscription->subscription_gateway_billing_cycle == 'monthly') ? $package->package_gateway_stripe_product_monthly : $package->package_gateway_stripe_product_yearly;
        $subscription->subscription_gateway_price_id = ($subscription->subscription_gateway_billing_cycle == 'monthly') ? $package->package_gateway_stripe_price_monthly : $package->package_gateway_stripe_price_yearly;
        $subscription->save();

        $payload = [
            'page' => $this->pageSettings(),
            'checkout_session_id' => $checkout_session_id,
            'settings_stripe_public_key' => $settings->settings_stripe_public_key,
            'landlord_settings' => $settings,
        ];

        //create the pay now button
        return new PayNowButtonResponse($payload);

    }

    /**
     * Show the thank you page and update/activate the customer's subscription
     * @return blade view | ajax view
     */
    public function thankYouPage() {

        //page settinf
        $page = $this->pageSettings('index');

        //basic validation
        if (!request()->filled('checkout_session_id')) {
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
            Log::error("unable to get the landlord settings table", ['process' => '[checkout-payment]', config('stripe-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get the subscription from paypal directly
        if (!$checkout_session = $this->striperepo->getCheckoutSession([
            'settings_stripe_secret_key' => $settings->settings_stripe_secret_key,
            'checkout_session_id' => request('checkout_session_id'),
        ])) {
            Log::error("thank you page error - failed to to retrieve the checkout session from the payment gateway", ['process' => '[pay]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //standerdize the payment gateway's [subscription status], it for our internal use
        $checkout_status = (isset($checkout_session->payment_status) && $checkout_session->payment_status == 'paid') ? 'completed' : 'pending';

        //complete the checkout session
        if (!$this->checkoutrepo->completeCheckoutSession([
            'checkout_session_id' => request('checkout_session_id'),
            'gateway_subscription_id' => $checkout_session->subscription,
            'gateway_name' => 'stripe',
            'gateway_subscription_status' => $checkout_status,
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