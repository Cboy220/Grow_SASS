<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class Home extends Controller {

    /**
     * The foo repository instance.
     */
    protected $foorepo;

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
            return view('errors/saas/404')->render();
        }

        //menus
        $mainmenu = \App\Models\Landlord\Frontend::Where('frontend_group', 'main-menu')->orderBy('frontend_name', 'asc')->get();

        //get section
        $hero = \App\Models\Landlord\Frontend::Where('frontend_name', 'hero-header')->first();

        //section 1
        $section1_title = \App\Models\Landlord\Frontend::Where('frontend_name', 'section-1-title')->first();
        $section1_content = \App\Models\Landlord\Frontend::Where('frontend_group', 'section-1')->get();

        //sections
        for ($i = 2; $i <= 4; $i++) {
            $section = "section$i";
            $$section = \App\Models\Landlord\Frontend::Where('frontend_name', "section-$i")->first();
        }

        //splash
        $splash_title = \App\Models\Landlord\Frontend::Where('frontend_name', 'section-splash-title')->first();
        for ($i = 1; $i <= 6; $i++) {
            $splash = "splash$i";
            $$splash = \App\Models\Landlord\Frontend::Where('frontend_name', "section-splash-$i")->first();
        }

        //section 5
        $section5_title = \App\Models\Landlord\Frontend::Where('frontend_name', 'section-5-title')->first();
        $section5_content = \App\Models\Landlord\Frontend::Where('frontend_group', 'section-5')->get();

        //footer
        $footer = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer')->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'mainmenu' => $mainmenu,
            'hero' => $hero,
            'section1_title' => $section1_title,
            'section1_content' => $section1_content,
            'section2' => $section2,
            'section3' => $section3,
            'section4' => $section4,
            'section5_title' => $section5_title,
            'section5_content' => $section5_content,
            'splash_title' => $splash_title,
            'splash1' => $splash1,
            'splash2' => $splash2,
            'splash3' => $splash3,
            'splash4' => $splash4,
            'splash5' => $splash5,
            'splash6' => $splash6,
        ];

        return view('frontend/home/page', compact('payload', 'mainmenu', 'footer'))->render();

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