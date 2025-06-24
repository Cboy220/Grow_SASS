<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account\Settings;

use App\Http\Controllers\Controller;
use Log;
use Validator;

class Email extends Controller {

    /**
     * The foo repository instance.
     */
    protected $foorepo;

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        $settings = \App\Models\Settings::Where('settings_id', 1)->first();

        //get landlord settings
        $landlord_defaults = \App\Models\Landlord\Defaults::On('landlord')->Where('defaults_id', 1)->first();

        //sync email settings
        $this->syncSettings($settings, $landlord_defaults);

        $page = $this->pageSettings();

        $html = view('account/email/general', compact('page', 'settings', 'landlord_defaults'))->render();

        $jsondata['dom_html'][] = array(
            'selector' => "#settings-wrapper",
            'action' => 'replace',
            'value' => $html);

        //left menu activate
        if (request('url_type') == 'dynamic') {
            $jsondata['dom_attributes'][] = [
                'selector' => '#settings-menu-email',
                'attr' => 'aria-expanded',
                'value' => false,
            ];
            $jsondata['dom_action'][] = [
                'selector' => '#settings-menu-email',
                'action' => 'trigger',
                'value' => 'click',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#settings-menu-email-saas',
                'action' => 'add',
                'value' => 'active',
            ];
        }

        request()->session()->forget(['smtp-required-warning']);

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * sync the customer settings to match default email settings in landlord
     *
     * @return \Illuminate\Http\Response
     */
    public function syncSettings($settings = [], $landlord_defaults = []) {

        //nothing to do
        if ($landlord_defaults->defaults_email_delivery == 'smtp_and_sendmail') {
            //make sure we have semto settings or revert to sendmail
            if ($settings->settings_email_server_type == 'smtp' && $settings->settings_email_smtp_host == '') {
                $settings->settings_email_server_type = 'sendmail';
                $settings->settings_saas_email_server_type = 'local';
                $settings->save();
            }
            return;
        }

        //nothing to do
        if ($settings->settings_email_server_type == 'smtp') {
            return;
        }

        //set the use to SMTP
        $settings->settings_email_server_type = 'smtp';
        $settings->settings_saas_email_forwarding_address = '';
        $settings->settings_saas_email_server_type = 'smtp';
        $settings->save();

    }

    /**
     * update the local email settings
     *
     * @return \Illuminate\Http\Response
     */
    public function updateLocal() {

        //get the item
        $settings = \App\Models\Settings::Where('settings_id', 1)->first();

        //current settings
        $current_forwarding_address = $settings->settings_saas_email_forwarding_address;

        //custom error messages
        $messages = [
            'settings_saas_email_local_address.required' => __('lang.email_address') . '-' . __('lang.is_required'),
            'settings_email_from_name.required' => __('lang.from_name') . '-' . __('lang.is_required'),
            'settings_saas_email_forwarding_address.required' => __('lang.forward_replies_to') . '-' . __('lang.is_required'),
            'settings_saas_email_forwarding_address.email' => __('lang.forward_replies_to') . '-' . __('lang.invalid_email_address'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_email_from_name' => [
                'required',
            ],
            'settings_saas_email_forwarding_address' => [
                'required',
                'email',
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

        $customer = \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', $settings->settings_saas_tenant_id)->first();
        $customer->tenant_email_config_type = 'local';
        $customer->tenant_email_local_email = $settings->settings_saas_email_local_address;
        $customer->tenant_email_forwarding_email = request('settings_saas_email_forwarding_address');
        $customer->save();

        //update landlord
        if ($current_forwarding_address != request('settings_saas_email_forwarding_address')) {
            $customer->tenant_email_config_status = 'pending';
            $customer->save();
        }

        //save settings
        $settings->settings_saas_email_forwarding_address = request('settings_saas_email_forwarding_address');
        $settings->settings_email_from_name = request('settings_email_from_name');
        $settings->settings_email_server_type = 'sendmail';
        $settings->settings_saas_email_server_type = 'local';
        $settings->save();

        return response()->json(array(
            'notification' => [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ],
            'skip_dom_reset' => true, //optional
        ));

    }

    /**
     * update the local email settings
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSMTP() {

        //get the item
        $settings = \App\Models\Settings::Where('settings_id', 1)->first();

        //custom error messages
        $messages = [
            'settings_email_from_name.required' => __('lang.from_name') . '-' . __('lang.is_required'),
            'settings_email_from_address.required' => __('lang.email_address') . '-' . __('lang.is_required'),
            'settings_email_smtp_host.required' => __('lang.smtp_host') . '-' . __('lang.is_required'),
            'settings_email_smtp_port.required' => __('lang.smtp_port') . '-' . __('lang.is_required'),
            'settings_email_smtp_username.required' => __('lang.username') . '-' . __('lang.is_required'),
            'settings_email_smtp_encryption.required' => __('lang.encryption') . '-' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_email_from_name' => [
                'required',
            ],
            'settings_email_from_address' => [
                'required',
                'email',
            ],
            'settings_email_smtp_host' => [
                'required',
            ],
            'settings_email_smtp_port' => [
                'required',
            ],
            'settings_email_smtp_username' => [
                'required',
            ],
            'settings_email_smtp_encryption' => [
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

        //delete any forwading request
        \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', $settings->settings_saas_tenant_id)
            ->update(['tenant_email_config_status' => 'completed']);

        //save settings
        $settings->settings_email_from_name = request('settings_email_from_name');
        $settings->settings_email_from_address = request('settings_email_from_address');
        $settings->settings_email_smtp_host = request('settings_email_smtp_host');
        $settings->settings_email_smtp_port = request('settings_email_smtp_port');
        $settings->settings_email_smtp_username = request('settings_email_smtp_username');
        $settings->settings_email_smtp_password = request('settings_email_smtp_password');
        $settings->settings_email_smtp_encryption = request('settings_email_smtp_encryption');
        $settings->settings_email_server_type = 'smtp';
        $settings->settings_saas_email_forwarding_address = '';
        $settings->settings_saas_email_server_type = 'smtp';
        $settings->save();

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

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.email'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.email'),
            'heading' => __('lang.email'),
        ];

        return $page;
    }
}