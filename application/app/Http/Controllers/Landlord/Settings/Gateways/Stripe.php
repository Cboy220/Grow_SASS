<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings\Gateways;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Gateways\Stripe\ShowResponse;
use DB;
use Validator;

class Stripe extends Controller {

    public function __construct(
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function show() {

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'settings' => $settings,
            'section' => 'stripe',
        ];

        //show the form
        return new ShowResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_stripe_secret_key' => 'required',
            'settings_stripe_public_key' => 'required',
            'settings_stripe_webhooks_key' => 'required',
            'settings_stripe_display_name' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //test api connection (validate the key)
        try {
            //set key
            \Stripe\Stripe::setApiKey(request('settings_stripe_secret_key'));
            //try a basic request
            $endpoints = \Stripe\WebhookEndpoint::all(['limit' => 1]);
        } catch (\Stripe\Exception\AuthenticationException$e) {
            abort(409, __('lang.stripe_authentication_error'));
        } catch (\Stripe\Exception\ApiConnectionException$e) {
            abort(409, __('lang.stripe_network_error'));
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            abort(409, __('lang.stripe_generic_error') . ' - ' . $error_message);
        }

        //update settings
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_stripe_secret_key' => request('settings_stripe_secret_key'),
                'settings_stripe_public_key' => request('settings_stripe_public_key'),
                'settings_stripe_webhooks_key' => request('settings_stripe_webhooks_key'),
                'settings_stripe_display_name' => request('settings_stripe_display_name'),
                'settings_stripe_status' => (request('settings_stripe_status') == 'on') ? 'enabled' : 'disabled',
            ]);

        //are we resetting or disabling the gateway
        if (request('settings_stripe_reset_plans') == 'on' || request('settings_stripe_status') != 'on') {
            //remove prices and plan id's
            DB::table('packages')->update([
                'package_gateway_stripe_product_monthly' => '',
                'package_gateway_stripe_price_monthly' => '',
                'package_gateway_stripe_product_yearly' => '',
                'package_gateway_stripe_price_yearly' => '',
            ]);

            //remove the customers stripe user account (when resetting only)
            if (request('settings_stripe_reset_plans') == 'on') {
                DB::table('tenants')->update([
                    'tenant_stripe_customer_id' => '',
                ]);
            }
        }

        //ajax response
        return response()->json(array(
            'notification' => [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ],
            'skip_dom_reset' => true,
        ));

    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.stripe_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_group_menu_billing' => 'active',
            'inner_menu_stripe' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}