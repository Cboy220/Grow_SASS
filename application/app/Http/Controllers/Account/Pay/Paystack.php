<?php

/** --------------------------------------------------------------------------------
 * The contraller generates the paynow buttons for each payment gateway
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account\Pay;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\Pay\Paystack\PayNowButtonResponse;
use App\Http\Responses\Account\Pay\ThankYouResponse;
use App\Repositories\Landlord\CheckoutRepository;
use App\Repositories\Landlord\PaystackRepository;
use Log;

class Paystack extends Controller {

    public function __construct(
        PaystackRepository $paystackrepo,
        CheckoutRepository $checkoutrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->paystackrepo = $paystackrepo;
        $this->checkoutrepo = $checkoutrepo;

    }

    /**
     * Creates a pay now button for the intial checkout (i.e. paying for subscription first time)
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function payNowButton(PaystackRepository $paystackrepo, $id) {

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
            'settings_paystack_secret_key' => $settings->settings_paystack_secret_key,
            'currency' => $settings->settings_system_currency_code,
            'tenant_id' => config('system.settings_saas_tenant_id'),
            'subscription_id' => $subscription->subscription_id,
            'billing_cycle' => $subscription->subscription_gateway_billing_cycle,
            'cancel_url' => url('app/settings/account/notices'),
        ];

        //create a new paystack session
        if (!$checkout_session_id = $paystackrepo->initiateSubscriptionPayment($data)) {
            Log::error("unable to create a paystack checkout session", ['process' => '[permissions]', config('paystack-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //update subscription with paystack data
        $subscription->subscription_gateway_plan_id = ($subscription->subscription_gateway_billing_cycle == 'monthly') ? $package->package_gateway_paystack_product_monthly : $package->package_gateway_paystack_product_yearly;
        $subscription->subscription_gateway_price_id = ($subscription->subscription_gateway_billing_cycle == 'monthly') ? $package->package_gateway_paystack_price_monthly : $package->package_gateway_paystack_price_yearly;
        $subscription->save();

        $payload = [
            'page' => $this->pageSettings(),
            'checkout_session_id' => $checkout_session_id,
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
        if (!request()->filled('checkout_session_id') || !request()->filled('trxref')) {
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
            Log::error("unable to get the landlord settings table", ['process' => '[checkout-payment]', config('paystack-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get completion data from paystack
        if (!$checkout = $this->paystackrepo->getMatchingSubscription([
            'settings_paystack_secret_key' => $settings->settings_paystack_secret_key,
            'transaction_id' => request('trxref'),
        ])) {
            Log::error("thank you page error - failed to to retrieve the checkout session from the payment gateway", ['process' => '[pay]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //complete the checkout session
        if (!$this->checkoutrepo->completeCheckoutSession([
            'checkout_session_id' => request('checkout_session_id'),
            'gateway_subscription_id' => $checkout['subscription_id'],
            'gateway_name' => 'paystack',
            'gateway_subscription_status' => 'completed',

            //optional data
            'subscription_checkout_reference' => request('checkout_session_id'),
            'subscription_checkout_reference_2' => $checkout['authorization_code'],
            'subscription_checkout_reference_3' => $checkout['plan_id'],
            'subscription_checkout_reference_4' => $checkout['customer_id'],

            //the email token is needed when making future requests to the api to cancel the subscription
            'subscription_checkout_reference_5' => $checkout['email_token'],
            
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