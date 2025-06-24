<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class Pricing extends Controller {

    /**
     * The foo repository instance.
     */
    protected $foorepo;

    public function __construct() {

        //parent
        parent::__construct();

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

        //footer
        $footer = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer')->first();

        //menus
        $packages = \App\Models\Landlord\Package::Where('package_visibility', 'visible')
            ->Where('package_status', 'active')
            ->orderBy('package_amount_monthly', 'asc')->get();

        //page content
        $content = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-pricing')->first();

        //cta panel
        $cta = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-footer-cta')->first();

        //reponse payload
        $payload = [
            'show_footer_cta' => true,
            'cta' => $cta,
        ];

        return view('frontend/pricing/page', compact('payload', 'packages', 'mainmenu', 'content', 'footer', 'cta'))->render();

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