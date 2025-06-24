<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Pages\CreateResponse;
use App\Http\Responses\Landlord\Pages\DestroyResponse;
use App\Http\Responses\Landlord\Pages\EditResponse;
use App\Http\Responses\Landlord\Pages\IndexResponse;
use App\Http\Responses\Landlord\Pages\StoreResponse;
use App\Http\Responses\Landlord\Pages\UpdateResponse;
use App\Repositories\Landlord\PagesRepository;
use Validator;

class Pages extends Controller {

    /**
     * The item repository instance.
     */
    protected $pagerepo;

    public function __construct(PagesRepository $pagerepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->pagerepo = $pagerepo;

    }

    /**
     * Display a listing of pages
     * @return blade view | ajax view
     */
    public function index() {

        //get items
        $pages = $this->pagerepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('pages'),
            'pages' => $pages,
            'count' => $pages->count(),
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'content' => [],
        ];

        return new CreateResponse($payload);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [
            'page_title.required' => __('lang.title') . ' - ' . __('lang.is_required'),
            'html_page_content.required' => __('lang.page_content') . ' - ' . __('lang.is_required'),
            'page_permanent_link.required' => __('lang.permanent_link') . ' - ' . __('lang.is_required'),
            'page_status.required' => __('lang.status') . ' - ' . __('lang.is_required'),
            'page_meta_title.required' => __('lang.meta_title') . ' - ' . __('lang.is_required'),
            'page_meta_description.required' => __('lang.meta_description') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'page_title' => [
                'required',
            ],
            'html_page_content' => [
                'required',
            ],
            'page_permanent_link' => [
                'required',
            ],
            'page_status' => [
                'required',
            ],
            'page_meta_title' => [
                'required',
            ],
            'page_meta_description' => [
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

        //fix the permalink
        $page_permanent_link = pagePermalinkSlug(request('page_permanent_link'));
        if ($page_permanent_link == '') {
            abort(409, __('lang.permanent_link') . ' - ' . __('lang.is_required'));
        }

        //check it there is no page with similar permalink
        if (\App\Models\Landlord\Page::Where('page_permanent_link', $page_permanent_link)->exists()) {
            abort(409, __('lang.permalink_exists'));
        }

        //store record
        $content = new \App\Models\Landlord\Page();
        $content->page_creatorid = auth()->id();
        $content->page_uniqueid = str_unique();
        $content->page_title = request('page_title');
        $content->page_content = request('html_page_content');
        $content->page_permanent_link = $page_permanent_link;
        $content->page_meta_title = request('page_meta_title');
        $content->page_meta_description = request('page_meta_description');
        $content->page_show_title = (request('page_show_title') == 'on') ? 'yes' : 'no';
        $content->page_status = request('page_status');
        $content->save();

        //payload
        $payload = [
            'content' => $content,
        ];

        //render
        return new StoreResponse($payload);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the foo
        if (!$content = \App\Models\Landlord\Page::Where('page_id', $id)->first()) {
            abort(404);
        }

        //preview page link
        $content->page_preview_link = 'https://' . config('system.settings_frontend_domain') . '/page/' . $content->page_permanent_link . '?preview=' . $content->page_uniqueid;

        //payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'content' => $content,
        ];

        return new EditResponse($payload);
    }

    /**
     * update the resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //get the foo
        if (!$content = \App\Models\Landlord\Page::Where('page_id', $id)->first()) {
            abort(404);
        }

        //current meta link
        $current_page_permanent_link = $content->page_permanent_link;

        //custom error messages
        $messages = [
            'page_title.required' => __('lang.title') . ' - ' . __('lang.is_required'),
            'html_page_content.required' => __('lang.page_content') . ' - ' . __('lang.is_required'),
            'page_permanent_link.required' => __('lang.permanent_link') . ' - ' . __('lang.is_required'),
            'page_status.required' => __('lang.status') . ' - ' . __('lang.is_required'),
            'page_meta_title.required' => __('lang.meta_title') . ' - ' . __('lang.is_required'),
            'page_meta_description.required' => __('lang.meta_description') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'page_title' => [
                'required',
            ],
            'html_page_content' => [
                'required',
            ],
            'page_permanent_link' => [
                'required',
            ],
            'page_status' => [
                'required',
            ],
            'page_meta_title' => [
                'required',
            ],
            'page_meta_description' => [
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

        //fix the permalink
        $page_permanent_link = pagePermalinkSlug(request('page_permanent_link'));
        if ($page_permanent_link == '') {
            abort(409, __('lang.permanent_link') . ' - ' . __('lang.is_required'));
        }

        //check it there is no page with similar permalink
        if (\App\Models\Landlord\Page::Where('page_permanent_link', $page_permanent_link)->WhereNotIN('page_id', [$id])->exists()) {
            abort(409, __('lang.permalink_exists'));
        }

        //update record
        $content->page_title = request('page_title');
        $content->page_content = request('html_page_content');
        $content->page_permanent_link = $page_permanent_link;
        $content->page_meta_title = request('page_meta_title');
        $content->page_meta_description = request('page_meta_description');
        $content->page_show_title = (request('page_show_title') == 'on') ? 'yes' : 'no';
        $content->page_status = request('page_status');
        $content->save();

        //update menus that use this link
        \App\Models\Landlord\Frontend::where('frontend_group', 'main-menu')->where('frontend_data_2', "/page/$current_page_permanent_link")
            ->update(['frontend_data_2' => "/page/$page_permanent_link"]);

        //payload
        $payload = [
            'content' => $content,
        ];

        //render
        return new UpdateResponse($payload);

    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the foo
        if (!$page = \App\Models\Landlord\Page::Where('page_id', $id)->first()) {
            abort(404);
        }

        //delete the page
        $page->delete();

        //payload
        $payload = [
            'id' => $id,
        ];

        //render
        return new DestroyResponse($payload);

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
                __('lang.pages'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.frontend'),
            'page' => 'landlord-settings',
            'mainmenu_frontend' => 'active',
            'inner_menu_pages' => 'active',
        ];

        if ($section == 'create') {
            $page['visibility_edit_page_preview_button'] = 'hidden';
            $page['mode'] = 'create';
        }

        if ($section == 'edit') {
            $page['mode'] = 'edit';
        }

        //show
        config(['visibility.left_inner_menu' => 'frontend']);

        //return
        return $page;
    }
}