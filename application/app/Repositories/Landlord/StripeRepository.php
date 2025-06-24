<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for payment gateways
 *
 *  [STRIPE API VERSION]
 *    - API version 2020-03-02
 *    - do not change this 'exact' API date. The expected webhooks data will not match.
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;
use Log;

class StripeRepository {

    public $api_version;

    /**
     * Inject dependecies
     */
    public function __construct() {

        //[IMPORTANT] do not change this 'exact' API version date. The expected webhooks data will not match.
        $this->api_version = '2020-03-02';

    }

    /**
     * set API keys and connect to Stripe
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function initialiseStripe($data = []) {

        //set stripe key
        try {
            \Stripe\Stripe::setApiKey($data['settings_stripe_secret_key']);
            //[IMPORTANT] do not change this 'exact' API date. The expected webhooks data will not match.
            \Stripe\Stripe::setApiVersion($this->api_version);
        } catch (Exception $e) {
            Log::critical("unable to connect to stripe - error: " . $e->getMessage(), ['process' => '[initialiseStripe]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        return true;
    }

    /**
     * get an array of all the prices for a product in Stripe
     * @return mixed error message or true
     */
    public function getProductPrices($data = []) {

        $product_id = $data['product_id'];

        Log::info("getting product prices ($product_id) from stripe - started", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validate
        if ($product_id == '') {
            Log::error('no product id was specifid', ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }

        //get all products
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $prices = $stripe->prices->all(['product' => $product_id]);
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("getting product prices ($product_id) from stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("getting product prices ($product_id) from stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        }

        //final check
        if (!is_object($prices)) {
            Log::error("unable to retrieve the products prices ($product_id) from stripe", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }

        //return array of the products
        Log::info("getting product prices ($product_id) from stripe - completed", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'prices' => $prices]);
        return $prices;
    }

    /**
     * get a specific product
     * @param string $product_di the unique stripe product id
     * @return mixed error message or true
     */
    public function getProduct($data = []) {

        $product_id = $data['product_id'];

        Log::info("getting product ($product_id) from stripe - started", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validate
        if ($product_id == '') {
            Log::error('no product id was specifid', ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }

        //get all products
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $product = $stripe->products->retrieve($product_id, []);
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("retrieving a product ($product_id) from stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("retrieving a product ($product_id) from stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        }

        //final check
        if (!is_object($product)) {
            Log::error("unable to retrieve the product ($product_id) from stripe", ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }

        //return array of the products
        Log::info("retrieving a poduct ($product_id) from stripe - completed", ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $product;
    }

    /**
     * get a specific price
     * @param string $price_id the unique stripe price id
     * @return mixed error message or true
     */
    public function getPrice($data = []) {

        $price_id = $data['price_id'];

        Log::info("retrieving a price ($price_id) from stripe - started", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validate
        if ($price_id == '') {
            Log::error('no stripe price_id was specifid', ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }
        //get all products
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $price = $stripe->prices->retrieve($price_id, []);
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("retrieving a price ($price_id) from stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("retrieving a price ($price_id) from stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error') . ' - (' . $e->getMessage() . ')');
            return false;
        }

        //final check
        if (!is_object($price)) {
            Log::error("unable to retrieve the price ($price_id) from stripe", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return array of the products
        Log::info("retrieving a price ($price_id) from stripe - completed", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $price;
    }

    /**
     * get a subscription from stripe
     * @param string $subscription_stripe_id the unique stripe id
     * @return mixed error message or true
     */
    public function getSubscription($data = []) {

        $subscription_stripe_id = $data['subscription_stripe_id'];

        Log::info("retrieving a subscription ($subscription_stripe_id) from stripe - started", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validation
        if ($subscription_stripe_id == '') {
            Log::error("retrieving a subscription ($subscription_stripe_id) from stripe failed - a subscription id was not provided", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $subscription = $stripe->subscriptions->retrieve(
                $subscription_stripe_id,
                []
            );
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("retrieving a subscription ($subscription_stripe_id) from stripe failed - unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("retrieving a subscription ($subscription_stripe_id) from stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //final check
        if (!is_object($subscription)) {
            Log::error("unable to retrieve the subscription ($subscription_stripe_id) from stripe", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the subscription
        Log::info("retrieving a subscription ($subscription_stripe_id) from stripe - completed", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $subscription;
    }

    /**
     * get a checkout session from stripe
     * @param string $checkout_session_id the unique stripe id
     * @return mixed error message or true
     */
    public function getCheckoutSession($data = []) {

        $checkout_session_id = $data['checkout_session_id'] ?? '';

        Log::info("retrieving a checkout session ($checkout_session_id) from stripe - started", ['process' => '[stripe-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validation
        if ($checkout_session_id == '') {
            Log::error("retrieving a checkout session ($checkout_session_id) from stripe failed - a checkout session id was not provided", ['process' => '[stripe-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $checkout_session = $stripe->checkout->sessions->retrieve(
                $checkout_session_id,
                []
            );
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("retrieving a checkout session ($checkout_session_id) from stripe failed - unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("retrieving a checkout session ($checkout_session_id) from stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //final check
        if (!is_object($checkout_session)) {
            Log::error("unable to retrieve the checkout session ($checkout_session_id) from stripe", ['process' => '[stripe-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the subscription
        Log::info("retrieving a checkout session ($checkout_session_id) from stripe - completed", ['process' => '[stripe-get-checkout session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $checkout_session;
    }

    /**
     * push a cancellation of a subscription to stripe
     * @param string $data the payload
     *               $data['subscription_stripe_id'] - required
     *               $data['settings_stripe_secret_key'] - required
     * @return mixed error message or true
     */
    public function cancelSubscription($data = []) {

        $subscription_stripe_id = $data['subscription_stripe_id'];

        Log::info("cancelling a subscription ($subscription_stripe_id) at stripe- started", ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validation
        if ($subscription_stripe_id == '') {
            Log::error("cancelling subscription ($subscription_stripe_id) failed - a subscription id was not provided", ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            request()->session()->flash('flash-error-message', __('lang.gateway_error_see_logs'));
            return false;
        }

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $stripe->subscriptions->cancel(
                $subscription_stripe_id,
                []
            );
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("cancelling stripe subscription ($subscription_stripe_id) failed  - unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("cancelling stripe subscription ($subscription_stripe_id) failed  - the server was unable to connect to api.stripe.com", ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\InvalidRequestException$e) {
            Log::error($e->getMessage(), ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the subscription
        Log::info("cancelling a subscription ($subscription_stripe_id) at stripe- completed", ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return true;
    }

    /**
     * create a new plan (together with parent 'product')
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function createPlan($data = []) {

        Log::info("creating a plan at stripe - started", ['process' => '[stripe-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $plan = $stripe->plans->create([
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'interval' => $data['cycle'],
                'product' => [
                    'name' => $data['name'],
                ],
            ]);
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("creating a plan at stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("creating a plan at stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\InvalidRequestException$e) {
            Log::error($e->getMessage(), ['process' => '[stripe-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error('creating a plan at stripe failed - error: ' . $e->getMessage(), ['process' => '[stripe-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the plan
        Log::info("creating a plan ($plan) at stripe - completed", ['process' => '[stripe-create-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $plan;

    }

    /**
     * add a price to an existing plan (product)
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function createPrice($data = []) {

        $product_id = $data['product_id'];

        Log::info("creating a new price at stripe - started", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //archive the price
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $price = $stripe->prices->create([
                'unit_amount' => $data['price_amount'],
                'currency' => $data['price_currency'],
                'recurring' => [
                    'interval' => $data['price_cycle'],
                ],
                'product' => $product_id,
            ]);
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("creating a new price at stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("creating a new price at stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\InvalidRequestException$e) {
            Log::error($e->getMessage(), ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the price
        Log::info("creating a new price at stripe ($price->id) - completed", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $price;

    }

    /**
     * archve a subscipiption plan (by arhiving the parent 'product')
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function archivePlan($data = []) {

        $plan_id = $data['plan_id'];

        Log::info("archiving a plan ($plan_id) at stripe - started", ['process' => '[stripe-archive-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $stripe->products->update(
                $data['plan_id'],
                [
                    'active' => false,
                ]
            );
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("archiving a plan at stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-archive-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("archiving a plan at stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-archive-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\InvalidRequestException$e) {
            Log::error($e->getMessage(), ['process' => '[stripe-archive-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-archive-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the plan
        Log::info("archiving a plan ($plan_id) at stripe - completed", ['process' => '[stripe-archive-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return true;

    }

    /**
     * archve a subscipiption plan's price. Stripe does now allow deleting prices, so this is the next best thing.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function archivePrice($data = []) {

        $price_id = $data['price_id'];

        Log::info("archiving a price ($price_id) at stripe - started", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //archive the price
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $stripe->prices->update(
                $data['price_id'],
                [
                    'active' => false,
                ]
            );
        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("archiving a price ($price_id) at stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("archiving a price ($price_id) at stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\InvalidRequestException$e) {
            Log::error($e->getMessage(), ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the price
        Log::info("archiving a price ($price_id) at stripe - completed", ['process' => '[stripe-archive-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return true;

    }

    /**
     * update the plan (product) name
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    public function updatePlanName($data = []) {

        $plan_id = $data['plan_id'];

        Log::info("updating plan name ($plan_id) at stripe - started", ['process' => '[stripe-update-product-name]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //update the plan
        try {
            $stripe = new \Stripe\StripeClient($data['settings_stripe_secret_key']);
            $stripe->products->update(
                $data['plan_id'],
                [
                    'name' => $data['plan_name'],
                ]
            );

        } catch (\Stripe\Exception\AuthenticationException$e) {
            Log::error("updating plan name ($plan_id) at stripe failed - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-update-product-name]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            Log::error("updating plan name ($plan_id) at stripe failed - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-update-product-name]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\InvalidRequestException$e) {
            Log::error($e->getMessage(), ['process' => '[stripe-update-product-name]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-update-product-name]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return the plan
        Log::info("updating plan name ($plan_id) at stripe - completed", ['process' => '[stripe-update-product-name]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return true;

    }

    /** ---------------------------------------------------------------------------------------------------
     * [subscription payment]
     * Start the process for a subscription stripe payment
     *     - generate a payment sessions id
     *     - subscription details like (amount) and (billing cycle) are taken from the Stripe product price
     *
     * @return mixed stripe customer object or bool (false)
     * ---------------------------------------------------------------------------------------------------*/
    public function initiateSubscriptionPayment($data = []) {

        Log::info("initiating a subscription payment session at stripe - started", ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //initialise stripe
        if (!$this->initialiseStripe($data)) {
            return false;
        }

        //validate
        if (!is_array($data)) {
            Log::error("initiating a subscription payment session at stripee failed - invalid paymment payload data", ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //validate gateway plans and return validated [package]
        if ($package = $this->validateGatewayPlans($data)) {
            //set the validated price_id
            $data['price_id'] = ($data['billing_cycle'] == 'monthly') ? $package->package_gateway_stripe_price_monthly : $package->package_gateway_stripe_price_yearly;
        } else {
            Log::error("stripe subscription plans could not be validated for this package", ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get the validated price data
        if (!$price = $this->getPrice($data)) {
            Log::error("initiating a subscription payment session at stripee failed - unable to get the price", ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get the customer from stripe
        if ($customer = $this->getCustomer($data)) {
            //set the validated customer id
            $data['customer_id'] = $customer->id;
        } else {
            Log::error("initiating a subscription payment session at stripee failed - unable to retrieve the customer", ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //create the checkout
        if (!$session = $this->createSubscriptionPaymentSession($data, $package)) {
            Log::error("initiating a subscription payment session at stripee failed - unable to create a payment session", ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save session id in sessions database
        $payment_session = new \App\Models\Landlord\PaymentSession();
        $payment_session->setConnection('landlord');
        $payment_session->session_creatorid = auth()->id();
        $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $payment_session->session_creator_email = auth()->user()->email;
        $payment_session->session_gateway_name = 'stripe';
        $payment_session->session_gateway_ref = $session->id;
        $payment_session->session_amount = $price->unit_amount / 100;
        $payment_session->session_invoices = null;
        $payment_session->session_subscription_id = $data['subscription_id'];
        $payment_session->session_payload = json_encode($session);
        $payment_session->save();

        //return the session id
        Log::info("initiating a subscription payment session at stripee - completed", ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $session->id;

    }

    /** --------------------------------------------------------------------------------------------
     * [get customer]
     * - if this user has a stripe id in our database, attempt to get the user from stripe
     * - else, create a new user in stripe
     * @source https://stripe.com/docs/api/customers/retrieve
     * @source https://stripe.com/docs/api/customers/create
     * @param int user_id
     * @return mixed stripe customer object or bool(false)
     * -------------------------------------------------------------------------------------------*/
    public function getCustomer($data = []) {

        Log::info("fetching a customer from stripe - started", ['process' => '[get-stripe-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        $tenant_id = $data['tenant_id'];

        //get the tenent
        if (!$tenant = \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', $tenant_id)->first()) {
            Log::error("getting a customer from stripe failed - the tenant could not be found in the landlord db", ['process' => '[get-stripe-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        $tenant_stripe_customer_id = $tenant->tenant_stripe_customer_id;

        //check if the current user is a stripe customer.
        if ($tenant->tenant_stripe_customer_id != '') {
            //get the customer
            try {
                $customer = \Stripe\Customer::retrieve($tenant->tenant_stripe_customer_id);
                Log::info("getting a customer ($tenant->tenant_stripe_customer_id) from stripe- completed", ['process' => '[get-stripe-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return $customer;
            } catch (exception $e) {
                Log::info("this tenant has a stripe customer id ($tenant_stripe_customer_id), but the user was not found in stripe - will now create a new user", ['process' => '[get-stripe-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //get the current logged in user (i.e. the paying user)
        $user = \App\Models\User::Where('id', auth()->id())->first();

        Log::info("the customer ($tenant_stripe_customer_id) was not found at stripe - will now create one", ['process' => '[stripe-validate-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //create a new customer in stripe
        try {
            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'metadata' => [
                    'userid' => $user->id,
                    'tenant_id' => $tenant_id,
                ],
            ]);
            //update tenant profile with stripe id
            $tenant->tenant_stripe_customer_id = $customer->id;
            $tenant->save();

            Log::info("creating a new customer ($customer->id) at stripe - completed", ['process' => '[get-stripe-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //return
            return $customer;
        } catch (exception $e) {
            Log::error("error creating a new customer at stripe - error: " . $e->getMessage(), ['process' => '[get-stripe-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        Log::error("fetching a customer from stripe - failed", ['process' => '[get-stripe-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //return
        return false;

    }

    /** --------------------------------------------------------------------------------------------
     * [create subsccription session]
     * - create a payment intent session. This session will also be returned by stripe in the
     *   paymeht complete webhook (checkout.session.completed) webhook
     * @source https://stripe.com/docs/payments/checkout
     * @return mixed stripe product object or bool(false)
     * -------------------------------------------------------------------------------------------*/
    public function createSubscriptionPaymentSession($data = []) {

        //create the checkout session
        try {
            $session = \Stripe\Checkout\Session::create([
                'customer' => $data['customer_id'],
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $data['price_id'],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('app/settings/account/thankyou/stripe?checkout_session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => $data['cancel_url'],
                'metadata' => [
                    'subscription_id' => $data['subscription_id'],
                ],
            ]);
            return $session;
        } catch (exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-initiating-a-payment-session]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
        }

        return false;
    }

    /**
     * With Stripe, the "Plans" are really "Products + Prices"
     * - Check if a package has payment gateway plans saved in it
     *    - If it does not - create them
     * - If it does, validate them
     *    - If they are invalid, try and create again
     *
     * @param  array  $data payload of the payment data
     * @return bool
     */
    public function validateGatewayPlans($data) {

        Log::info("validating the package's plans (prices) at stripe", ['process' => '[stripe-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //get the package
        $package = $data['package'];

        //[monthly plan] - the package has a gateway plan/price id - attempt to retrieve it
        if ($package->package_gateway_stripe_price_monthly) {
            if ($this->getPrice([
                'settings_stripe_secret_key' => $data['settings_stripe_secret_key'],
                'price_id' => $package->package_gateway_stripe_price_monthly,
            ])) {
            } else {
                $package->package_gateway_stripe_price_monthly = '';
                $package->package_gateway_stripe_product_monthly = '';
                $package->save();
                Log::info("the plan (price) could not be loaded from the stripe. Will now delete it from the package and recreate in stripe", ['process' => '[stripe-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[yearly plan] - the package has a gateway plan/price id - attempt to retrieve it
        if ($package->package_gateway_stripe_price_yearly) {
            if ($this->getPrice([
                'settings_stripe_secret_key' => $data['settings_stripe_secret_key'],
                'price_id' => $package->package_gateway_stripe_price_yearly,
            ])) {
            } else {
                //update the package
                $package->package_gateway_stripe_price_yearly = '';
                $package->package_gateway_stripe_product_yearly = '';
                $package->save();
                Log::info("the plan (price) could not be loaded from the stripe. Will now delete it from the package and recreate in stripe", ['process' => '[stripe-validate-gateway-plans]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[monthly plan] - the package does not have a gateway plan - attempt to create it
        if ($package->package_gateway_stripe_price_monthly == '') {
            if ($plan = $this->createPlan([
                'settings_stripe_secret_key' => $data['settings_stripe_secret_key'],
                'amount' => $package->package_amount_monthly * 100,
                'currency' => $data['currency'],
                'cycle' => 'month',
                'name' => $package->package_name,
            ])) {
                //update the package
                $package->package_gateway_stripe_price_monthly = $plan->id;
                $package->package_gateway_stripe_product_monthly = $plan->product;
                $package->save();
            }
        }

        //there is no yearly plan - create one
        if ($package->package_gateway_stripe_price_yearly == '') {
            if ($plan = $this->createPlan([
                'settings_stripe_secret_key' => $data['settings_stripe_secret_key'],
                'amount' => $package->package_amount_yearly * 100,
                'currency' => $data['currency'],
                'cycle' => 'year',
                'name' => $package->package_name,
            ])) {
                //update package
                $package->package_gateway_stripe_price_yearly = $plan->id;
                $package->package_gateway_stripe_product_yearly = $plan->product;
                $package->save();
            }
        }

        //return the validate package
        return $package;

    }

    /**
     * Stripe API does not allow updating a price that has already been used. It also does now allow
     * deleting any prices (via the API)
     *  - archive the current price ( default plan prices will not be archived by stripe)
     *  - create a new price
     *  - update the package with the new price id from stripe
     *
     * @param  object  $package the package
     * @param  array  $data payload
     * @return bool
     */
    public function updatePlanPrice($package = '', $data = []) {


        Log::info("updating package ($package->package_name) [price] at stripe - started", ['process' => '[stripe-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
        Log::info("we will be creating a new plan for the packge ($package->package_name)", ['process' => '[stripe-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validation - if this update process is necessary
        if (empty($data['product_id'])) {
            Log::info("the package does not have a stripe [product_id] - this update process is not needed - will now exit", ['process' => '[stripe-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return true;
        }

        //validation - for error
        $required = ['stripe_secret_key', 'price_id', 'price_amount', 'price_cycle', 'price_currency'];
        foreach ($required as $key) {
            if (empty($data[$key])) {
                Log::error("updating a plan - failed - [$key] was not provided", ['process' => '[stripe-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        }

        Log::info("we now create a new [price] for the packge ($package->package_name) and archiving the old price (".$data['price_id'].")", ['process' => '[stripe-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //(1) archive current price
        $this->archivePrice([
            'settings_stripe_secret_key' => $data['stripe_secret_key'],
            'price_id' => $data['price_id'],
        ]);

        //(2) create a new price
        if ($price = $this->createPrice([
            'settings_stripe_secret_key' => $data['stripe_secret_key'],
            'product_id' => $data['product_id'],
            'price_amount' => $data['price_amount'],
            'price_currency' => $data['price_currency'],
            'price_cycle' => $data['price_cycle'],
        ])) {

            //update package with new price id
            if ($data['price_cycle'] == 'month') {
                $package->package_gateway_stripe_price_monthly = $price->id;
            }
            if ($data['price_cycle'] == 'year') {
                $package->package_gateway_stripe_price_yearly = $price->id;
            }
            $package->save();

            Log::info("updating package ($package->package_name) with new price ($price->id) at stripe - completed", ['process' => '[stripe-update-plan-price]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //success
            return true;

        } else {
            return false;
        }
    }

}