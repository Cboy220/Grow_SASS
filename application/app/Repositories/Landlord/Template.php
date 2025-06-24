<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Foo;
use Illuminate\Http\Request;
use Log;


class FoosRepository{



    /**
     * The leads repository instance.
     */
    protected $foo;

    /**
     * Inject dependecies
     */
    public function __construct(Foo $foo) {
        $this->foo = $foo;
    }


        /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object foos collection
     */
    public function search($id = '') {

        $foos = $this->foo->newQuery();

        // all client fields
        $foos->selectRaw('*');

        //joins
        $foos->leftJoin('users', 'users.id', '=', 'foos.foo_creatorid');

        //default where
        $foos->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_foo_id')) {
            $foos->where('foo_id', request('filter_foo_id'));
        }
        if (is_numeric($id)) {
            $foos->where('foo_id', $id);
        }


        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $foos->where(function ($query) {
                $query->orWhere('foo_description', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('foo_rate', '=', request('search_query'));
                $query->orWhereHas('category', function ($q) {
                    $q->where('category_name', 'LIKE', '%' . request('search_query') . '%');
                });
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('foos', request('orderby'))) {
                $foos->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'category':
                $foos->orderBy('category_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $foos->orderBy('foo_id', 'desc');
        }

        //eager load
        $foos->with(['category']);

        // Get the results and return them.
        return $foos->paginate(config('system.settings_system_pagination_limits'));
    }


       /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $foo = new $this->foo;

        //data
        $foo->foo_categoryid = request('foo_categoryid');
        $foo->foo_creatorid = auth()->id();

        //save and return id
        if ($foo->save()) {
            return $foo->foo_id;
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
        if (!$foo = $this->foo->find($id)) {
            return false;
        }

        //general
        $foo->foo_categoryid = request('foo_categoryid');

        //save
        if ($foo->save()) {
            return $foo->foo_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }


}