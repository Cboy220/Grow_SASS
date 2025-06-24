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

class SectionCTA extends Controller {

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
    public function edit($id) {

        $page = $this->pageSettings($id);

        //get section
        if (!$section = \App\Models\Landlord\Frontend::Where('frontend_name', $id)->first()) {
            abort(404);
        }

        return view('landlord/frontend/cta/page', compact('page', 'section'))->render();

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //get the item
        if (!$section = \App\Models\Landlord\Frontend::Where('frontend_name', $id)->first()) {
            abort(404);
        }

        //update record
        $section->frontend_data_1 = request('frontend_data_1');
        $section->frontend_data_2 = request('frontend_data_2');
        $section->frontend_data_3 = request('frontend_data_3');
        $section->frontend_data_4 = request('frontend_data_4');
        $section->frontend_data_5 = request('frontend_data_5');
        $section->frontend_data_6 = request('frontend_data_6');

        $section->save();


        //redirect back
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
        $jsondata['redirect_url'] = url("app-admin/frontend/section/$id/cta");

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
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            'inner_menu_section_home' => 'active',
        ];

        if($section == 'section-3'){
            $page['crumbs'] = [
                __('lang.frontend'),
                __('lang.section_3'),
            ];
            $page['inner_menu_tab_section_3'] = 'active';
        }

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}