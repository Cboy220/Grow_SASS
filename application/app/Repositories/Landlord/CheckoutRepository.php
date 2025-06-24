<?php

/** --------------------------------------------------------------------------------
 * Used during checkout for customer account subscription. It used by all payment
 * gateway checkout (i.e. common functions)
 *
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class CheckoutRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /**
     * get all the data needed to initiate a payment
     *
     * @param  int  $id subscription id
     * @return \Illuminate\Http\Response
     */
    public function getPaymentData($subscription_id) {

        //get the subscription
        if (!$subscription = \App\Models\Landlord\Subscription::on('landlord')
            ->Where('subscription_uniqueid', $subscription_id)
            ->first()) {
            Log::error("unable to find the tenants subscription (subscription_uniqueid: $subscription_id)", ['process' => '[checkout-payment]', config('stripe-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            return false;
        }

        //get the package
        if (!$package = \App\Models\Landlord\Package::on('landlord')
            ->Where('package_id', $subscription->subscription_package_id)
            ->first()) {
            Log::error("unable to find the package for this subscrition (subscription_uniqueid: $subscription_id) - (package_id: " . $subscription->subscription_package_id . ")", ['process' => '[checkout-payment]', config('stripe-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            return false;
        }

        //get landlord settings
        if (!$settings = \App\Models\Landlord\Settings::on('landlord')
            ->Where('settings_id', 'default')
            ->first()) {
            Log::error("unable to get the landlord settings table", ['process' => '[checkout-payment]', config('stripe-paynow-button'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            return false;
        }

        return [
            'subscription' => $subscription,
            'package' => $package,
            'settings' => $settings,
        ];

    }

    /**
     * The customer has paid and been redirected to the [thankyou] url. This method will do all of the following tasks
     *
     *  - validate the checkout session
     *  - update the customers subscription the the payment gateway's new [subscription id]
     *  - temporarily mark the subscription is 'active' in the customers database and the landlord. (final updates will be via Webhooks and cronjobs)
     *  - temporarily add a 'next_renewal_date' (final updates will be via Webhooks and cronjobs)
     *  - mark the checkout session as completed
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function completeCheckoutSession($data) {

        Log::info("completing a checkout session - started", ['process' => '[checkout-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //is the subsciption active at the gateway
        if ($data['gateway_subscription_status'] != 'completed') {
            Log::error("completing a checkout session failed - the subscription is not marked as completed (or simialar) at the payment gateway", ['process' => '[paypal-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validate checkout session
        if (!$payment_session = \App\Models\Landlord\PaymentSession::on('landlord')->Where('session_gateway_ref', $data['checkout_session_id'])->first()) {
            Log::error("completing a checkout session failed - checkout session could not be found in the database", ['process' => '[paypal-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //is the checkout session still pending (avoid spoofing)
        if ($payment_session->session_status != 'pending') {
            Log::info("checkout session has already been competed previously - will now exit", ['process' => '[checkout-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return true;
        }

        //validate subscription session
        if (!$subscription = \App\Models\Landlord\Subscription::on('landlord')->Where('subscription_id', $payment_session->session_subscription_id)->first()) {
            Log::error("completing a checkout session failed - subscription could not be found in the database", ['process' => '[checkout-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //update subscription with the paypal subscription id
        $subscription->subscription_gateway_id = $data['gateway_subscription_id'];
        $subscription->subscription_gateway_name = $data['gateway_name'];
        $subscription->subscription_date_renewed = now();
        $subscription->subscription_status = 'active';
        $subscription->subscription_checkout_reference = $data['subscription_checkout_reference'] ?? '';
        $subscription->subscription_checkout_reference_2 = $data['subscription_checkout_reference_2'] ?? '';
        $subscription->subscription_checkout_reference_3 = $data['subscription_checkout_reference_3'] ?? '';
        $subscription->subscription_checkout_reference_4 = $data['subscription_checkout_reference_4'] ?? '';
        $subscription->subscription_checkout_reference_5 = $data['subscription_checkout_reference_5'] ?? '';
        $subscription->subscription_checkout_payload = $data['subscription_checkout_payload'] ?? '';
        $subscription->subscription_date_next_renewal = ($subscription->subscription_gateway_billing_cycle == 'monthly') ? \Carbon\Carbon::now()->addMonths(1)->format('Y-m-d') : \Carbon\Carbon::now()->addYears(1)->format('Y-m-d');
        $subscription->save();

        /** ----------------------------------------------------------------------
         * update the tenants instance in their DB as [active]
         * ---------------------------------------------------------------------*/
        Tenant::forgetCurrent();
        if ($tenant = Tenant::Where('tenant_id', $subscription->subscription_customerid)->first()) {
            try {
                //swicth to this tenants DB
                $tenant->makeCurrent();

                //update customers account as cancelled
                \App\Models\Landlord\Settings::on('tenant')->where('settings_id', 1)
                    ->update([
                        'settings_saas_status' => 'active',
                    ]);

                //forget tenant
                Tenant::forgetCurrent();

            } catch (Exception $e) {
                Log::error("completing a checkout session failed - error trying to set the tenants database as [active] - error: " . $e->getMessage(), ['process' => '[checkout-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }
        } else {
            Log::error("completing a checkout session failed - error trying to set the tenants database as [active] - tenant could not be found", ['process' => '[checkout-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //mark tenant account has paid
        \App\Models\Landlord\Tenant::on('landlord')->where('tenant_id', $subscription->subscription_customerid)
            ->update(['tenant_status' => 'active']);

        //update session as processed
        \App\Models\Landlord\PaymentSession::on('landlord')->Where('session_gateway_ref', $data['checkout_session_id'])
            ->update(['session_status' => 'completed']);

        Log::info("completing a checkout session - completed", ['process' => '[checkout-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //all ok
        return true;
    }

}