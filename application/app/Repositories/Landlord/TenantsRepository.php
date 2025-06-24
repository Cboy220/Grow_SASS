<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class TenantsRepository {

    /**
     * The leads repository instance.
     */
    protected $tenant;

    /**
     * Inject dependecies
     */
    public function __construct(Tenant $tenant) {
        $this->tenant = $tenant;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object tenants collection
     */
    public function search($id = '') {

        $tenants = $this->tenant->newQuery();

        // all client fields
        $tenants->selectRaw('*');


        //joins
        $tenants->leftJoin('subscriptions', function($join) {
            $join->on('subscriptions.subscription_customerid', '=', 'tenants.tenant_id')
                 ->whereNotIn('subscriptions.subscription_status', ['cancelled'])
                 ->limit(1);
        });
        $tenants->leftJoin('packages', 'packages.package_id', '=', 'subscriptions.subscription_package_id');

        //default where
        $tenants->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_tenant_id')) {
            $tenants->where('tenant_id', request('filter_tenant_id'));
        }
        if (is_numeric($id)) {
            $tenants->where('tenant_id', $id);
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $tenants->where(function ($query) {
                $query->orWhere('tenant_status', '=', request('search_query'));
                $query->orWhere('tenant_email', '=', request('search_query'));
                $query->orWhere('subdomain', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('package_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('tenant_name', 'LIKE', '%' . request('search_query') . '%');
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('tenants', request('orderby'))) {
                $tenants->orderBy(request('orderby'), request('sortorder'));
            }
        } else {
            //default sorting
            $tenants->orderBy('tenant_id', 'asc');
        }

        // Get the results and return them.
        return $tenants->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $tenant = new $this->tenants;

        //data
        $tenant->tenant_categoryid = request('tenant_categoryid');
        $tenant->tenant_creatorid = auth()->id();

        //save and return id
        if ($tenant->save()) {
            return $tenant->tenant_id;
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
        if (!$tenant = $this->tenants->find($id)) {
            return false;
        }

        //general
        $tenant->tenant_categoryid = request('tenant_categoryid');

        //save
        if ($tenant->save()) {
            return $tenant->tenant_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }

    /**
     * various feeds for ajax auto complete
     * @param string $type (company_name)
     * @param string $searchterm
     * @return object tenant model object
     */
    public function autocompleteFeed( $searchterm = '') {

        //validation
        if ($searchterm == '') {
            return [];
        }

        //start
        $query = $this->tenant->newQuery();

        $query->selectRaw('tenant_name AS value, tenant_id AS id');
        $query->where('tenant_name', 'LIKE', '%' . $searchterm . '%');

        //return
        return $query->get();
    }

}