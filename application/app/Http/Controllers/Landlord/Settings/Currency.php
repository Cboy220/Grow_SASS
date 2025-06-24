<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Currency\ShowResponse;
use Validator;

class Currency extends Controller {

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
            'settings_system_currency_code.required' => __('lang.currency_code') . ' - ' . __('lang.is_required'),
            'settings_system_currency_symbol.required' => __('lang.currency_symbol') . ' - ' . __('lang.is_required'),
            'settings_system_decimal_separator.required' => __('lang.decimal_separator') . ' - ' . __('lang.is_required'),
            'settings_system_thousand_separator.required' => __('lang.thousands_separator') . ' - ' . __('lang.is_required'),
            'settings_system_currency_position.required' => __('lang.currency_symbol_position') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_system_currency_code' => [
                'required',
            ],
            'settings_system_currency_symbol' => [
                'required',
            ],
            'settings_system_decimal_separator' => [
                'required',
            ],
            'settings_system_thousand_separator' => [
                'required',
            ],
            'settings_system_currency_position' => [
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
                'settings_system_currency_code' => request('settings_system_currency_code'),
                'settings_system_currency_symbol' => request('settings_system_currency_symbol'),
                'settings_system_decimal_separator' => request('settings_system_decimal_separator'),
                'settings_system_thousand_separator' => request('settings_system_thousand_separator'),
                'settings_system_currency_position' => request('settings_system_currency_position'),
            ]);

        //ajax response
        return response()->json(array(
            'notification' => [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ],
            'skip_dom_reset' => true, //optional
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
                __('lang.currency'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_menu_currency' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}