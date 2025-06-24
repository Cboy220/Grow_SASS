<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Emailtemplates\ShowResponse;
use App\Http\Responses\Landlord\Settings\Emailtemplates\ShowTemplateResponse;
use Illuminate\Http\Request;
use Validator;

class Emailtemplates extends Controller {

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

        //templates by types
        $customer = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_category', 'customer')->orderBy('emailtemplate_id','asc')->get();
        $admin = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_category', 'admin')->orderBy('emailtemplate_id','asc')->get();
        $other = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_category', 'other')->orderBy('emailtemplate_id','asc')->get();
        $system = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_category', 'system')->orderBy('emailtemplate_id','asc')->get();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'settings' => $settings,
            'customer' => $customer,
            'system' => $system,
            'admin' => $admin,
            'other' => $other,
            'section' => 'general',
        ];

        //show the form
        return new ShowResponse($payload);
    }

    /**
     * Display email template form
     *
     * @return \Illuminate\Http\Response
     */
    public function showTemplate($id) {

        if (!$template = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_id', $id)->first()) {
            abort(404);
        }

        //basic variables
        $variables['template'] = explode(',', $template->emailtemplate_variables);
        $variables['general'] = explode(',', config('system.settings_email_general_variables'));

        //reponse payload
        $payload = [
            'template' => $template,
            'variables' => $variables,
        ];

        //show the view
        return new ShowTemplateResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //get the tempate
        if (!$template = \App\Models\Landlord\EmailTemplate::Where('emailtemplate_id', $id)->first()) {
            abort(404);
        }
        //validate
        $validator = Validator::make(request()->all(), [
            'emailtemplate_body' => 'required',
            'emailtemplate_subject' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //update
        $template->emailtemplate_subject = request('emailtemplate_subject');
        $template->emailtemplate_body = request('emailtemplate_body');
        $template->emailtemplate_status = (request('emailtemplate_status') == 'on') ? 'enabled' : 'disabled';

        $template->save();

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
                __('lang.email_templates'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_group_menu_email' => 'active',
            'inner_menu_emailtemplates' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}