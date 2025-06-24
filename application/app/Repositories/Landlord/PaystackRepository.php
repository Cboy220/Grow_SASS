<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /** ---------------------------------------------------------------------------------------------------
     * [subscription payment]
     * Start the process for a subscription paystack payment
     *     - validate the gatewayplans. if none are set,create them (both monthly and yearly
     *     - validate the customer exists at the gateway. if not, create one
     *     - generate a payment session_id for the checkout
     *
     * @return mixed paystack customer object or bool (false)
     * ---------------------------------------------------------------------------------------------------*/
    public function initiateSubscriptionPayment($data = []) {

        Log::info("initiating a subscription payment session at paystack - started", ['process' => '[paystack-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validate
        if (!is_array($data)) {
            Log::error("initiating a subscription payment session at paystacke failed - invalid paymment payload data", ['process' => '[paystack-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //validate gateway plans and return validated [package]
        if ($package = $this->validateGatewayPlans($data)) {
            //set the validated plan_id
            $data['plan_id'] = ($data['billing_cycle'] == 'monthly') ? $package->package_gateway_paystack_plan_monthly : $package->package_gateway_paystack_plan_yearly;
        } else {
            Log::error("paystack subscription plans could not be validated for this package", ['process' => '[paystack-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //get the validated plan data
        if (!$plan = $this->getPlan($data)) {
            Log::error("initiating a subscription payment session at paystacke failed - unable to get the plan", ['process' => '[paystack-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //get the customer from paystack
        if ($customer = $this->validateCustomer($data)) {
            $data['customer_email'] = $customer['customer_email'];
        } else {
            Log::error("initiating a subscription payment session at paystacke failed - unable to retrieve the customer", ['process' => '[paystack-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //create the checkout
        if ($response = $this->createSubscriptionCheckoutSession($data)) {
            $session = $response['checkout_session'];
            $our_checkout_session_id = $response['our_checkout_session_id'];
        } else {
            Log::error("initiating a subscription payment session at paystack failed - unable to create a payment session", ['process' => '[paystack-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //save session id in sessions database
        $payment_session = new \App\Models\Landlord\PaymentSession();
        $payment_session->setConnection('landlord');
        $payment_session->session_creatorid = auth()->id();
        $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $payment_session->session_creator_email = auth()->user()->email;
        $payment_session->session_gateway_name = 'paystack';
        $payment_session->session_gateway_ref = $our_checkout_session_id;
        $payment_session->session_gateway_ref_2 = $session->access_code;
        $payment_session->session_amount = ($data['billing_cycle'] == 'monthly') ? $package->package_amount_monthly : $package->package_amount_yearly;
        $payment_session->session_invoices = null;
        $payment_session->session_subscription_id = $data['subscription_id'];
        $payment_session->session_payload = json_encode($session);
        $payment_session->save();

        //temp
        return $session->access_code;

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

        Log::info("validating the package's plans at paystack", ['process' => '[paystack-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //get the package
        $package = $data['package'];

        //[monthly plan] - the package does not have a gateway plan - attempt to create it
        if ($package->package_gateway_paystack_plan_monthly == '') {
            if ($plan = $this->createPlan([
                'settings_paystack_secret_key' => $data['settings_paystack_secret_key'],
                'amount' => $package->package_amount_monthly * 100,
                'currency' => $data['currency'],
                'cycle' => 'monthly',
                'name' => $package->package_name,
            ])) {
                //update the package
                $package->package_gateway_paystack_plan_monthly = $plan->data->plan_code;
                $package->save();
            }
        }

        //there is no yearly plan - create one
        if ($package->package_gateway_paystack_plan_yearly == '') {
            if ($plan = $this->createPlan([
                'settings_paystack_secret_key' => $data['settings_paystack_secret_key'],
                'amount' => $package->package_amount_yearly * 100,
                'currency' => $data['currency'],
                'cycle' => 'annually',
                'name' => $package->package_name,
            ])) {
                //update package
                $package->package_gateway_paystack_plan_yearly = $plan->data->plan_code;
                $package->save();
            }
        }

        //return the validate package
        return $package;
    }

    /**
     * Check if the customer already exists in paystack
     *
     *  - If no, create a new customer at the gateay
     *
     * @param  array  $data payload of the payment data
     * @return bool
     */
    public function validateCustomer($data) {

        Log::info("validating the customer at paystack", ['process' => '[paystack-validate-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //get the tenant
        $tenant_id = $data['tenant_id'];

        //get the tenent
        if (!$tenant = \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', $tenant_id)->first()) {
            Log::error("validating the customer at paystack failed - the tenant could not be found in the landlord db", ['process' => '[paystack-validate-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $tenant_id]);
            return false;
        }

        //get the current logged in user (i.e. the paying user)
        $user = \App\Models\User::Where('id', auth()->id())->first();

        /** -------------------------------------------------------------------------
         * (1) check if the current user is a customer in paystack
         * we will search with their email address (rather the customer_code)
         * reasom is that we want to make sure we do not end us creating a new
         * customer with the same email address (paystack requires it to be unique)
         * -------------------------------------------------------------------------*/
        try {

            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $data['settings_paystack_secret_key'],
                'Accept' => 'application/json',
            ])->get('https://api.paystack.co/customer', [
                'email' => $user->email,
            ]);

            if ($response->successful()) {
                $payload = $response->body();
                $payload = json_decode($payload);
                if (isset($payload->data) && isset($payload->data[0])) {

                    //get the customer
                    $customer = $payload->data[0];

                    //update tenant profile with paystack id
                    $tenant->tenant_paystack_customer_id = $customer->customer_code;
                    $tenant->save();

                    Log::info("fetching existing customer from paystack (" . $customer->customer_code . ") - completed", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

                    //return
                    return [
                        'customer_code' => $customer->customer_code,
                        'customer_email' => $user->email,
                    ];
                }
            } else {
                $error = json_encode($response->body());
                Log::info("fetching a customer (" . $user->email . ") failed - error: $error", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }
        } catch (exception $e) {
            Log::error("fetching a customer (" . $user->email . ")  failed - error: " . $e->getMessage(), ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
        }

        //customer not found
        Log::info("customer is not in pastack (" . $user->email . ") - will now create add them to paystack", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        /** -------------------------------------------------------------------------
         * (2) create a new customer in paystack
         * -------------------------------------------------------------------------*/
        try {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $data['settings_paystack_secret_key'],
                'Accept' => 'application/json',
            ])->post('https://api.paystack.co/customer', [
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);

            $response_body = $response->body();
            $customer = json_decode($response_body);

            //update tenant profile with paystack id
            $tenant->tenant_paystack_customer_id = $customer->data->customer_code;
            $tenant->save();
            Log::info("getting customer from paystack (" . $customer->data->customer_code . ") - completed", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //return
            return [
                'customer_code' => $customer->data->customer_code,
                'customer_email' => $user->email,
            ];

        } catch (exception $e) {
            Log::error("error retrieving customer from paystack - error: " . $e->getMessage(), ['process' => '[get-paystack-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * create a new plan
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function createPlan($data) {

        Log::info("creating a plan at paystack - started", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //Create M
        try {
            $response = Http::withHeaders([
                "Authorization" => "Bearer " . $data['settings_paystack_secret_key'],
                "Content-Type" => "application/json",
            ])->post('https://api.paystack.co/plan', [
                "name" => $data['name'],
                "amount" => $data['amount'],
                "interval" => $data['cycle'],
                "currency" => strtoupper($data['currency']),
                "description" => $data['name'],
            ]);

            if ($response->successful()) {
                $response_body = $response->body();
                $plan = json_decode($response_body);
                Log::info("creating a plan at paystack - completed", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'plan' => $plan]);
                return $plan;
            } else {
                $error = json_encode($response->body());
                Log::error("creating a plan failed - error: $error", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }
        } catch (Exception $e) {
            Log::error("creating a plan failed - error: " . $e->getMessage(), ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }
    }

    /**
     * create a new plan
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function getPlan($data) {

        Log::info("getting a plan at paystack - started", ['process' => '[paystack-create-get]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //Create M
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $data['settings_paystack_secret_key'],
                'Content-Type' => 'application/json',
            ])->get('https://api.paystack.co/plan/' . $data['plan_id']);

            if ($response->successful()) {
                $response_body = $response->body();
                $plan = json_decode($response_body);
                Log::info("fetching a plan at paystack - completed", ['process' => '[paystack-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'plan' => $plan]);
                return $plan;
            } else {
                $error = json_encode($response->body());
                Log::error("fetching a plan (" . $data['plan_id'] . ") failed - error: $error", ['process' => '[paystack-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }
        } catch (Exception $e) {
            Log::error("fetching a plan (" . $data['plan_id'] . ")  failed - error: " . $e->getMessage(), ['process' => '[paystack-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }
    }

    /**
     * create a checkout session at paystack
     * @param array $data payload
     * @return mixed error message or true
     */
    public function createSubscriptionCheckoutSession($data = []) {

        Log::info("creating a checkout session at paystack - started", ['process' => '[paystack-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //create our own checkkout session identifier (will be found in paystack metadata)
        $our_checkout_session_id = str_unique();

        //Create a new checkout session
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $data['settings_paystack_secret_key'],
                'Accept' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $data['customer_email'],
                'plan' => $data['plan_id'],
                'quantity' => 1,
                'amount' => null,
                'callback_url' => url("app/settings/account/thankyou/paystack?checkout_session_id=$our_checkout_session_id"),
                'metadata' => [
                    'our_checkout_session_id' => $our_checkout_session_id,
                ],
            ]);

            if ($response->successful()) {
                $response_body = $response->body();
                $checkout_session = json_decode($response_body);
                Log::info("creating a checkout session at paystack - completed", ['process' => '[paystack-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'plan' => $checkout_session]);
                return [
                    'checkout_session' => $checkout_session->data,
                    'our_checkout_session_id' => $our_checkout_session_id,
                ];
            } else {
                $error = json_encode($response->body());
                Log::error("creating a checkout session at paystack failed - error: $error", ['process' => '[paystack-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }

        } catch (Exception $e) {
            Log::error("creating a checkout session at paystack  failed - error: " . $e->getMessage(), ['process' => '[paystack-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

    }

    /**
     * Paystack does not include the subscription data within the 'charge.success' event. This means when payments come in, we
     * have no way to directly link them to a subscription. The workflow in this method will attempt to find/match the subscription.
     * It will achieve this as follows:
     *
     *    - Get the payment from paystack using the transaction id
     *    - Fetch all the subcription from paystack for the customer who made this payment (using the customer id from the payment)
     *    - Filter the returned subscription and find the needle in the haystack by using these filters (as also found in the payment)
     *                - plan_code
     *                - authorization_code
     *
     * @param array $data the payload which contains the subscription
     * @return array
     */
    public function getMatchingSubscription($data = '') {

        Log::info("fetching a matching subscription - started", ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validate
        if (!isset($data['settings_paystack_secret_key']) || !isset($data['transaction_id'])) {
            Log::error("fetching a matching subscription failed - require data is missing", ['process' => '[paystack-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        /** -------------------------------------------------------------------------
         * (1) fetch the payment from paystack using the transaction id
         * -------------------------------------------------------------------------*/
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $data['settings_paystack_secret_key'],
            ])->get('https://api.paystack.co/transaction/verify/' . $data['transaction_id']);

            if ($response->successful()) {
                $payload = $response->object()->data;
                $transaction = [
                    'customer_code' => $payload->customer->customer_code,
                    'customer_id' => $payload->customer->id,
                    'authorization_code' => $payload->authorization->authorization_code,
                    'plan_id' => $payload->plan_object->id,
                    'plan_code' => $payload->plan_object->plan_code,
                ];
                Log::info("transaction was found at paystack - will now fetch the subscription", ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);
            } else {
                $error = json_encode($response->body());
                Log::error("fetching a matching subscription failed - error: $error", ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }

        } catch (Exception $e) {
            Log::error("fetching a matching subscription failed - error: " . $e->getMessage(), ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        /** -------------------------------------------------------------------------
         * (2) find the subscription that matches this payment's data
         *     we will do this by looking for a transaction with the same
         *         - customer_id
         *         - plan_id
         *         - authorization_code
         * -------------------------------------------------------------------------*/
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $data['settings_paystack_secret_key'],
            ])->get('https://api.paystack.co/subscription', [
                'customer_id' => $transaction['customer_id'],
                'plan_id' => $transaction['plan_id'],
            ]);

            if ($response->successful()) {

                //list of subscriptions
                $subscriptions = $response->json()['data'];

                foreach ($subscriptions as $subscription) {

                    if ($subscription['authorization']['authorization_code'] == $transaction['authorization_code']) {
                        if ($subscription['plan']['plan_code'] == $transaction['plan_code']) {

                            //save subscription data
                            $transaction['subscription_id'] = $subscription['subscription_code'];
                            $transaction['next_payment_date'] = $subscription['next_payment_date'] ?? '';
                            $transaction['email_token'] = $subscription['email_token'] ?? ''; //needed in future for cancelling

                            Log::info("a subscription was found (" . $subscription['subscription_code'] . ") - will now exit", ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $transaction]);
                            return $transaction;
                        }
                    }

                }
                Log::error("fetching a matching subscription failed - a matching subscription could not be found at paystack", ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            } else {
                $error = json_encode($response->body());
                Log::error("completing a paystack checkout failed - error: $error", ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }
        } catch (Exception $e) {
            Log::error("completing a paystack checkout failed - error: " . $e->getMessage(), ['process' => '[paystack-get-matching-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }
    }

    /**
     * cancel a subscription at the payment gateway
     *
     * @param  string  $subscription_id the paypal subscription id
     * @return bool
     */
    public function cancelSubscription($data = []) {

        //validation
        if (!isset($data['subscription_id'])) {
            Log::info("cancelling a subscription - failed - [subscription_id] was not provided", ['process' => '[paystack-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::info("cancelling a subscription (" . $data['subscription_id'] . ") - started", ['process' => '[paystack-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //Create M
        try {

            $response = Http::withBody(
                '{ "code": "' . $data['subscription_id'] . '", "token": "' . $data['paystack_token'] . '" }',
                'application/json'
            )
                ->withToken($data['settings_paystack_secret_key'])
                ->post('https://api.paystack.co/subscription/disable');

            if ($response->successful()) {
                Log::info("cancelling a subscription (" . $data['subscription_id'] . ") - completed", ['process' => '[paystack-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return true;
            } else {
                $error = json_encode($response->body());
                $status = json_encode($response);

                Log::error("cancelling subscription (" . $data['subscription_id'] . ") failed: $status", ['process' => '[paystack-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        } catch (Exception $e) {
            Log::error("cancelling subscription (" . $data['subscription_id'] . ") failed: " . $e->getMessage(), ['process' => '[paystack-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     *  To update a Paypal plan price,we will do the following:
     *
     *  (1) Create a new plan with the new price
     *  (2) Archive the old plan
     *
     * @param  object  $package the package
     * @param  array  $data payload
     * @return bool
     */
    public function updatePlanPrice($package = '', $data = []) {

        Log::info("updating package ($package->package_name) plan [price] at paystack - started", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validation - if this update process is necessary
        if (empty($data['plan_id'])) {
            Log::info("the package does not have a paystack [product_id] - this update process is not needed - will now exit", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return true;
        }

        //validation
        $required = ['paystack_secret_key', 'plan_id', 'plan_name', 'plan_cycle', 'plan_amount', 'plan_currency'];
        foreach ($required as $key) {
            if (empty($data[$key])) {
                Log::info("updating a plan - failed - [$key] was not provided", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        }

        Log::info("we will now create a new [plan] for the packge ($package->package_name) and deleting the old plan [if its empty] (" . $data['plan_id'] . ")", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        /** -------------------------------------------------------------------------
         * (1) delete the old plan. this should work if it has no subscribers
         * -------------------------------------------------------------------------*/
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $data['paystack_secret_key'],
            ])->delete('https://api.paystack.co/plan/' . $data['plan_id']);

            if ($response->successful()) {
                Log::info("deleting the old plan at paystack (" . $data['plan_id'] . ") - completed", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            } else {
                Log::info("deleting the old plan at paystack - failed - will ignore", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        } catch (Exception $e) {
            Log::info("deleting the old plan at paystack - failed - will ignore - error:" . $e->getMessage(), ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        /** -------------------------------------------------------------------------
         * (2) create the new plan and update the package with its id
         * -------------------------------------------------------------------------*/
        if ($plan = $this->createPlan([
            'name' => $data['plan_name'],
            'amount' => $data['plan_amount'],
            'cycle' => $data['plan_cycle'],
            'currency' => $data['plan_currency'],
            'settings_paystack_secret_key' => $data['paystack_secret_key'],
        ])) {
            if ($data['plan_cycle'] == 'monthly') {
                $package->package_gateway_paystack_plan_monthly = $plan->data->plan_code;
            } else {
                $package->package_gateway_paystack_plan_yearly = $plan->data->plan_code;
            }
            $package->save();

            Log::info("updating package ($package->package_name) with new price (" . $plan->data->plan_code . ") at paystack - completed", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return true;
        } else {
            Log::error("the plan could not be created at paystack - will now exit", ['process' => '[paystack-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }

}
