<?php

/** --------------------------------------------------------------------------------
 * The contraller generates the paynow buttons for each payment gateway
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account\Pay;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\Pay\Paypal\PayNowButtonResponse;
use App\Http\Responses\Account\Pay\ThankYouResponse;
use App\Repositories\Landlord\CheckoutRepository;
use App\Repositories\Landlord\PaypalRepository;
use Log;

class Paypal extends Controller {

    public function __construct(
        PaypalRepository $paypalrepo,
        CheckoutRepository $checkoutrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->paypalrepo = $paypalrepo;
        $this->checkoutrepo = $checkoutrepo;

    }

    /**
     * Creates a pay now button for the intial checkout (i.e. paying for subscription first time)
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function payNowButton($id) {

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
            'settings' => $settings,
            'subscription' => $subscription,
            'package' => $package,
            'tenant_id' => config('system.settings_saas_tenant_id'),
            'price_id' => ($subscription->subscription_gateway_billing_cycle == 'monthly') ? $package->package_gateway_stripe_price_monthly : $package->package_gateway_stripe_price_yearly,
            'cancel_url' => url('app/settings/account/notices'),
            'customer_first_name' => auth()->user()->first_name,
            'customer_last_name' => auth()->user()->last_name,
            'customer_email' => auth()->user()->email,
            'saas_company_name' => $settings->settings_company_name,
        ];

        //create a new stripe session
        if (!$response = $this->paypalrepo->initiateSubscriptionPayment($data)) {
            Log::error("unable to create a paypal [pay now] button", ['process' => '[permissions]', config('paypal-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        $payload = [
            'page' => $this->pageSettings(),
            'paypal_plan_id' => $response['plan_id'],
            'paypal_client_id' => $response['client_id'],
            'subscription_uniqueid' => $subscription->subscription_uniqueid,
            'checkout_session_id' => $response['checkout_session_id'],
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
        if (!request()->filled('checkout_session_id') || !request()->filled('subscription_id')) {
            Log::error("thank you page error - required information from the payment gateway is missing", ['process' => '[thank-you-page]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //get the subscription from paypal directly
        if (!$gateway_subscription = $this->paypalrepo->getSubscription(request('subscription_id'))) {
            Log::error("thank you page error - failed to to retrieve the subscription from the payment gateway", ['process' => '[pay]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //show error
            return new ThankYouResponse([
                'page' => $page,
                'status' => 'error',
            ]);
        }

        //standerdize the payment gateway's [subscription status], it for our internal use
        $gateway_subscription_status = (isset($gateway_subscription['status']) && $gateway_subscription['status'] == 'ACTIVE') ? 'completed' : 'pending';

        //complete the checkout session
        if (!$this->checkoutrepo->completeCheckoutSession([
            'checkout_session_id' => request('checkout_session_id'),
            'gateway_subscription_id' => request('subscription_id'),
            'gateway_name' => 'paypal',
            'gateway_subscription_status' => $gateway_subscription_status,
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