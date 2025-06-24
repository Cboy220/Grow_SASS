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

class SectionSplash extends Controller {

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
    public function edit() {

        $page = $this->pageSettings();

        //get section
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', "section-splash-title")->first();

        //items
        $items = \App\Models\Landlord\Frontend::Where('frontend_group', "section-splash")->get();

        return view('landlord/frontend/splash/page', compact('page', 'section', 'items'))->render();

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FileRepository $filerepo) {

        //get the item
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', "section-splash-title")->first();

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
        $jsondata['redirect_url'] = url("app-admin/frontend/section/splash");

        //ajax response
        return response()->json($jsondata);

    }



        /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function editImage($id) {

        $page = $this->pageSettings();

        //get section
        $item = \App\Models\Landlord\Frontend::Where('frontend_id', $id)->first();

        $html = view('landlord/frontend/modal/splash-content', compact('page', 'item'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLandlordUploadImage',
        ];

        //ajax response
        return response()->json($jsondata);

    }


        /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function updateImage(FileRepository $filerepo, $id) {

        //get the item
        $section = \App\Models\Landlord\Frontend::Where('frontend_id', $id)->first();

        //update record
        $section->frontend_data_1 = request('frontend_data_1');
        $section->save();

        //update image details
        if (request()->filled('image_directory') && request()->filled('image_filename')) {
            $filerepo->processFrontendImage([
                'directory' => request('image_directory'),
                'filename' => request('image_filename'),
            ]);
            $section->frontend_directory = request('image_directory');
            $section->frontend_filename = request('image_filename');
            $section->save();
        }

        //replace element
        $item = $section;
        $html = view('landlord/frontend/splash/ajax', compact('item'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#splash_item_' . $section->frontend_id,
            'action' => 'replace-with',
            'value' => $html,
        ];

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        //notice success
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
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
            'crumbs' => [
                __('lang.frontend'),
                __("lang.section_6"),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            'inner_menu_section_home' => 'active',
            "inner_menu_tab_section_6" => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}