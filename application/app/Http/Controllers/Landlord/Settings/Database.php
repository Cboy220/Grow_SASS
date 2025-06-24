<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Database\ShowResponse;
use Validator;
use App\Rules\NoTags;

class Database extends Controller {

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

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'settings' => $settings,
            'section' => 'general',
        ];

        //show the form
        return new ShowResponse($payload);
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUser() {

        //custom error messages
        $messages = [
            'settings_database_direct_username.required' => __('lang.username') . '-' . __('lang.is_required'),
            'settings_database_direct_password.required' => __('lang.password') . '-' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_database_direct_username' => [
                'required',
                new NoTags,
            ],
            'settings_database_direct_password' => [
                'required',
                new NoTags,
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //



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
                __('lang.database_manager'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_menu_database_manager' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}