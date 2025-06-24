<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Cronjob\ShowResponse;
use Validator;

class Cronjob extends Controller {

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
            'section' => 'cronjob',
        ];

        //show the form
        return new ShowResponse($payload);
    }

        /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function update() {

        //custom error messages
        $messages = [
            'settings_purchase_code.required' => __('lang.purchase_code') . ' - ' . __('lang.is_required'),
            'settings_system_timezone.required' => __('lang.timezone') . ' - ' . __('lang.is_required'),
            'settings_system_date_format.required' => __('lang.date_format') . ' - ' . __('lang.is_required'),
            'settings_system_datepicker_format.required' => __('lang.date_picker_format') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_purchase_code' => [
                'required',
            ],
            'settings_system_timezone' => [
                'required',
            ],
            'settings_system_date_format' => [
                'required',
            ],
            'settings_system_datepicker_format' => [
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

        //reset existing account owner
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_purchase_code' => request('settings_purchase_code'),
                'settings_system_timezone' => request('settings_system_timezone'),
                'settings_system_date_format' => request('settings_system_date_format'),
                'settings_system_datepicker_format' => request('settings_system_datepicker_format'),
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
                __('lang.cronjob'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_menu_cronjob' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}