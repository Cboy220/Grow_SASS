<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Blog;
use Illuminate\Http\Request;
use Log;


class BlogsRepository{



    /**
     * The leads repository instance.
     */
    protected $blog;

    /**
     * Inject dependecies
     */
    public function __construct(Blog $blog) {
        $this->blog = $blog;
    }


        /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object blogs collection
     */
    public function search($id = '') {

        $blogs = $this->blog->newQuery();

        // all client fields
        $blogs->selectRaw('*');

        //joins
        $blogs->leftJoin('users', 'users.id', '=', 'blogs.blog_creatorid');

        //default where
        $blogs->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_blog_id')) {
            $blogs->where('blog_id', request('filter_blog_id'));
        }
        if (is_numeric($id)) {
            $blogs->where('blog_id', $id);
        }


        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $blogs->where(function ($query) {
                $query->orWhere('blog_description', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('blog_title', '=', request('search_query'));
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('blogs', request('orderby'))) {
                $blogs->orderBy(request('orderby'), request('sortorder'));
            }
        } else {
            //default sorting
            $blogs->orderBy('blog_id', 'desc');
        }

        // Get the results and return them.
        return $blogs->paginate(config('system.settings_system_pagination_limits'));
    }


       /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $blog = new $this->blog;

        //data
        $blog->blog_categoryid = request('blog_categoryid');
        $blog->blog_creatorid = auth()->id();

        //save and return id
        if ($blog->save()) {
            return $blog->blog_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$blog = $this->blog->find($id)) {
            return false;
        }

        //general
        $blog->blog_categoryid = request('blog_categoryid');

        //save
        if ($blog->save()) {
            return $blog->blog_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }


}