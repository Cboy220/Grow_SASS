<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Frontend;

use App\Http\Controllers\Controller;
use Validator;

class Faq extends Controller {

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

        //get section
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-faq')->first();

        //items
        $faqs = \App\Models\Landlord\Faq::orderBy('faq_position', 'asc')->get();

        return view('landlord/frontend/faq/table/table', compact('page', 'faqs', 'section'))->render();

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //page
        $html = view('landlord/frontend/faq/modals/add-edit')->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLandlordFAQEdit',
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

        //custom error messages
        $messages = [
            'faq_title.required' => __('lang.title') . ' - ' . __('lang.is_required'),
            'faq_content.required' => __('lang.content') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'faq_title' => [
                'required',
            ],
            'faq_content' => [
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

        //get position of this menu item
        if ($last = \App\Models\Landlord\Faq::orderBy('faq_position', 'desc')->first()) {
            $position = $last->faq_position + 1;
        } else {
            $position = 1;
        }

        //store record
        $faq = new \App\Models\Landlord\Faq();
        $faq->faq_title = request('faq_title');
        $faq->faq_content = request('faq_content');
        $faq->faq_position = $position;
        $faq->save();

        //redirect to packages page
        $jsondata['redirect_url'] = url('app-admin/frontend/faq');

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
        $menu = \App\Models\Landlord\Faq::where('faq_id', $id)->first();

        //delete record
        $menu->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#faq_' . $id,
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

        //get the faq
        if (!$faq = \App\Models\Landlord\Faq::where('faq_id', $id)->first()) {
            abort(404);
        }

        //page
        $html = view('landlord/frontend/faq/modals/add-edit', compact('faq'))->render();
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

        //get the faq
        if (!$faq = \App\Models\Landlord\Faq::where('faq_id', $id)->first()) {
            abort(404);
        }

        //custom error messages
        $messages = [
            'faq_title.required' => __('lang.title') . ' - ' . __('lang.is_required'),
            'faq_content.required' => __('lang.content') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'faq_title' => [
                'required',
            ],
            'faq_content' => [
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

        //update record
        $faq->faq_title = request('faq_title');
        $faq->faq_content = request('faq_content');
        $faq->save();

        //redirect to packages page
        $jsondata['redirect_url'] = url('app-admin/frontend/faq');

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
        foreach (request('sort-faq') as $key => $id) {
            if (is_numeric($id)) {
                \App\Models\Landlord\Faq::where('faq_id', $id)->update(['faq_position' => $i]);
            }
            $i++;
        }

        //retun simple success json
        //return response()->json('success', 200);
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function updateDetails() {

        //get the item
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-faq')->first();

        //custom error messages
        $messages = [
            'frontend_data_1.required' => __('lang.title') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'frontend_data_1' => [
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

        //update record
        $section->frontend_data_1 = request('frontend_data_1');
        $section->frontend_data_2 = request('frontend_data_2');
        $section->save();

        //redirect back
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
        $jsondata['redirect_url'] = url('app-admin/frontend/faq');

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
                __('lang.frontend'),
                __("lang.main_menu"),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            "inner_menu_faq" => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}