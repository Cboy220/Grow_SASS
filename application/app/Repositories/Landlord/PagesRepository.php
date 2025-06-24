<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Page;
use Illuminate\Http\Request;

class PagesRepository {

    /**
     * The leads repository instance.
     */
    protected $page;

    /**
     * Inject dependecies
     */
    public function __construct(Page $page) {
        $this->page = $page;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object pages collection
     */
    public function search($id = '') {

        $pages = $this->page->newQuery();

        // all client fields
        $pages->selectRaw('*');

        //joins
        $pages->leftJoin('users', 'users.id', '=', 'pages.page_creatorid');

        //default where
        $pages->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_page_id')) {
            $pages->where('page_id', request('filter_page_id'));
        }
        if (request()->filled('filter_page_slug')) {
            $pages->where('page_slug', request('filter_page_slug'));
        }
        if (is_numeric($id)) {
            $pages->where('page_id', $id);
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('pages', request('orderby'))) {
                $pages->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'creator':
                $pages->orderBy('first_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $pages->orderBy('page_id', 'desc');
        }

        // Get the results and return them.
        return $pages->paginate(config('system.settings_system_pagination_limits'));
    }
}