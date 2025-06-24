<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\Landlord\FileRepository;
use Validator;

class Customcode extends Controller {

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
    public function edit() {

        $page = $this->pageSettings();

        //get section
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        return view('landlord/frontend/customcode/page', compact('page', 'settings'))->render();

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FileRepository $filerepo) {

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();
        $settings->settings_code_head = request('settings_code_head');
        $settings->settings_code_body = request('settings_code_body');
        $settings->save();

        //redirect back
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
        $jsondata['redirect_url'] = url('app-admin/frontend/customcode');

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
            'meta_title' => __('lang.frontend'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            'inner_group_menu_theme' => 'active',
            'inner_menu_customcode' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}