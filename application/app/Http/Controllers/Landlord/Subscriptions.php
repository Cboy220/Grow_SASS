<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Subscriptions\IndexResponse;
use App\Repositories\Landlord\SubscriptionsRepository;
use DB;
use Exception;
use Illuminate\Support\Str;
use Log;
use Spatie\Multitenancy\Models\Tenant;
use Validator;

class Subscriptions extends Controller {

    //repositories
    protected $subscriptionsrepo;

    public function __construct(
        SubscriptionsRepository $subscriptionsrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //repositories
        $this->subscriptionsrepo = $subscriptionsrepo;

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get customers
        $subscriptions = $this->subscriptionsrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'subscriptions' => $subscriptions,
        ];

        //show the form
        return new IndexResponse($payload);
    }

    /**
     * Show detailed information about the subscription
     * @return blade view | ajax view
     */
    public function subscriptionInfo($id) {

        //get the subscription
        if (!$subscription = \App\Models\Landlord\Subscription::Where('subscription_id', $id)->first()) {
            abort(404);
        }

        //page
        $html = view('landlord/subscriptions/modal/info', compact('subscription'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //render
        return response()->json($jsondata);
    }

    /**
     * show the form to create a new subscription or change a subscription
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createEditPlan($id) {

        //get the customer
        if (!$customer = \App\Models\Landlord\Tenant::Where('tenant_id', $id)->first()) {
            abort(409, __('lang.customer_could_not_be_found'));
        }

        //if customer has existing subscription
        if ($subscription = \App\Models\Landlord\Subscription::Where('subscription_customerid', $id)->where('subscription_archived', 'no')->first()) {
            //check if we have any alternative plans to change to
            if (!$packages = \App\Models\Landlord\Package::Where('package_status', 'active')->WhereNotIN('package_id', [$subscription->subscription_package_id])->orderBy('package_name', 'DESC')->get()) {
                abort(409, __('lang.no_alternative_plans_found'));
            }
        } else {
            //get all packages
            if (!$packages = \App\Models\Landlord\Package::Where('package_status', 'active')->orderBy('package_name', 'DESC')->get()) {
                abort(409, __('lang.no_alternative_plans_found'));
            }
        }

        //render page
        $html = view('landlord/customers/modal/change-plan', compact('packages', 'customer'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXCustomerChangePlan',
        ];

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * show the form to change the customers subscription plan
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function storeUpdatePlan($id) {

        //custom error messages
        $messages = [
            'free_trial_days.required_if' => __('lang.free_trial_duration') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'free_trial_days' => [
                'nullable',
                'required_if:free_trial,yes',
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

        //get the customer
        if (!$customer = \App\Models\Landlord\Tenant::Where('tenant_id', $id)->first()) {
            abort(409, __('lang.customer_could_not_be_found'));
        }

        //are we creating a new subscription or updating existing
        if (\App\Models\Landlord\Subscription::Where('subscription_customerid', $id)->exists()) {
            $event_action = 'changed-plan';
        } else {
            $event_action = 'created-subscription';
        }

        //get the package
        if (!$package = \App\Models\Landlord\Package::Where('package_id', request('changed_package_id'))->first()) {
            abort(409, __('lang.package_not_found'));
        }

        //change the package and get the new subscription
        $data = [
            'customer_id' => $id,
            'package_id' => request('changed_package_id'),
            'billing_cycle' => request('billing_cycle'),
            'billing_type' => request('subscription_payment_method'),
            'free_trial' => request('free_trial'),
            'free_trial_days' => request('free_trial_days'),
        ];
        if (!$subscription = $this->subscriptionsrepo->changeCustomersPlan($data)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        /** ----------------------------------------------
         * record event
         * ----------------------------------------------*/
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = auth()->id();
        $event->event_type = $event_action;
        $event->event_creator_type = 'admin';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = $package->package_name;
        $event->event_payload_3 = '';
        $event->save();

        //change subscription details in tenants database
        Tenant::forgetCurrent();

        //Update the tenants crm database with new package settings
        if ($customer = Tenant::Where('tenant_id', $id)->first()) {
            try {
                $customer->makeCurrent();
                DB::connection('tenant')
                    ->table('settings')
                    ->where('settings_id', 1)
                    ->update([
                        'settings_saas_status' => $subscription->subscription_status,
                        'settings_saas_package_id' => $package->package_id,
                        'settings_saas_package_limits_clients' => $package->package_limits_clients,
                        'settings_saas_package_limits_team' => $package->package_limits_team,
                        'settings_saas_package_limits_projects' => $package->package_limits_projects,
                        'settings_modules_projects' => ($package->package_module_projects == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_tasks' => ($package->package_module_tasks == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_invoices' => ($package->package_module_invoices == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_leads' => ($package->package_module_leads == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_knowledgebase' => ($package->package_module_knowledgebase == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_estimates' => ($package->package_module_estimates == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_expenses' => ($package->package_module_expense == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_subscriptions' => ($package->package_module_subscriptions == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_tickets' => ($package->package_module_tickets == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_calendar' => ($package->package_module_calendar == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_timetracking' => ($package->package_module_timetracking == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_reminders' => ($package->package_module_reminders == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_proposals' => ($package->package_module_proposals == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_contracts' => ($package->package_module_contracts == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_messages' => ($package->package_module_messages == 'yes') ? 'enabled' : 'disabled',
                    ]);
            } catch (Exception $e) {
                Log::error("updating customers plan failed - " . $e->getMessage(), ['process' => '[change-customer-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
                abort(409, __('lang.error_message_with_code') . config('app.debug_ref'));
            }
        }

        //success
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        $jsondata['redirect_url'] = url("app-admin/customers/$id");

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * cancel a subsccription
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelSubscription(Tenant $tenant, $id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot update the main demo subscriptions. You can create new ones for testing');
        }

        //get the subscription from db
        if (!$subscription = \App\Models\Landlord\Subscription::Where('subscription_id', $id)->first()) {
            abort(404);
        }

        //get customer
        $customer = \App\Models\Landlord\Tenant::where('tenant_id', $subscription->subscription_customerid)->first();

        //queue for cancelling at the payment gateway (will be done via cronjob)
        if ($subscription->subscription_type == 'paid' && $subscription->subscription_payment_method == 'automatic') {
            if ($subscription->subscription_status == 'active' || $subscription->subscription_status == 'failed') {
                $scheduled = new \App\Models\Landlord\Scheduled();
                $scheduled->scheduled_gateway = $subscription->subscription_gateway_name;
                $scheduled->scheduled_type = 'cancel-subscription';
                $scheduled->scheduled_payload_1 = $subscription->subscription_gateway_id;
                $scheduled->scheduled_payload_2 = $subscription->subscription_checkout_reference_2;
                $scheduled->scheduled_payload_3 = $subscription->subscription_checkout_reference_3;
                $scheduled->scheduled_payload_4 = $subscription->subscription_checkout_reference_4;
                $scheduled->scheduled_payload_5 = $subscription->subscription_checkout_reference_5;
                $scheduled->save();
            }
        }

        //update customer
        $customer->tenant_status = 'unsubscribed';
        $customer->save();

        //update tenant
        if ($this_tenant = Tenant::Where('tenant_id', $customer->tenant_id)->first()) {
            try {

                //update the tenant database
                Tenant::forgetCurrent();

                //swicth to this tenants DB
                $this_tenant->makeCurrent();

                //[example] update something on the tenant DB
                DB::connection('tenant')
                    ->table('settings')
                    ->where('settings_id', 1)
                    ->update([
                        'settings_saas_status' => 'unsubscribed',
                        'settings_saas_package_id' => null,
                        'settings_saas_package_limits_clients' => 0,
                        'settings_saas_package_limits_team' => 0,
                        'settings_saas_package_limits_projects' => 0,
                    ]);
            } catch (Exception $e) {
                Log::error("the application could not switch databases to the tenant database (" . $e->getMessage() . ")", ['process' => '[subscription-cancel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $id]);
                abort(409, __('lang.error_check_logs_for_details'));
            }
        } else {
            Log::error("the application could not switch databases to the tenant database", ['process' => '[subscription-cancel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $id]);
            abort(409, __('lang.error_check_logs_for_details'));
        }

        //log event
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = auth()->id();
        $event->event_type = 'subscription-cancelled';
        $event->event_creator_type = 'admin';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $subscription->subscription_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = $subscription->subscription_gateway_plan_id;
        $event->event_payload_3 = '';
        $event->save();

        //remove subscription row
        $jsondata['dom_visibility'][] = [
            'selector' => '#subscription_' . $subscription->subscription_id,
            'action' => 'slideout-slow',
        ];

        //archive subscription record
        $subscription->subscription_archived = 'yes';
        $subscription->subscription_status = 'cancelled';
        $subscription->save();

        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        $jsondata['redirect_url'] = url('app-admin/customers/' . $customer->tenant_id);

        //response
        return response()->json($jsondata);
    }

    /**
     * cancel a subsccription
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tenant $tenant, $id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot delete the main demo subscriptions. You can create new ones for testing');
        }

        //get the subscription from db
        if (!$subscription = \App\Models\Landlord\Subscription::Where('subscription_id', $id)->first()) {
            abort(404);
        }

        //cancel customers saas subscription
        if ($subscription->subscription_archived == 'no') {

            //get customer
            if ($customer = \App\Models\Landlord\Tenant::where('tenant_id', $subscription->subscription_customerid)->first()) {

                //queue for cancelling at the payment gateway (will be done via cronjob)
                if ($subscription->subscription_type == 'paid' && $subscription->subscription_payment_method == 'automatic') {
                    if ($subscription->subscription_status == 'active' || $subscription->subscription_status == 'failed') {
                        $scheduled = new \App\Models\Landlord\Scheduled();
                        $scheduled->scheduled_gateway = $subscription->subscription_gateway_name;
                        $scheduled->scheduled_type = 'cancel-subscription';
                        $scheduled->scheduled_payload_1 = $subscription->subscription_gateway_id;
                        $scheduled->save();
                    }
                }

                //update customer
                $customer->tenant_status = 'unsubscribed';
                $customer->save();

                //update tenant
                if ($this_tenant = Tenant::Where('tenant_id', $customer->tenant_id)->first()) {
                    try {

                        //update the tenant database
                        Tenant::forgetCurrent();

                        //swicth to this tenants DB
                        $this_tenant->makeCurrent();

                        //[example] update something on the tenant DB
                        DB::connection('tenant')
                            ->table('settings')
                            ->where('settings_id', 1)
                            ->update([
                                'settings_saas_status' => 'unsubscribed',
                                'settings_saas_package_id' => null,
                                'settings_saas_package_limits_clients' => 0,
                                'settings_saas_package_limits_team' => 0,
                                'settings_saas_package_limits_projects' => 0,
                            ]);
                    } catch (Exception $e) {
                        Log::error("the application could not switch databases to the tenant database (" . $e->getMessage() . ")", ['process' => '[subscription-cancel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $id]);
                        abort(409, __('lang.error_check_logs_for_details'));
                    }
                } else {
                    Log::error("the application could not switch databases to the tenant database", ['process' => '[subscription-cancel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $id]);
                    abort(409, __('lang.error_check_logs_for_details'));
                }
            }
        }

        //delete the subscription
        $subscription->delete();

        //succcess
        $jsondata['dom_visibility'][] = [
            'selector' => "#subscription_$id",
            'action' => 'hide',
        ];

        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

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
                __('lang.subscriptions'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.subscriptions'),
            'heading' => __('lang.subscriptions'),
            'page' => 'subscriptions',
            'mainmenu_subscriptions' => 'active',
        ];

        //return
        return $page;
    }
}