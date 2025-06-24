<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Domain\ShowResponse;
use DB;
use Validator;

class Domain extends Controller {

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
            'section' => 'domain',
        ];

        //show the form
        return new ShowResponse($payload);
    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function update() {

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //get curreny email domain
        $current_email_domain = $settings->settings_email_domain;

        //custom error messages
        $messages = [
            'settings_base_domain.required' => __('lang.base_domain_name') . ' - ' . __('lang.is_required'),
            'settings_email_domain.required' => __('lang.email_domain_name') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_base_domain' => [
                'required',
            ],
            'settings_email_domain' => [
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
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_base_domain' => cleanURLDomain(request('settings_base_domain')),
                'settings_email_domain' => cleanURLDomain(request('settings_email_domain')),
                'settings_reserved_words' => request('settings_reserved_words'),
            ]);

        //update all tenant domain names
        $settings_base_domain = request('settings_base_domain');
        \App\Models\Landlord\Tenant::where('domain_type', 'subdomain')
            ->update([
                'domain' => DB::raw('CONCAT(subdomain,".' . $settings_base_domain . '")'),
            ]);

        //schedule updates to tenants databases
        if ($current_email_domain != request('settings_email_domain')) {

            //delete previously scheduled changes
            \App\Models\Landlord\Scheduled::Where('scheduled_type', 'update-email-domain')->delete();

            //schedule for cronjob
            $schedule = new \App\Models\Landlord\Scheduled();
            $schedule->scheduled_type = 'update-email-domain';
            $schedule->scheduled_payload_1 = request('settings_email_domain');
            $schedule->save();
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
                __('lang.domain_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_menu_domain' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}