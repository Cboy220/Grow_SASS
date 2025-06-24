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

class Heroheader extends Controller {

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
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', 'hero-header')->first();

        return view('landlord/frontend/heroheader/page', compact('page', 'section'))->render();

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FileRepository $filerepo) {

        //get the item
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', 'hero-header')->first();

        //update record
        $section->frontend_data_1 = request('frontend_data_1');
        $section->frontend_data_2 = request('frontend_data_2');
        $section->frontend_data_3 = request('frontend_data_3');
        $section->frontend_data_4 = request('frontend_data_4');
        $section->frontend_data_5 = request('frontend_data_5');
        $section->frontend_data_6 = request('frontend_data_6');
        $section->frontend_data_7 = request('frontend_data_7');
        $section->frontend_data_10 = request('frontend_data_10');
        $section->frontend_data_11 = request('frontend_data_11');
        $section->save();

        //update main image details
        if (request()->filled('image_directory') && request()->filled('image_filename')) {
            $filerepo->processFrontendImage([
                'directory' => request('image_directory'),
                'filename' => request('image_filename'),
            ]);
            $section->frontend_directory = request('image_directory');
            $section->frontend_filename = request('image_filename');
            $section->save();
        }

        //update background image details
        if (request()->filled('image_directory_2') && request()->filled('image_filename_2')) {
            $filerepo->processFrontendImage([
                'directory' => request('image_directory_2'),
                'filename' => request('image_filename_2'),
            ]);
            $section->frontend_data_8 = request('image_directory_2');
            $section->frontend_data_9 = request('image_filename_2');
            $section->save();
        }

        //redirect back
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
        $jsondata['redirect_url'] = url('app-admin/frontend/hero');

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
                __('lang.hero_header'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            'inner_menu_hero' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}