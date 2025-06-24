<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Defaults\ShowResponse;
use Validator;

class Defaults extends Controller {

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
        $defaults = \App\Models\Landlord\Defaults::Where('defaults_id', 1)->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'defaults' => $defaults,
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
            'defaults_language_default.required' => __('lang.default_language') . ' - ' . __('lang.is_required'),
            'defaults_timezone.required' => __('lang.timezone') . ' - ' . __('lang.is_required'),
            'defaults_date_format.required' => __('lang.date_format') . ' - ' . __('lang.is_required'),
            'defaults_datepicker_format.required' => __('lang.date_picker_format') . ' - ' . __('lang.is_required'),
            'defaults_currency_code.required' => __('lang.currency_code') . ' - ' . __('lang.is_required'),
            'defaults_currency_symbol.required' => __('lang.currency_symbol') . ' - ' . __('lang.is_required'),
            'defaults_decimal_separator.required' => __('lang.decimal_separator') . ' - ' . __('lang.is_required'),
            'defaults_thousand_separator.required' => __('lang.thousands_separator') . ' - ' . __('lang.is_required'),
            'defaults_currency_position.required' => __('lang.currency_symbol_position') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'defaults_language_default' => [
                'required',
            ],
            'defaults_timezone' => [
                'required',
            ],
            'defaults_date_format' => [
                'required',
            ],
            'defaults_datepicker_format' => [
                'required',
            ],
            'defaults_currency_code' => [
                'required',
            ],
            'defaults_currency_symbol' => [
                'required',
            ],
            'defaults_decimal_separator' => [
                'required',
            ],
            'defaults_thousand_separator' => [
                'required',
            ],
            'defaults_currency_position' => [
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


       //update settings
        \App\Models\Landlord\Defaults::where('defaults_id', 1)
            ->update([
                'defaults_language_default' => request('defaults_language_default'),
                'defaults_timezone' => request('defaults_timezone'),
                'defaults_date_format' => request('defaults_date_format'),
                'defaults_datepicker_format' => request('defaults_datepicker_format'),
                'defaults_currency_code' => request('defaults_currency_code'),
                'defaults_currency_symbol' => request('defaults_currency_symbol'),
                'defaults_decimal_separator' => request('defaults_decimal_separator'),
                'defaults_thousand_separator' => request('defaults_thousand_separator'),
                'defaults_currency_position' => request('defaults_currency_position'),
                'defaults_email_delivery' => request('defaults_email_delivery'),
            ]);

        $jsondata['redirect_url'] = url('/app-admin/settings/defaults');

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
            'inner_menu_defaults' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}