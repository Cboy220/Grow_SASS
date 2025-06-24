<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Smtp\ShowResponse;
use Validator;

class Smtp extends Controller {

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

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_email_smtp_host' => [
                'required',
            ],
            'settings_email_smtp_port' => [
                'required',
            ],
            'settings_email_smtp_username' => [
                'required',
            ],
            'settings_email_smtp_password' => [
                'required',
            ],
            'settings_email_smtp_encryption' => [
                'required',
            ],
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //reset existing account owner
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_email_smtp_host' => request('settings_email_smtp_host'),
                'settings_email_smtp_port' => request('settings_email_smtp_port'),
                'settings_email_smtp_username' => request('settings_email_smtp_username'),
                'settings_email_smtp_password' => request('settings_email_smtp_password'),
                'settings_email_smtp_encryption' => request('settings_email_smtp_encryption'),
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
     * run tests to check if SMTP ports are open
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function testSMTP() {

        $host = 'smtp.mailtrap.io';
        $ports = array(25, 2525, 465, 587);

        //check if fsockopen() is enabled
        if (!function_exists('fsockopen')) {
            $jsondata['dom_visibility'][] = [
                'selector' => '.email-testing-tool-sections',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#email-testing-tool-error-fsockopen',
                'action' => 'show',
            ];
            return response()->json($jsondata);
        }

        $results = '';
        $error_count = 0;
        foreach ($ports as $port) {
            //$connection = @fsockopen($host, $port);
            $connection = @fsockopen($host, $port, $errno, $errstr, 15);

            if (is_resource($connection)) {
                $results .= '<tr><td class="p-l-15">' . __('lang.smtp_port') . ' (' . $port . ')</td><td class="text-center">
                    <div class="inline-block label label-rounded label-success">' . __('lang.open') . '</div></td></tr>';
                fclose($connection);
            } else {
                $results .= '<tr><td class="p-l-15">' . __('lang.smtp_port') . ' (' . $port . ')</td><td class="text-center">
                    <div class="inline-block label label-rounded label-danger">' . __('lang.closed') . '</div></td></tr>';
                $error_count++;
            }
        }

        //errors
        if ($error_count == 0) {
            $jsondata['dom_visibility'][] = [
                'selector' => '.email-testing-tool-sections',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#email-testing-tool-smtp-passed',
                'action' => 'show',
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#email-testing-tool-smtp-results-passed',
                'action' => 'replace',
                'value' => $results,
            ];
            return response()->json($jsondata);
        } else {
            $jsondata['dom_visibility'][] = [
                'selector' => '.email-testing-tool-sections',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#email-testing-tool-smtp-failed',
                'action' => 'show',
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#email-testing-tool-smtp-results-failed',
                'action' => 'replace',
                'value' => $results,
            ];
            return response()->json($jsondata);
        }

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
                __('lang.smtp_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_group_menu_email' => 'active',
            'inner_menu_smtp' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}