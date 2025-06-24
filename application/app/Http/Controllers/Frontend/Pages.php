<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class Pages extends Controller {

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
    public function show() {

        $page_permanent_link = request()->segment(2);

        //page content
        if (!$content = \App\Models\Landlord\Page::Where('page_permanent_link', $page_permanent_link)->first()) {
            return view('errors.frontend.404');
        }

        //check if preview mode (for unpublished pages)
        if($content->page_status == 'draft'){
            //is this admin previewing the page
            if(request('preview') != $content->page_uniqueid){
                return view('errors.frontend.404');
            }
        }

        //some page settings
        $page = [
            'meta_title' => $content->page_meta_title,
            'meta_description' => $content->page_meta_description,
        ];

        //menus
        $mainmenu = \App\Models\Landlord\Frontend::Where('frontend_group', 'main-menu')->orderBy('frontend_name', 'asc')->get();

        //footer
        $footer = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer')->first();

        //reponse payload
        $payload = [
            'page' => $page,
            'mainmenu' => $mainmenu,
            'footer' => $footer,
            'content' => $content,
        ];

        return view('frontend/pages/page', compact('payload', 'mainmenu', 'page', 'footer', 'content'))->render();

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