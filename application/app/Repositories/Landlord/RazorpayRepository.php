<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for razorpay payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;

class RazorpayRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /** ---------------------------------------------------------------------------------------------------
     * [subscription payment]
     * Start the process for a subscription razorpay payment
     *     - validate the gatewayplans. if none are set,create them (both monthly and yearly
     *     - validate the customer exists at the gateway. if not, create one
     *     - generate a payment session_id for the checkout
     *
     * @return mixed razorpay customer object or bool (false)
     * ---------------------------------------------------------------------------------------------------*/
    public function initiateSubscriptionPayment($data = []) {

        Log::info("initiating a subscription payment session at razorpay - started", ['process' => '[razorpay-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validate
        if (!is_array($data)) {
            Log::error("initiating a subscription payment session at razorpay failed - invalid paymment payload data", ['process' => '[razorpay-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //validate gateway plans and return validated [package]
        if ($package = $this->validateGatewayPlans($data)) {
            //set the validated plan_id
            $data['plan_id'] = ($data['billing_cycle'] == 'monthly') ? $package->package_gateway_razorpay_plan_monthly : $package->package_gateway_razorpay_plan_yearly;
            $data['plan_amount'] = ($data['billing_cycle'] == 'monthly') ? $package->package_amount_monthly * 100 : $package->package_amount_yearly * 100;
        } else {
            Log::error("razorpay subscription plans could not be validated for this package", ['process' => '[razorpay-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //get the customer from razorpay
        if ($customer = $this->getCustomer($data)) {
            //set the validated customer id
            $data['customer_id'] = $customer['id'];
        } else {
            Log::error("initiating a subscription payment session at razorpay failed - unable to retrieve the customer", ['process' => '[razorpay-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        if (!$session = $this->createSubscriptionCheckoutSession($data)) {
            Log::error("initiating a subscription payment session at razorpay failed - unable to create a checkout session", ['process' => '[razorpay-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save session id in sessions database
        $payment_session = new \App\Models\Landlord\PaymentSession();
        $payment_session->setConnection('landlord');
        $payment_session->session_creatorid = auth()->id();
        $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $payment_session->session_creator_email = auth()->user()->email;
        $payment_session->session_gateway_name = 'razorpay';
        $payment_session->session_gateway_ref = $session['gateway_subscription_id'];
        $payment_session->session_gateway_ref_2 = null;
        $payment_session->session_amount = $session['amount'];
        $payment_session->session_invoices = null;
        $payment_session->session_subscription_id = $data['subscription_id'];
        $payment_session->session_payload = json_encode($session);
        $payment_session->save();

        //temp
        return $session;

    }

    /**
     * Check if the plans already at the payment gateway
     *
     *  - If yes, validate it with the payment gateway
     *  - If no, create a new plan at the gateay
     *
     * @param  array  $data payload of the payment data
     * @return bool
     */
    public function validateGatewayPlans($data) {

        Log::info("validating the package's plans at razorpay", ['process' => '[razorpay-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //get the package
        $package = $data['package'];

        //[monthly plan] - the package does not have a gateway plan - attempt to create it
        if ($package->package_gateway_razorpay_plan_monthly == '') {
            if ($plan = $this->createPlan([
                'settings_razorpay_api_key' => $data['settings_razorpay_api_key'],
                'settings_razorpay_secret_key' => $data['settings_razorpay_secret_key'],
                'amount' => $package->package_amount_monthly * 100,
                'currency' => $data['currency'],
                'cycle' => 'monthly',
                'name' => $package->package_name,
            ])) {
                //update the package
                $package->package_gateway_razorpay_plan_monthly = $plan['id'];
                $package->save();
            }
        }

        //there is no yearly plan - create one
        if ($package->package_gateway_razorpay_plan_yearly == '') {
            if ($plan = $this->createPlan([
                'settings_razorpay_api_key' => $data['settings_razorpay_api_key'],
                'settings_razorpay_secret_key' => $data['settings_razorpay_secret_key'],
                'amount' => $package->package_amount_yearly * 100,
                'currency' => $data['currency'],
                'cycle' => 'yearly',
                'name' => $package->package_name,
            ])) {
                //update package
                $package->package_gateway_razorpay_plan_yearly = $plan['id'];
                $package->save();
            }
        }

        //return the validate package
        return $package;
    }

    /**
     * create a new plan
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function createPlan($data) {

        Log::info("creating a plan at razorpay - started", ['process' => '[razorpay-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //Create M
        try {
            $response = Http::withBasicAuth($data['settings_razorpay_api_key'], $data['settings_razorpay_secret_key'])
                ->post('https://api.razorpay.com/v1/plans', [
                    'period' => $data['cycle'],
                    'interval' => 1,
                    'item' => [
                        'name' => $data['name'],
                        'description' => $data['name'],
                        'amount' => $data['amount'],
                        'currency' => strtoupper($data['currency']),
                        'unit' => $data['name'],
                    ],
                ]);

            if ($response->successful()) {
                $plan = $response->json();
                Log::info("creating a plan at razorpay - completed", ['process' => '[razorpay-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'plan' => $plan]);
                return $plan;
            } else {
                $error = $response->body();
                Log::error("creating a plan failed - error: $error", ['process' => '[razorpay-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }
        } catch (Exception $e) {
            Log::error("creating a plan failed - error: " . $e->getMessage(), ['process' => '[razorpay-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }
    }

    /** --------------------------------------------------------------------------------------------
     * [get customer]
     * - if this user has a razorpay id in our database, attempt to get the user from razorpay
     * - else, create a new user in razorpay
     * -------------------------------------------------------------------------------------------*/
    public function getCustomer($data = []) {

        Log::info("fetching a customer from razorpay - started", ['process' => '[get-razorpay-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        $tenant_id = $data['tenant_id'];

        //get the tenent
        if (!$tenant = \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', $tenant_id)->first()) {
            Log::error("getting a customer from razorpay failed - the tenant could not be found in the landlord db", ['process' => '[get-razorpay-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        $tenant_razorpay_customer_id = $tenant->tenant_razorpay_customer_id;

        //check if the current user is a razorpay customer.
        if ($tenant->tenant_razorpay_customer_id != '') {
            //get the customer
            try {
                $response = Http::withBasicAuth($data['settings_razorpay_api_key'], $data['settings_razorpay_secret_key'])
                    ->get("https://api.razorpay.com/v1/customers/{$tenant_razorpay_customer_id}");
                $customer = $response->json();
                Log::info("customer was found at razorpay", ['process' => '[get-razorpay-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'customer' => $customer]);
                return $customer;
            } catch (exception $e) {
                Log::info("this tenant has a razorpay customer id ($tenant_razorpay_customer_id), but the user was not found in razorpay - will now create a new user", ['process' => '[get-razorpay-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //get the current logged in user (i.e. the paying user)
        $user = \App\Models\User::Where('id', auth()->id())->first();

        Log::info("the customer ($tenant_razorpay_customer_id) was not found at razorpay - will now create one", ['process' => '[razorpay-validate-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //create a new customer in razorpay
        try {
            $response = Http::withBasicAuth($data['settings_razorpay_api_key'], $data['settings_razorpay_secret_key'])
                ->post('https://api.razorpay.com/v1/customers', [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'contact' => '',
                ])->throw();

            $customer = $response->json();

            //update tenant profile with razorpay id
            $tenant->tenant_razorpay_customer_id = $customer['id'];
            $tenant->save();

            Log::info("creating a new customer (" . $customer['id'] . ") at razorpay - completed", ['process' => '[get-razorpay-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //return
            return $customer;
        } catch (exception $e) {
            Log::error("error creating a new customer at razorpay - error: " . $e->getMessage(), ['process' => '[get-razorpay-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        Log::error("fetching a customer from razorpay - failed", ['process' => '[get-razorpay-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //return
        return false;

    }

    /**
     * create a checkout session at razorpay
     * @param array $data payload
     * @return mixed error message or true
     */
    public function createSubscriptionCheckoutSession($data = []) {

        Log::info("creating a checkout session at razorpay - started", ['process' => '[razorpay-create-checkout-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //create our own checkkout session identifier (will be found in razorpay metadata)
        $our_checkout_session_id = str_unique();

        //Create a new checkout session
        try {

            $response = Http::withBasicAuth($data['settings_razorpay_api_key'], $data['settings_razorpay_secret_key'])
                ->post('https://api.razorpay.com/v1/subscriptions', [
                    'plan_id' => $data['plan_id'],
                    'total_count' => 100, //a total number of renewal cycles is required by Razorpay. Have set to a high number.
                    'quantity' => 1,
                    'notes' => [
                        'fee_bearer' => 'platform',
                    ],
                ])->throw();

            $subscription = $response->json();
            Log::info("creating a checkout session at razorpay - completed", ['process' => '[razorpay-create-checkout-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'checkout_session' => $subscription['id']]);
            return [
                'gateway_subscription_id' => $subscription['id'],
                'amount' => $data['plan_amount'],
                'currency' => strtoupper($data['currency']),
                'plan_id' => $data['plan_id'],
            ];
        } catch (Exception $e) {
            Log::error("creating a checkout session at razorpay  failed - error: " . $e->getMessage(), ['process' => '[razorpay-create-checkout-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

    }

    /**
     * get the subscription from the gateway and check if it is marked as paid "completed" status
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function getMatchingSubscription($data = []) {

        Log::info("creating a checkout session at razorpay - started", ['process' => '[razorpay-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        try {
            $response = Http::withBasicAuth($data['settings_razorpay_api_key'], $data['settings_razorpay_secret_key'])
                ->get("https://api.razorpay.com/v1/subscriptions/" . $data['checkout_session_id']);

            if ($response->successful()) {
                $subscription = $response->json();

                // Check if the subscription status is 'completed'
                if ($subscription['status'] === 'active') {
                    Log::info("the matching was found and it is marked as paid", ['process' => '[razorpay-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription' => $subscription]);
                    return true;
                } else {
                    Log::error("the subscription was found but it is not marked as paid", ['process' => '[razorpay-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription' => $subscription]);
                    return false;
                }
            } else {
                Log::error("the subscription could not be found", ['process' => '[razorpay-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::error("subscrition could not be fitched - error: $error", ['process' => '[razorpay-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }
}
