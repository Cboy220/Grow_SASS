<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PackagesRepository {

    /**
     * The leads repository instance.
     */
    protected $package;

    /**
     * Inject dependecies
     */
    public function __construct(Package $package) {
        $this->package = $package;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object packages collection
     */
    public function search($id = '') {

        $packages = $this->package->newQuery();

        // all client fields
        $packages->selectRaw('*');

        //joins
        $packages->leftJoin('users', 'users.id', '=', 'packages.package_creatorid');

        //default where
        $packages->whereRaw("1 = 1");

        //count al tasks
        $packages->selectRaw("(SELECT COUNT(*)
                                      FROM subscriptions
                                      WHERE subscription_package_id = packages.package_id)
                                      AS count_subscriptions");
        //filters: id
        if (request()->filled('filter_package_id')) {
            $packages->where('package_id', request('filter_package_id'));
        }
        if (is_numeric($id)) {
            $packages->where('package_id', $id);
        }

        //status
        if (request()->filled('package_status')) {
            $packages->where('package_status', request('package_status'));
        } else {
            $packages->where('package_status', 'active');
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $packages->where(function ($query) {
                $query->orWhere('package_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('package_amount', '=', request('search_query'));
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('packages', request('orderby'))) {
                $packages->orderBy(request('orderby'), request('sortorder'));
            }
        } else {
            //default sorting
            $packages->orderBy('package_name', 'asc');
        }

        // Get the results and return them.
        return $packages->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * schdeule plans for deletion at the payment gateway
     *
     */
    public function schedulePlanDeletion($package) {

        //schdule stripe monthly [plan/price] for deletion
        if ($package->package_gateway_stripe_price_monthly != '') {
            $scheduled = new \App\Models\Landlord\Scheduled();
            $scheduled->scheduled_gateway = 'stripe';
            $scheduled->scheduled_type = 'delete-plan';
            $scheduled->scheduled_payload_1 = $package->package_gateway_stripe_price_monthly;
            $scheduled->save();
        }

        //schdule stripe monthly [product] for deletion
        if ($package->package_gateway_stripe_price_monthly != '') {
            $scheduled = new \App\Models\Landlord\Scheduled();
            $scheduled->scheduled_gateway = 'stripe';
            $scheduled->scheduled_type = 'delete-plan';
            $scheduled->scheduled_payload_1 = $package->package_gateway_stripe_product_monthly;
            $scheduled->save();
        }

        //schdule stripe yearly [plan/price] for deletion
        if ($package->package_gateway_stripe_price_yearly != '') {
            $scheduled = new \App\Models\Landlord\Scheduled();
            $scheduled->scheduled_gateway = 'stripe';
            $scheduled->scheduled_type = 'delete-plan';
            $scheduled->scheduled_payload_1 = $package->package_gateway_stripe_price_yearly;
            $scheduled->save();
        }

        //clean-up paypal - monthly plan
        if ($package->package_gateway_paypal_plan_monthly != '') {
            $scheduled = new \App\Models\Landlord\Scheduled();
            $scheduled->scheduled_gateway = 'paypal';
            $scheduled->scheduled_type = 'delete-plan';
            $scheduled->scheduled_payload_1 = $package->package_gateway_paypal_plan_monthly;
            $scheduled->save();
        }

        //clean-up paypal - yearly plan
        if ($package->package_gateway_paypal_plan_yearly != '') {
            $scheduled = new \App\Models\Landlord\Scheduled();
            $scheduled->scheduled_gateway = 'paypal';
            $scheduled->scheduled_type = 'delete-plan';
            $scheduled->scheduled_payload_1 = $package->package_gateway_paypal_plan_yealy;
            $scheduled->save();
        }
    }
}