<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use Validator;

class Gateways extends Controller {

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

        $page = $this->pageSettings('index');

        //show the form
        return view('landlord/settings/sections/gateways/general', compact('page', 'settings'))->render();

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function update() {

        //custom error messages
        $messages = [
            'settings_gateways_default_product_name.required' => __('lang.default_product_name') . ' - ' . __('lang.is_required'),
            'settings_gateways_default_product_description.required' => __('lang.default_product_description') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_gateways_default_product_name' => [
                'required',
            ],
            'settings_gateways_default_product_description' => [
                'required',
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //schedule for updating at the [product name] at various payment gateways
        if ($settings->settings_gateways_default_product_name != request('settings_gateways_default_product_name') || $settings->settings_gateways_default_product_description != request('settings_gateways_default_product_description')) {

            //delete previously scheduled changes
            \App\Models\Landlord\Scheduled::Where('scheduled_type', 'update-default-product-name')
                ->Where('scheduled_status', 'new')
                ->delete();

            //schedule for cronjob
            $schedule = new \App\Models\Landlord\Scheduled();
            $schedule->scheduled_gateway = 'all';
            $schedule->scheduled_type = 'update-default-product-name';
            $schedule->scheduled_payload_1 = request('settings_gateways_default_product_name');
            $schedule->save();
        }

        //update the settings
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_gateways_default_product_name' => request('settings_gateways_default_product_name'),
                'settings_gateways_default_product_description' => request('settings_gateways_default_product_description'),
            ]);

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
                __('lang.general_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'mainmenu_settings' => 'active',
            'inner_group_menu_billing' => 'active',
            'inner_menu_gateways' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}