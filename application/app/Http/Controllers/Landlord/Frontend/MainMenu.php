<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Frontend;

use App\Http\Controllers\Controller;

class MainMenu extends Controller {

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
    public function index() {

        $page = $this->pageSettings();

        //items
        $items = \App\Models\Landlord\Frontend::Where('frontend_group', "main-menu")->orderBy('frontend_name', 'asc')->get();

        return view('landlord/frontend/mainmenu/table/table', compact('page', 'items'))->render();

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //get all internal pages
        $internal_pages = \App\Models\Landlord\Page::OrderBy('page_title', 'ASC')->get();

        //default data
        $payload = [
            'link_type' => 'internal',
        ];

        //page
        $html = view('landlord/frontend/mainmenu/modals/add-edit', compact('payload', 'internal_pages'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLandlordMainMenuEdit',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //get position of this menu item
        if ($last = \App\Models\Landlord\Frontend::where('frontend_group', 'main-menu')->orderBy('frontend_name', 'desc')->first()) {
            $position = $last->frontend_name + 1;
        } else {
            $position = 1;
        }

        //validation
        if (!request()->filled('frontend_data_1')) {
            abort(409, __('lang.name') . ' - ' . __('lang.is_required'));
        }

        //validation
        if (request('frontend_data_3') == 'manual' && !request()->filled('link_manual')) {
            abort(409, __('lang.url') . ' - ' . __('lang.is_required'));
        }

        //validation
        if (request('frontend_data_3') == 'internal' && !request()->filled('link_internal')) {
            abort(409, __('lang.page') . ' - ' . __('lang.is_required'));
        }

        //store record
        $menu = new \App\Models\Landlord\Frontend();
        $menu->frontend_group = 'main-menu';
        $menu->frontend_data_1 = request('frontend_data_1');
        $menu->frontend_data_2 = (request('frontend_data_3') == 'internal') ? request('link_internal') : request('link_manual');
        $menu->frontend_data_3 = request('frontend_data_3');
        $menu->frontend_data_4 = 'parent';
        $menu->frontend_data_6 = (request('frontend_data_3') == 'manual') ? request('link_target') : 'same_window';
        $menu->frontend_name = $position;
        $menu->save();

        //redirect to packages page
        $jsondata['redirect_url'] = url('app-admin/frontend/mainmenu');

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the record
        $menu = \App\Models\Landlord\Frontend::where('frontend_id', $id)->first();

        //delete record
        $menu->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#item_' . $id,
            'action' => 'slideup-slow-remove',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the foo
        if (!$menu = \App\Models\Landlord\Frontend::where('frontend_group', 'main-menu')->where('frontend_id', $id)->first()) {
            abort(404);
        }
    
        //get all internal pages
        $internal_pages = \App\Models\Landlord\Page::OrderBy('page_title', 'ASC')->get();

        //default data
        $payload = [
            'link_type' => $menu->frontend_data_3,
        ];

        //page
        $html = view('landlord/frontend/mainmenu/modals/add-edit', compact('payload', 'menu', 'internal_pages'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLandlordMainMenuEdit',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to update a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //validation
        if (!request()->filled('frontend_data_1')) {
            abort(409, __('lang.name') . ' - ' . __('lang.is_required'));
        }

        //validation
        if (request('frontend_data_3') == 'manual' && !request()->filled('link_manual')) {
            abort(409, __('lang.url') . ' - ' . __('lang.is_required'));
        }

        //validation
        if (request('frontend_data_3') == 'internal' && !request()->filled('link_internal')) {
            abort(409, __('lang.page') . ' - ' . __('lang.is_required'));
        }

        //get the foo
        if (!$menu = \App\Models\Landlord\Frontend::where('frontend_group', 'main-menu')->where('frontend_id', $id)->first()) {
            abort(404);
        }

        //update record
        $menu->frontend_data_1 = request('frontend_data_1');
        $menu->frontend_data_2 = (request('frontend_data_3') == 'internal') ? request('link_internal') : request('link_manual');
        $menu->frontend_data_3 = request('frontend_data_3');
        $menu->frontend_data_6 = (request('frontend_data_3') == 'manual') ? request('link_target') : 'same_window';
        $menu->save();

        //redirect to packages page
        $jsondata['redirect_url'] = url('app-admin/frontend/mainmenu');

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * Update a menu position
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function updatePositions() {

        //reposition each task status
        $i = 1;
        foreach (request('sort-menu') as $key => $id) {
            if (is_numeric($id)) {
                \App\Models\Landlord\Frontend::where('frontend_id', $id)->update(['frontend_name' => $i]);
            }
            $i++;
        }

        //retun simple success json
        //return response()->json('success', 200);
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
                __("lang.main_menu"),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            "inner_menu_main_menu" => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}