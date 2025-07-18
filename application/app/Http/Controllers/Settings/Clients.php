<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for clients settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Clients\IndexResponse;
use App\Http\Responses\Settings\Clients\UpdateResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Clients extends Controller {

        /**
     * The settings repository instance.
     */
    protected $settingsrepo;

    public function __construct(SettingsRepository $settingsrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->settingsrepo = $settingsrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $settings = \App\Models\Settings::find(1);
        $settings2 = \App\Models\Settings2::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'settings2' => $settings2,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        $settings = \App\Models\Settings::find(1);
        $settings2 = \App\Models\Settings2::find(1);

        //custom error messages
        $messages = [];

        //update
        if (!$this->settingsrepo->updateClients()) {
            abort(409);
        }

        //update other settings
        $settings2->settings2_importing_clients_duplicates_email = request('settings2_importing_clients_duplicates_email') == 'on' ? 'yes' : 'no';
        $settings2->settings2_importing_clients_duplicates_telephone = request('settings2_importing_clients_duplicates_telephone') == 'on' ? 'yes' : 'no';
        $settings2->settings2_importing_clients_duplicates_company = request('settings2_importing_clients_duplicates_company') == 'on' ? 'yes' : 'no';
        $settings2->save();


        //reponse payload
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

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.clients'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' =>  __('lang.settings'),
            'heading' =>  __('lang.clients'),
            'settingsmenu_feedback' => 'active',
        ];
        return $page;
    }

}
