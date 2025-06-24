<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Subscription;
use App\Repositories\Landlord\StripeRepository;
use Illuminate\Http\Request;
use Log;

class SubscriptionsRepository {

    //repos
    protected $subscription;
    protected $striperepo;

    /**
     * Inject dependecies
     */
    public function __construct(
        Subscription $subscription,
        StripeRepository $striperepo) {

        $this->subscription = $subscription;
        $this->striperepo = $striperepo;

    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object subscriptions collection
     */
    public function search($id = '') {

        $subscriptions = $this->subscription->newQuery();

        // all client fields
        $subscriptions->selectRaw('*');

        //joins
        $subscriptions->leftJoin('users', 'users.id', '=', 'subscriptions.subscription_creatorid');
        $subscriptions->leftJoin('tenants', 'tenants.tenant_id', '=', 'subscriptions.subscription_customerid');

        //default where
        $subscriptions->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_subscription_id')) {
            $subscriptions->where('subscription_id', request('filter_subscription_id'));
        }
        if (is_numeric($id)) {
            $subscriptions->where('subscription_id', $id);
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $subscriptions->where(function ($query) {
                $query->orWhere('subscription_gateway_id', '=', request('search_query'));
                $query->orWhere('subscription_gateway_name', '=', request('search_query'));
                $query->orWhere('subscription_date_renewed', '=', request('search_query'));
                $query->orWhere('subscription_date_started', '=', request('search_query'));
                $query->orWhere('subscription_final_amount', '=', request('search_query'));
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('subscriptions', request('orderby'))) {
                $subscriptions->orderBy(request('orderby'), request('sortorder'));
            }
        } else {
            //default sorting
            $subscriptions->orderBy('subscription_id', 'desc');
        }

        // Get the results and return them.
        return $subscriptions->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $subscription = new $this->subscriptions;

        //data
        $subscription->subscription_categoryid = request('subscription_categoryid');
        $subscription->subscription_creatorid = auth()->id();

        //save and return id
        if ($subscription->save()) {
            return $subscription->subscription_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * change a customers subscription as follows
     *   (1) delete existing subscription
     *   (2) create a new subscription
     *   (3) update the tenant database with the new subscription status
     *
     * @param array $data data payload
     * @return mixed int|bool
     */
    public function changeCustomersPlan($data) {

        //changing customers subsccription
        Log::info("changing customers subscription plan - started", ['process' => '[change-customers-subscription-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //get current subscription from database
        if (!$customer = \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', $data['customer_id'])->first()) {
            Log::error("customer could not be found in the tenant database", ['process' => '[change-customers-subscription-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //get the package
        if (!$package = \App\Models\Landlord\Package::On('landlord')->Where('package_id', $data['package_id'])->first()) {
            Log::error("the package could not be found", ['process' => '[change-customers-subscription-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //remove existing subscriptions (set it to
        if ($subscriptions = \App\Models\Landlord\Subscription::On('landlord')->Where('subscription_customerid', $data['customer_id'])->get()) {

            foreach ($subscriptions as $subscription) {

                //queue for cancelling at the payment gateway (will be done via cronjob)
                if ($subscription->subscription_type == 'paid' && $subscription->subscription_payment_method == 'automatic') {
                    if ($subscription->subscription_status == 'active' || $subscription->subscription_status == 'failed') {
                        $schedule = new \App\Models\Landlord\Scheduled();
                        $schedule->setConnection('landlord');
                        $schedule->scheduled_gateway = $subscription->subscription_gateway_name;
                        $schedule->scheduled_type = 'cancel-subscription';
                        $schedule->scheduled_payload_1 = $subscription->subscription_gateway_id;
                        $schedule->scheduled_payload_2 = $subscription->subscription_checkout_reference_2;
                        $schedule->scheduled_payload_3 = $subscription->subscription_checkout_reference_3;
                        $schedule->scheduled_payload_4 = $subscription->subscription_checkout_reference_4;
                        $schedule->scheduled_payload_5 = $subscription->subscription_checkout_reference_5;
                        $schedule->save();
                    }
                }

                //control
                $archive = true;

                //if it was a free plan - delete it
                if ($subscription->subscription_type == 'free') {
                    $subscription->delete();
                    $archive = false;
                }

                //if it had no previous payments - delete it
                if (\App\Models\Landlord\Payment::On('landlord')->Where('payment_subscription_id', $subscription->subscription_id)->doesntExist()) {
                    $subscription->delete();
                    $archive = false;
                }

                //archive subscription record - this subscription was once active and paid - let us keep it in our database
                if ($archive) {
                    $subscription->subscription_archived = 'yes';
                    $subscription->subscription_status = 'cancelled';
                    $subscription->save();
                }
            }
        }

        //paid packages - free trial
        if ($package->package_subscription_options == 'paid' && $data['free_trial'] == 'yes') {
            $subscription_status = 'free-trial';
            $free_trial = 'yes';
            $subscription_trial_end = \Carbon\Carbon::now()->addDays($data['free_trial_days'])->format('Y-m-d');
            $subscription_amount = ($data['billing_cycle'] == 'monthly') ? $package->package_amount_monthly : $package->package_amount_yearly;
            $subscription_date_started = null;
        }

        //paid packages - free trial
        if ($package->package_subscription_options == 'paid' && $data['free_trial'] == 'no') {
            $subscription_status = 'awaiting-payment';
            $free_trial = 'no';
            $subscription_trial_end = null;
            $subscription_amount = ($data['billing_cycle'] == 'monthly') ? $package->package_amount_monthly : $package->package_amount_yearly;
            $subscription_date_started = null;
        }

        //free packages
        if ($package->package_subscription_options == 'free') {
            $subscription_status = 'active';
            $free_trial = 'no';
            $subscription_trial_end = null;
            $subscription_amount = 0;
            $subscription_date_started = now();
        }

        $subscription = new \App\Models\Landlord\Subscription();
        $subscription->setConnection('landlord');
        $subscription->subscription_creatorid = auth()->id();
        $subscription->subscription_uniqueid = str_unique();
        $subscription->subscription_customerid = $customer->tenant_id;
        $subscription->subscription_type = $package->package_subscription_options;
        $subscription->subscription_payment_method = $data['billing_type'];
        $subscription->subscription_amount = $subscription_amount;
        $subscription->subscription_trial_end = $subscription_trial_end;
        $subscription->subscription_date_started = $subscription_date_started;
        $subscription->subscription_package_id = $package->package_id;
        $subscription->subscription_status = $subscription_status;
        $subscription->subscription_gateway_billing_cycle = $data['billing_cycle'];
        $subscription->save();

        //change customer status
        $customer->tenant_status = $subscription_status;
        $customer->save();

        Log::info("changing customers subscription plan - completed", ['process' => '[change-customers-subscription-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription' => $subscription]);

        return $subscription;

    }

    /**
     * a new subscription payment has been received.
     *   - mark the subscription as paid
     *   -
     * @param int $id record id
     * @return mixed int|bool
     */
    public function newPaymentReceived($data) {

    }

}