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

class Faq extends Controller {


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

        //cta panel
        $faqs = \App\Models\Landlord\Faq::OrderBy('faq_position', 'ASC')->get();

        //cta panel
        $cta = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer-cta')->first();

        //footer
        $footer = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer')->first();

        //page content
        $content = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-faq')->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'mainmenu' => $mainmenu,
            'faqs' => $faqs,
            'footer' => $footer,
            'show_footer_cta' => true,
            'content' => $content
        ];

        return view('frontend/faq/page', compact('payload', 'mainmenu', 'faqs', 'cta', 'footer', 'content'))->render();

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