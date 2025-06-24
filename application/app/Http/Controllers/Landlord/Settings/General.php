<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\General\ShowResponse;
use Validator;

class General extends Controller {

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
            'settings_system_renewal_grace_period.required' => __('lang.renewal_grace_period') . ' - ' . __('lang.is_required'),
            'html_settings_terms_of_service.required_if' => __('lang.terms_of_service') . ' - ' . __('lang.is_required'),
            'settings_terms_of_service_text.required_if' => __('lang.terms_of_service_text') . ' - ' . __('lang.is_required'),
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
            'settings_system_renewal_grace_period' => [
                'required',
            ],
            'html_settings_terms_of_service' => [
                'required_if:settings_terms_of_service_status,enabled',
            ],
            'settings_terms_of_service_text' => [
                'required_if:settings_terms_of_service_status,enabled',
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

        //validate required
        if (request('settings_system_renewal_grace_period') < 1) {
            abort(409, __('lang.grace_period_warning'));
        }

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //update settings
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_purchase_code' => request('settings_purchase_code'),
                'settings_system_timezone' => request('settings_system_timezone'),
                'settings_system_date_format' => request('settings_system_date_format'),
                'settings_system_datepicker_format' => request('settings_system_datepicker_format'),
                'settings_system_renewal_grace_period' => request('settings_system_renewal_grace_period'),
                'settings_system_language_default' => request('settings_system_language_default'),
                'settings_terms_of_service' => request('html_settings_terms_of_service'),
                'settings_terms_of_service_status' => request('settings_terms_of_service_status'),
                'settings_terms_of_service_text' => request('settings_terms_of_service_text'),
            ]);

        $jsondata['redirect_url'] = url('/app-admin/settings/general');

        request()->session()->flash('success-notification-long', __('lang.request_has_been_completed'));

        //ajax response
        return response()->json($jsondata);
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
            'inner_menu_general' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}