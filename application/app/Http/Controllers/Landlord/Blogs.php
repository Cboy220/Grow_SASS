<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Blogs\IndexResponse;
use App\Repositories\Landlord\BlogsRepository;
use Validator;

class Blogs extends Controller {

    //repositories
    protected $blogsrepo;

    public function __construct(
        BlogsRepository $blogsrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //repositories
        $this->blogsrepo = $blogsrepo;

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get blogs
        $blogs = $this->blogsrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'blogs' => $blogs,
        ];

        //show the form
        return new IndexResponse($payload);
    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function create() {

        $page = $this->pageSettings('create');

        //show the page
        return view('landlord/blogs/compose', compact('page'))->render();

    }
    

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function store() {

        //custom error messages
        $messages = [
            'foo_bar.required' => __('lang.foo') . ' - ' . __('lang.is_required'),
            'foo_bar.unique' => __('lang.foo') . ' - ' . __('lang.already_exists'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'blog_title' => [
                'required',
            ],
            'blog_text' => [
                'required',
            ],
            'blog_created' => [
                'required',
            ],
            'blog_status' => [
                'required',
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //save post
        $blog = new \App\Models\Landlord\Blog();
        $blog->blog_title = request('blog_title');
        $blog->blog_text = request('blog_text');
        $blog->blog_created = request('blog_created');
        $blog->blog_status = request('blog_status');
        $blog->blog_creatorid = auth()->id();
        $blog->save();

        //save slug title
        $blog->blog_slug = createSlug($blog->blog_id, $foo->blog_title);
        $blog->save();

        //redirect
        $jsondata['redirect_url'] = url('app-admin/blogs');

        //success
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
        
        //ajax response
        return response()->json($jsondata);
    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function edit() {

        $page = $this->pageSettings('create');

        //show the page
        return view('landlord/blogs/compose', compact('page'))->render();

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
                __('lang.blogs'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.blogs'),
            'heading' => __('lang.blogs'),
            'page' => 'blogs',
            'mainmenu_blogs' => 'active',
        ];

        if ($section == 'create') {
            $page['crumbs'] = [
                __('lang.blogs'),
                __('lang.compose'),
            ];
        }

        //return
        return $page;
    }
}