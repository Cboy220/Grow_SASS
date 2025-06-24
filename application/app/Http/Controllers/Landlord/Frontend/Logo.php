<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Frontend\Logo\EditResponse;
use App\Http\Responses\Landlord\Frontend\Logo\ShowResponse;
use App\Http\Responses\Landlord\Frontend\Logo\UpdateResponse;
use App\Repositories\Landlord\AttachmentRepository;

class Logo extends Controller {

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
            'section' => 'logo',
        ];

        //show the form
        return new ShowResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function edit() {

        //reponse payload
        $payload = [];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function update(AttachmentRepository $attachmentrepo) {

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        //validate input
        $data = [
            'directory' => request('logo_directory'),
            'filename' => request('logo_filename'),
            'logo_size' => request('logo_size'),
            'new_filename' => random_string(40) . request('logo_filename'),
        ];

        //process and save to db
        if (!$attachmentrepo->processAppLogo($data)) {
            abort(409);
        }

        //update settings
        if (request('logo_size') == 'frontend-logo') {
            $settings->settings_system_logo_frontend_name = $data['new_filename'];
        }

       //small logo
        if (request('logo_size') == 'frontend-favicon') {
            $settings->settings_favicon_frontend_filename = $data['new_filename'];
        }

        //add new version to avoid caching
        $settings->save();

        //payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
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
            'inner_menu_logo' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}