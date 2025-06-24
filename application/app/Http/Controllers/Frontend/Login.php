<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class Login extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        //$this->middleware('guest');

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //menus
        $mainmenu = \App\Models\Landlord\Frontend::Where('frontend_group', 'main-menu')->orderBy('frontend_name', 'asc')->get();

        //get the item
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-login')->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
        ];

        return view('frontend/signin/page', compact('payload', 'mainmenu', 'section'))->render();

    }

    /**
     * get users account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAccount() {

        //validation
        if (!request()->filled('domain_name')) {
            return response()->json(array(
                'notification' => [
                    'type' => 'error',
                    'value' => __('lang.account_could_not_be_found'),
                ],
                'skip_dom_reset' => true,
                'skip_dom_tinymce' => true,
            ));
        }

        //check if file exists in the database
        if (!$tenant = \App\Models\Landlord\Tenant::On('landlord')->Where('subdomain', request('domain_name'))->first()) {
            return response()->json(array(
                'notification' => [
                    'type' => 'error',
                    'value' => __('lang.account_could_not_be_found'),
                ],
                'skip_dom_reset' => true,
                'skip_dom_tinymce' => true,
            ));
        }

        //redirect url
        $jsondata['redirect_url'] = 'https://' . $tenant->domain;

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