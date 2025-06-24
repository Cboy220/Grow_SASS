<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\EnvSaaSRepository;
use Illuminate\Support\Facades\Validator;

class Start extends Controller {

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
    public function show() {

        $page = $this->pageSettings();

        //get settings
        $settings = \App\Models\Landlord\Settings::Where('settings_id', 'default')->first();

        return view('landlord/frontend/start/page', compact('page', 'settings'))->render();

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function update(EnvSaaSRepository $envrepo) {

        //custom error messages
        $messages = [
            'settings_frontend_status.required' => __('lang.frontend_status') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_frontend_status' => [
                'required',
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

        //update .env file with the frontend domain name
        if (!$envrepo->updateFrontendDomain(cleanURLDomain(request('settings_frontend_domain')))) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //reset existing account owner
        \App\Models\Landlord\Settings::where('settings_id', 'default')
            ->update([
                'settings_frontend_status' => request('settings_frontend_status'),
                'settings_frontend_domain' => cleanURLDomain(request('settings_frontend_domain')),
            ]);

        //ajax response
        return response()->json(array(
            'notification' => [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ],
            'skip_dom_reset' => true,
        ));
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
                __('lang.start'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            'inner_menu_start' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}