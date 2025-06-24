<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\Profile\StoreUpdate;

class Profile extends Controller {

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

        $user = \App\Models\User::Where('id', auth()->id())->first();

        $html = view('landlord/profile/edit', compact('user'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXUpdateAdminProfile',
        ];

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdate $request) {

        //get the item
        if (!$user = \App\Models\User::Where('id', auth()->id())->first()) {
            abort(404);
        }

        //update record
        $user->first_name = request('first_name');
        $user->last_name = request('last_name');
        if (request()->filled('password')) {
            $user->email = request('email');
        }
        if (request()->filled('password')) {
            $user->password = bcrypt(request('password'));
        }
        $user->save();

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        //update name on topnav
        $jsondata['dom_html'][] = [
            'selector' => '#topnav_username',
            'action' => 'replace',
            'value' => request('first_name'),
        ];

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

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
                __('lang.foos'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.foos'),
            'heading' => __('lang.foos'),
            'page' => 'foos',
            'mainmenu_foos' => 'active',
        ];

        //return
        return $page;
    }
}