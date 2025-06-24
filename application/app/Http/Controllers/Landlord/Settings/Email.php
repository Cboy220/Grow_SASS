<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Emaillog\LogResponse;
use App\Http\Responses\Landlord\Settings\Email\ShowResponse;
use App\Http\Responses\Landlord\Settings\Email\TestEmailResponse;
use Validator;

class Email extends Controller {

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
            'page' => $this->pageSettings('general'),
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

        //validate the form
        $validator = Validator::make(request()->all(), [
            'settings_email_from_address' => 'required|email',
            'settings_email_from_name' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //reset existing account owner
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_email_from_address' => request('settings_email_from_address'),
                'settings_email_from_name' => request('settings_email_from_name'),
                'settings_email_server_type' => request('settings_email_server_type'),
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
     * show form for send a test email
     *
     * @return \Illuminate\Http\Response
     */
    public function testEmail() {

        //default what to show in modal
        $show = 'form';

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //check if cronjon has run
        if ($settings->settings_cronjob_has_run != 'yes') {
            $show = 'error';
        }

        //reponse payload
        $payload = [
            'section' => 'form',
            'show' => $show,
        ];

        //show the view
        return new TestEmailResponse($payload);
    }

    /**
     * Send a test email
     *
     * @return \Illuminate\Http\Response
     */
    public function testEmailAction() {

        //validate
        if (!request()->filled('email')) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        /** ----------------------------------------------
         * send a test email
         * ----------------------------------------------*/
        $data = [
            'notification_subject' => __('lang.email_delivery_test'),
            'notification_title' => __('lang.email_delivery_test'),
            'notification_message' => __('lang.email_delivery_this_is_a_test'),
            'first_name' => auth()->user()->first_name,
            'email' => request('email'),
        ];
        $mail = new \App\Mail\Landlord\Admin\TestEmail($data);
        $mail->build();

        //reponse payload
        $payload = [
            'section' => 'success',
        ];

        //show the view
        return new TestEmailResponse($payload);
    }

    /**
     * Show email log
     * @return blade view | ajax view
     */
    public function logShow() {

        //get all emails
        $emails = \App\Models\Landlord\EmailLog::query()
            ->orderBy('emaillog_id', 'DESC')
            ->paginate(config('system.settings_system_pagination_limits'));

        //payload
        $payload = [
            'page' => $this->pageSettings('log'),
            'emails' => $emails,
        ];

        //show the view
        return new LogResponse($payload);
    }

    /**
     * Show the email
     * @return blade view | ajax view
     */
    public function logRead($id) {

        if (!$email = \App\Models\Landlord\EmailLog::Where('emaillog_id', $id)->first()) {
            abort(404);
        }

        //page
        $html = view('landlord/settings/sections/emaillog/read', compact('email'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //fix emailfooer
        $jsondata['dom_classes'][] = [
            'selector' => 'style',
            'action' => 'remove',
            'value' => 'footer',
        ];

        //remove <style> tags
        $jsondata['dom_visibility'][] = [
            'selector' => '.settings-email-view-wrapper > style',
            'action' => 'hide-remove',
        ];

        //render
        return response()->json($jsondata);
    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function logDelete($id) {

        if (!$email = \App\Models\Landlord\EmailLog::Where('emaillog_id', $id)->first()) {
            abort(404);
        }

        //delete record
        $email->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#email_' . $id,
            'action' => 'slideup-slow-remove',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }

    /**
     * delete all emails in the log
     *
     * @return \Illuminate\Http\Response
     */
    public function logPurge() {

        //delete all rows
        \App\Models\Landlord\EmailLog::getQuery()->delete();

        //remove all rows
        $jsondata['dom_visibility'][] = array(
            'selector' => '.settings-each-email',
            'action' => 'hide',
        );
        $jsondata['dom_visibility'][] = array(
            'selector' => '.loadmore-button-container',
            'action' => 'hide',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
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
                __('lang.email_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_group_menu_email' => 'active',
        ];

        //general settings
        if ($section == 'general') {
            $page['inner_menu_email'] = 'active';
        }

        //log
        if ($section == 'log') {
            $page['inner_menu_email_log'] = 'active';
        }

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}