<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings\Gateways;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Gateways\Paystack\ShowResponse;
use DB;
use Validator;

class Paystack extends Controller {

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
            'section' => 'general',
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
        //validate - custom error messages
        $messages = [
            'settings_paystack_secret_key.required' => __('lang.secret_key') . ' - ' . __('lang.is_required'),
            'settings_paystack_public_key.required' => __('lang.public_key') . ' - ' . __('lang.is_required'),
            'settings_paystack_display_name.required' => __('lang.display_name') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_paystack_secret_key' => 'required',
            'settings_paystack_public_key' => 'required',
            'settings_paystack_display_name' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //reset existing account owner
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_paystack_secret_key' => request('settings_paystack_secret_key'),
                'settings_paystack_public_key' => request('settings_paystack_public_key'),
                'settings_paystack_display_name' => request('settings_paystack_display_name'),
                'settings_paystack_status' => (request('settings_paystack_status') == 'on') ? 'enabled' : 'disabled',
            ]);

        //are we resetting or disabling the gateway
        if (request('settings_paystack_reset_plans') == 'on' || request('settings_paystack_status') != 'on') {
            DB::table('packages')->update([
                'package_gateway_paystack_plan_monthly' => '',
                'package_gateway_paystack_plan_yearly' => '',
            ]);

            //remove the customers paystack user account (when resetting only)
            if (request('settings_paystack_reset_plans') == 'on') {
                DB::table('tenants')->update([
                    'tenant_paystack_customer_id' => '',
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
                __('lang.paystack_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_group_menu_billing' => 'active',
            'inner_menu_paystack' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}