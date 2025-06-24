<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Validator;

class Contact extends Controller {


    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        //$this->middleware('auth');

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        if (config('system.settings_frontend_status') == 'disabled') {
            abort(404);
        }

        //menus
        $mainmenu = \App\Models\Landlord\Frontend::Where('frontend_group', 'main-menu')->orderBy('frontend_name', 'asc')->get();

        //section 5
        $content = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-contact')->first();

        //footer
        $footer = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer')->first();

        //cta panel
        $cta = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer-cta')->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'mainmenu' => $mainmenu,
            'show_footer_cta' => true,
            'cta' => $cta,
        ];

        return view('frontend/contact/page', compact('payload', 'mainmenu', 'content', 'footer', 'cta'))->render();

    }

    /**
     * process subsmitted contact form
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function submitForm() {

        //[HEADER]

        //custom error messages
        $messages = [
            'contact_name.required' => __('lang.name') . '-' . __('lang.is_required'),
            'contact_email.required' => __('lang.email') . '-' . __('lang.is_required'),
            'contact_message.required' => __('lang.message') . '-' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'contact_name' => [
                'required',
            ],
            'contact_email' => [
                'required',
            ],
            'contact_message' => [
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

        /** --------------------------------------------------------------------------------------
         * send form to the email address that is specified in the contact us form's settings
         * -------------------------------------------------------------------------------------*/
        $section = \App\Models\Landlord\Frontend::On('landlord')->Where('frontend_name', 'page-contact')->first();
        $to_email = $section->frontend_data_3;

        $data = [
            'to_email' => $to_email,
            'contact_name' => request('contact_name'),
            'contact_email' => request('contact_email'),
            'message' => request('contact_message'),
        ];
        $mail = new \App\Mail\Landlord\Admin\ContactUs($data, []);
        $mail->build();

        $jsondata['dom_visibility'][] = [
            'selector' => '#contact-us-form',
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#contact-us-thank-you',
            'action' => 'show',
        ];

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

        ];

        //return
        return $page;
    }
}