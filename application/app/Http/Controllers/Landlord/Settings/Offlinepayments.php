<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Offlinepayment\ShowResponse;
use Validator;

class Offlinepayments extends Controller {

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
            'settings_offline_payments_status.required' => __('lang.offline_payments_status') . ' - ' . __('lang.is_required'),
            'settings_offline_payments_display_name.required_if' => __('lang.display_name') . ' - ' . __('lang.is_required'),
            'settings_offline_payments_details.required_if' => __('lang.payment_details_instructions') . ' - ' . __('lang.is_required'),
            'html_settings_offline_proof_of_payment_message.required_if' => __('lang.proof_of_payment_information') . ' - ' . __('lang.is_required'),
            'html_settings_offline_proof_of_payment_thank_you.required_if' => __('lang.proof_of_payment_thank_you') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_offline_payments_status' => [
                'required',
            ],
            'settings_offline_payments_display_name' => [
                'required_if:settings_offline_payments_status,enabled',
            ],
            'settings_offline_payments_details' => [
                'required_if:settings_offline_payments_status,enabled',
            ],
            'html_settings_offline_proof_of_payment_message' => [
                'required_if:settings_offline_payments_status,enabled',
            ],
            'html_settings_offline_proof_of_payment_thank_you' => [
                'required_if:settings_offline_payments_status,enabled',
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
                'settings_offline_payments_status' => request('settings_offline_payments_status'),
                'settings_offline_payments_display_name' => request('settings_offline_payments_display_name'),
                'settings_offline_payments_details' => request('settings_offline_payments_details'),
                'settings_offline_proof_of_payment_message' => request('html_settings_offline_proof_of_payment_message'),
                'settings_offline_proof_of_payment_thank_you' => request('html_settings_offline_proof_of_payment_thank_you'),
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
                __('lang.paypal_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_group_menu_billing' => 'active',
            'inner_menu_offline_payment' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}