<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @fooo    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaypalRepository {

    protected $paypal_client_id;
    protected $paypal_secret_key;
    protected $paypal_mode;
    protected $paypal_endpoint;
    protected $paypal_url;
    protected $paypal_access_token;

    /**
     * Inject dependecies
     */
    public function __construct() {

        //get settings
        $settings = $settings = \App\Models\Landlord\Settings::on('landlord')->Where('settings_id', 'default')->first();

        // set the env mode
        if ($settings->settings_paypal_mode == 'live') {
            $this->paypal_client_id = $settings->settings_paypal_live_client_id;
            $this->paypal_secret_key = $settings->settings_paypal_live_secret_key;
            $this->paypal_mode = 'live';
            $this->paypal_endpoint = 'https://api-m.paypal.com';
            $this->paypal_url = 'https://paypal.com';
        } else {
            $this->paypal_client_id = $settings->settings_paypal_sandbox_client_id;
            $this->paypal_secret_key = $settings->settings_paypal_sandbox_secret_key;
            $this->paypal_mode = 'sandbox';
            $this->paypal_endpoint = 'https://api-m.sandbox.paypal.com';
            $this->paypal_url = 'https://sandbox.paypal.com';
        }
    }

    /**
     * get an access token from Paypal to use with REST requests
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAccessToken() {

        Log::info("getting authentication token - started", ['process' => '[paypal-get-token]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //request an access token
        try {

            $response = Http::asForm()
                ->withBasicAuth($this->paypal_client_id, $this->paypal_secret_key)
                ->post($this->paypal_endpoint . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            //process response & return the token
            if ($response->successful()) {
                $payload = $response->json();
                if (isset($payload['access_token'])) {
                    Log::info("getting authentication token - completed", ['process' => '[paypal-get-token]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'access_token' => $payload['access_token']]);
                    return $payload['access_token'];
                }
            }

        } catch (exception $e) {
            Log::error("getting authentication token failed: " . $e->getMessage(), ['process' => '[paypal-get-token]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::error("getting authentication token failed", ['process' => '[paypal-get-token]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /** ---------------------------------------------------------------------------------------------------
     * [subscription payment]
     * Start the process for a subscription payment
     *     - sanity check as follows
     *          - check if default product exists (if not, create it)
     *          - check if the plan exists (if not, create it)
     *
     * @return mixed
     * ---------------------------------------------------------------------------------------------------*/
    public function initiateSubscriptionPayment($data = []) {

        Log::info("initiating a subscription payment session at paypal - started", ['process' => '[paypal-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validate the subscription plan, at paypal
        if ($payload = $this->validateGatewayPlans($data)) {

            //the original subscriptio
            $subscription = $data['subscription'];

            //create a session that we will use to process the payment
            $payment_session = new \App\Models\Landlord\PaymentSession();
            $payment_session->setConnection('landlord');
            $payment_session->session_creatorid = auth()->id();
            $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
            $payment_session->session_creator_email = auth()->user()->email;
            $payment_session->session_gateway_name = 'paypal';
            $payment_session->session_gateway_ref = $payload['checkout_session_id'];
            $payment_session->session_amount = $subscription->subscription_amount;
            $payment_session->session_invoices = null;
            $payment_session->session_subscription_id = $subscription->subscription_id;
            $payment_session->session_payload = $payload['plan_id'];
            $payment_session->save();

            Log::info("initiating a subscription payment session at paypal - completed", ['process' => '[paypal-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            return $payload;
        }

        return false;
    }

    /**
     * Ensure that the payment plans exist at the payment gateway and that they are valid
     *
     *    - Check if we have the default product (if not, create it)
     *    - check if the specified plan exists (if not, create it
     *
     * @param  array  $data payload of the payment data
     * @return bool
     */
    public function validateGatewayPlans($data) {

        Log::info("validating the package plan at paypal", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //payment data
        $package = $data['package'];
        $settings = $data['settings'];
        $subscription = $data['subscription'];
        $billing_cycle = $subscription->subscription_gateway_billing_cycle;
        $plan_id = ($billing_cycle == 'monthly') ? $package->package_gateway_paypal_plan_monthly : $package->package_gateway_paypal_plan_yearly;

        //defaults
        $create_new_product = false;
        $create_new_plan = false;

        // [1][a] - [default product]
        if ($settings->settings_paypal_subscription_product_id) {
            Log::info("there is a default product. will validate it by fetching it from paypal", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //get the product state
            $product = $this->getProduct($settings->settings_paypal_subscription_product_id);

            //[not-found] - product was not found at paypal - will reset db create a new one
            if ($product['state'] == 'product-does-not-exist') {
                Log::info("the deafult product in the db could not be found at payapl - will update db and create a new product", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                $settings->settings_paypal_subscription_product_id = '';
                $settings->save();
                //create new
                $create_new_product = true;
            }
            //[general error]
            if ($product['state'] == 'general-error') {
                Log::error("an api or network error was encountered and cannot confirm if the product exists or not - will now exit", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
            //[general error]
            if ($product['state'] == 'product-exists') {
                Log::info("the default product was found at paypal", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[1][b]we do not have a product create it
        if (!$settings->settings_paypal_subscription_product_id || $create_new_product) {
            Log::info("there is no default product in the db. will now create one at paypal", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //create product
            if ($product_id = $this->createProduct([
                'product_name' => $settings->settings_gateways_default_product_name,
                'product_description' => $settings->settings_gateways_default_product_description,
                'type' => 'SERVICE',
                'category' => 'SOFTWARE',
            ])) {
                Log::info("product was created at paypal", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                //save product id to the database
                $settings->settings_paypal_subscription_product_id = $product_id;
                $settings->save();
            } else {
                return false;
                Log::error("default product could not be created", ['process' => '[paypal-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[2][a] - get the subscription plan
        if ($plan_id) {
            Log::info("there is a plan in the db. will validate it by fetching it from paypal", ['process' => '[paypal-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            //get the plan state
            $plan = $this->getPlan($plan_id);

            //[not-found] - plan was not found at paypal - will reset db create a new one
            if ($plan['state'] == 'plan-does-not-exist') {
                Log::info("the plan in the db could not be found at payapl - will update db and create a new plan", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                if ($billing_cycle == 'monthly') {
                    $package->package_gateway_paypal_plan_monthly = '';
                } else {
                    $package->package_gateway_paypal_plan_yearly = '';
                }
                $package->save();
                //create new
                $create_new_plan = true;
            }
            //[general error]
            if ($plan['state'] == 'general-error') {
                Log::info("an api or network error was encountered and cannot confirm if the plan exists or not - will now exit", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
            //[general error]
            if ($plan['state'] == 'plan-exists') {
                Log::info("the plan was found at paypal", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[2][b] - create the plan
        if (!$plan_id || $create_new_plan) {
            Log::info("there is no plan id in the db. will now create a new plan at paypal", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //create the plan
            if ($plan_id = $this->createPlan([
                'product_id' => $settings->settings_paypal_subscription_product_id,
                'plan_name' => $package->package_name,
                'plan_description' => __('lang.crm_subscription'),
                'plan_cycle' => ($subscription->subscription_gateway_billing_cycle == 'monthly') ? 'MONTH' : 'YEAR',
                'plan_amount' => ($subscription->subscription_gateway_billing_cycle == 'monthly') ? $package->package_amount_monthly : $package->package_amount_yearly,
                'plan_currency' => strtoupper($settings->settings_system_currency_code),
            ])) {
                if ($billing_cycle == 'monthly') {
                    $package->package_gateway_paypal_plan_monthly = $plan_id;
                } else {
                    $package->package_gateway_paypal_plan_yearly = $plan_id;
                }
                $package->save();
            } else {
                Log::error("the plan could not be created at paypal - will now exit", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        }

        //all is ok - return the needed data
        return [
            'plan_id' => $plan_id,
            'client_id' => $this->paypal_client_id,
            'client_id' => $this->paypal_client_id,
            'checkout_session_id' => random_string(15),
        ];
    }

    /**
     * create the default produc that will house all the pricing plans
     *
     * @param  array  $data information about the product
     * @return string product id
     */
    public function createProduct($data = []) {

        Log::info("creating a product - started", ['process' => '[paypal-create-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get access token
        if (!$token = $this->getAccessToken()) {
            Log::info("creating a product - failed - unable to get an access token", ['process' => '[paypal-create-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //create the product
        try {

            $response = Http::withBody('{
                "name": "' . $data['product_name'] . '",
                "description": "' . $data['product_name'] . '",
                "type": "SERVICE",
                "category": "SOFTWARE"
              }', 'application/json')
                ->withToken($token)
                ->withHeaders([
                    'PayPal-Request-Id' => 'PRODUCT-' . time(),
                ])
                ->post($this->paypal_endpoint . '/v1/catalogs/products');

            //process response & return the product id
            if ($response->successful()) {

                //get the json response
                $payload = $response->json();

                //check if we have the product id
                if (isset($payload['id'])) {
                    Log::info("creating a product (" . $payload['id'] . ") - completed", ['process' => '[paypal-create-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    return $payload['id'];
                }

                //an error
                Log::error("creating a product failed: the expected response was not received from paypal", ['process' => '[paypal-create-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;

            }

            //an error
            Log::error("creating a product failed: " . $response->body(), ['process' => '[paypal-create-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;

        } catch (exception $e) {
            Log::error("creating a product failed: " . $e->getMessage(), ['process' => '[paypal-create-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::error("creating a product failed", ['process' => '[paypal-create-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /**
     * get the default product fro paypal
     *
     * @param  string  $product_id the paypal product id
     * @return json the product
     */
    public function getProduct($product_id = '') {

        //validation
        if ($product_id == '') {
            Log::error("getting a product - failed - [product_id] was not provided", ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::info("getting a product ($product_id) - started", ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get access token
        if (!$token = $this->getAccessToken()) {
            Log::info("getting a product ($product_id) - failed - unable to get an access token", ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //get the product
        try {

            $response = Http::withToken($token)
                ->get($this->paypal_endpoint . "/v1/catalogs/products/$product_id");

            //http status
            $status = $response->status();

            //get the json response
            $payload = $response->json();

            //process response & return the product id
            if ($response->successful()) {

                //check if we have the product id
                if (isset($payload['id'])) {
                    Log::info("getting a product ($product_id) - completed", ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    return ['state' => 'product-exists', 'product_id' => $payload['id']];
                }

                //an error
                Log::error("getting a product ($product_id) failed: the expected response (product: ID) was not received from paypal", ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return ['state' => 'general-error'];
            }

            //product does not exists
            if ($response->status() == '404') {
                Log::info("the product ($product_id) could not be found at paypal", ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return ['state' => 'product-does-not-exist'];
            }

            //some other general error
            Log::error("api error - getting a product ($product_id) failed: " . $response->body(), ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return ['state' => 'general-error'];

        } catch (exception $e) {
            Log::error("api error - getting a product ($product_id) failed: " . $e->getMessage(), ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return ['state' => 'general-error'];
        }

        Log::error("api error - getting a product ($product_id) failed", ['process' => '[paypal-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return ['state' => 'general-error'];
    }

    /**
     * get the plan from paypal
     *
     * @param  string  $plan_id the paypal plan id
     * @return json the plan
     */
    public function getPlan($plan_id = '') {

        //validation
        if ($plan_id == '') {
            Log::error("getting a plan - failed - [plan_id] was not provided", ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::info("getting a plan ($plan_id) - started", ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get access token
        if (!$token = $this->getAccessToken()) {
            Log::info("get a plan ($plan_id) - failed - unable to get an access token", ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //get the plan
        try {

            $response = Http::withToken($token)
                ->get($this->paypal_endpoint . "/v1/billing/plans/$plan_id");

            //http status
            $status = $response->status();

            //get the json response
            $payload = $response->json();

            //process response & return the plan id
            if ($response->successful()) {

                //check if we have the plan id
                if (isset($payload['id'])) {
                    Log::info("getting a plan ($plan_id) - completed", ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    return [
                        'state' => 'plan-exists',
                        'plan_id' => $payload['id'],
                        'plan_subscribers' => $payload['id'],
                        'plan_status' => $payload['status'],
                    ];
                }

                //an error
                Log::error("getting a plan ($plan_id) failed: the expected response (plan: ID) was not received from paypal", ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return ['state' => 'general-error'];
            }

            //plan does not exists
            if ($response->status() == '404') {
                Log::info("the plan ($plan_id) could not be found at paypal", ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return ['state' => 'plan-does-not-exist'];
            }

            //some other general error
            Log::error("api error - getting a plan ($plan_id) failed: " . $response->body(), ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return ['state' => 'general-error'];

        } catch (exception $e) {
            Log::error("api error - getting a plan ($plan_id) failed: " . $e->getMessage(), ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return ['state' => 'general-error'];
        }

        Log::error("api error - getting a plan ($plan_id) failed", ['process' => '[paypal-get-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return ['state' => 'general-error'];
    }

    /**
     * create the a new plan at paypal
     *
     * @param  array  $data information about the plan
     * @return string plan id
     */
    public function createPlan($data = []) {

        Log::info("creating a plan - started", ['process' => '[paypal-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get access token
        if (!$token = $this->getAccessToken()) {
            Log::info("creating a plan - failed - unable to get an access token", ['process' => '[paypal-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //create the plan
        try {

            $response = Http::withBody('{
                "product_id": "' . $data['product_id'] . '",
                "name": "' . $data['plan_name'] . '",
                "description": "' . $data['plan_description'] . '",
                "status": "ACTIVE",
                "billing_cycles": [
                  {
                    "frequency": {
                      "interval_unit": "' . $data['plan_cycle'] . '",
                      "interval_count": 1
                    },
                    "tenure_type": "REGULAR",
                    "sequence": 1,
                    "total_cycles": 0,
                    "pricing_scheme": {
                      "fixed_price": {
                        "value": "' . $data['plan_amount'] . '",
                        "currency_code": "' . $data['plan_currency'] . '"
                      }
                    }
                  }
                ],
                "payment_preferences": {
                  "auto_bill_outstanding": true,
                  "payment_failure_threshold": 3
                }
              }', 'application/json')
                ->withToken($token)
                ->withHeaders([
                    'PayPal-Request-Id' => 'PLAN-' . time(),
                ])
                ->post($this->paypal_endpoint . '/v1/billing/plans');

            //process response & return the plan id
            if ($response->successful()) {

                //get the json response
                $payload = $response->json();

                //check if we have the plan id
                if (isset($payload['id'])) {
                    Log::info("creating a plan (" . $payload['id'] . ")- completed", ['process' => '[paypal-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    return $payload['id'];
                }

                //an error
                Log::error("creating a plan failed: the expected response was not received from paypal", ['process' => '[paypal-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }

            //an error
            Log::error("creating a plan failed: " . $response->body(), ['process' => '[paypal-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;

        } catch (exception $e) {
            Log::error("creating a plan failed: " . $e->getMessage(), ['process' => '[paypal-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::error("creating a plan failed", ['process' => '[paypal-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /**
     * get the subscription from paypal
     *
     * @param  string  $plan_id the paypal plan id
     * @return json the plan
     */
    public function getSubscription($subscription_id = '') {

        //validation
        if ($subscription_id == '') {
            Log::error("getting a subscription - failed - [subscription_id] was not provided", ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::info("getting a subscription ($subscription_id) - started", ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get access token
        if (!$token = $this->getAccessToken()) {
            Log::info("getting a subscription ($subscription_id) - failed - unable to get an access token", ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //get the subscription
        try {

            $response = Http::withToken($token)
                ->get($this->paypal_endpoint . "/v1/billing/subscriptions/$subscription_id");

            //http status
            $status = $response->status();

            //get the json response
            $payload = $response->json();

            //process response & return the subscription id
            if ($response->successful()) {

                //check if we have the subscription id
                if (isset($payload['id'])) {
                    Log::info("getting a subscription ($subscription_id) - completed", ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    //retunr the subscription array
                    return $payload;
                }

                //an error
                Log::error("getting a subscription ($subscription_id) failed: the expected response (subscription: ID) was not received from paypal", ['process' => '[paypal-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }

            //subscription does not exists
            if ($response->status() == '404') {
                Log::info("the subscription ($subscription_id) could not be found at paypal", ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }

            //some other general error
            Log::error("api error - getting a subscription ($subscription_id) failed: " . $response->body(), ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;

        } catch (exception $e) {
            Log::error("api error - getting a subscription ($subscription_id) failed: " . $e->getMessage(), ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::error("api error - getting a subscription ($subscription_id) failed", ['process' => '[paypal-get-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /**
     * cancel a subscription at the payment gateway
     *
     * @param  string  $subscription_id the paypal subscription id
     * @return bool
     */
    public function cancelSubscription($subscription_id = '') {

        //validation
        if ($subscription_id == '') {
            Log::info("cancelling a subscription - failed - [subscription_id] was not provided", ['process' => '[paypal-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::info("cancelling a subscription ($subscription_id) - started", ['process' => '[paypal-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get access token
        if (!$token = $this->getAccessToken()) {
            Log::info("cancelling a subscription ($subscription_id) - failed - unable to get an access token", ['process' => '[paypal-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //get the product
        try {

            $response = Http::withToken($token)
                ->post($this->paypal_endpoint . "/v1/billing/subscriptions/$subscription_id/cancel",
                    [
                        'reason' => 'subscription is no longer required',
                    ]
                );

            //process response & return the product id
            if ($response->successful()) {
                Log::info("cancelling a subscription ($subscription_id) - completed", ['process' => '[paypal-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return true;
            }
            //error
            Log::error("cancelling subscription ($subscription_id) failed: " . $response->body(), ['process' => '[paypal-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (exception $e) {
            Log::error("cancelling subscription ($subscription_id) failed: " . $e->getMessage(), ['process' => '[paypal-cancel-subsccription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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

        Log::info("updating package ($package->package_name) plan [price] at paypal - started", ['process' => '[paypal-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validation - if this update process is necessary
        if (empty($data['product_id'])) {
            Log::info("the package does not have a paypal [product_id] - this update process is not needed - will now exit", ['process' => '[paypal-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return true;
        }

        //validation
        $required = ['product_id', 'plan_name', 'plan_cycle', 'plan_amount', 'plan_currency', 'plan_description'];
        foreach ($required as $key) {
            if (empty($data[$key])) {
                Log::info("updating a plan - failed - [$key] was not provided", ['process' => '[paypal-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        }

        Log::info("we will now create a new [plan] for the packge ($package->package_name) and [archiving] the old plan (" . $data['plan_id'] . ")", ['process' => '[paystack-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        /** -------------------------------------------------------------------------
         * (1) delete the old plan. this should work if it has no subscribers
         * -------------------------------------------------------------------------*/
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->post($this->paypal_endpoint . '/v1/billing/plans/' . $data['plan_id'] . '/deactivate');

            if ($response->successful()) {
                Log::info("archiving the old plan at paypal (" . $data['plan_id'] . ") - completed", ['process' => '[paypal-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            } else {
                Log::info("archiving the old plan at paypal - failed - will ignore - error: " . $response->body(), ['process' => '[paypal-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        } catch (Exception $e) {
            Log::info("archiving the old plan at paypal - failed - will ignore - error:" . $e->getMessage(), ['process' => '[paypal-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        /** -------------------------------------------------------------------------
         * (2) create the new plan and update the package with its id
         * -------------------------------------------------------------------------*/
        if ($plan_id = $this->createPlan([
            'product_id' => $data['product_id'],
            'plan_name' => $data['plan_name'],
            'plan_description' => $data['plan_description'],
            'plan_cycle' => $data['plan_cycle'],
            'plan_amount' => $data['plan_amount'],
            'plan_currency' => $data['plan_currency'],
            'plan_description' => $data['plan_description'],
        ])) {
            if ($data['plan_cycle'] == 'MONTH') {
                $package->package_gateway_paypal_plan_monthly = $plan_id;
            } else {
                $package->package_gateway_paypal_plan_yearly = $plan_id;
            }
            $package->save();

            Log::info("updating package ($package->package_name) with new price ($plan_id) at paypal - completed", ['process' => '[paypal-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return true;
        } else {
            Log::error("the plan could not be created at paypal - will now exit", ['process' => '[paypal-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }

}