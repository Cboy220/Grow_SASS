<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;

class System extends Controller {

    public function __construct(
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function show() {

       $data = [
           'landlord_database' => env('LANDLORD_DB_DATABASE'),
           'php_version' => phpversion(),
           'memory_limit' =>ini_get('memory_limit'),
           'upload_size_limit' => ini_get('upload_max_filesize'),
           'mysql_creation_type' => env('DB_METHOD'),
           'crm_version' => config('system.settings_version'),
       ];

        //reponse payload
        $page = $this->pageSettings('index');

        return view('landlord/settings/sections/info/page', compact('page', 'data'))->render();
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
                __('lang.settings'),
                __('lang.system_information'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_menu_system' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}