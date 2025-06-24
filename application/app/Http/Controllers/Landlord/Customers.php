<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\Customers\SetActiveValidation;
use App\Http\Requests\Landlord\Customers\StoreUpdateValidation;
use App\Http\Requests\Landlord\Customers\UpdatePasswordValidation;
use App\Http\Requests\Landlord\Customers\UpdateValidation;
use App\Http\Responses\Landlord\Customers\IndexResponse;
use App\Http\Responses\Landlord\Customers\UpdateResponse;
use App\Repositories\Landlord\CreateTenantRepository;
use App\Repositories\Landlord\SubscriptionsRepository;
use App\Repositories\Landlord\TenantsRepository;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Log;
use Spatie\Multitenancy\Models\Tenant;

class Customers extends Controller {

    //repositories
    protected $tenantsrepo;
    protected $subscriptionrepo;

    public function __construct(
        TenantsRepository $tenantsrepo,
        SubscriptionsRepository $subscriptionrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //repositories
        $this->tenantsrepo = $tenantsrepo;
        $this->subscriptionrepo = $subscriptionrepo;

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get customers
        $customers = $this->tenantsrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'customers' => $customers,
        ];

        //show the form
        return new IndexResponse($payload);
    }

    /**
     * Show the resource
     * @return blade view | ajax view
     */
    public function show($id) {

        //validate exist
        if (\App\Models\Landlord\Tenant::Where('tenant_id', $id)->doesntExist()) {
            abort(404);
        }

        //get the item
        $customers = $this->tenantsrepo->search($id);
        $customer = $customers->first();

        //get customers subscription if there is one
        if ($subscription = \App\Models\Landlord\Subscription::Where('subscription_customerid', $id)
            ->where('subscription_archived', 'no')
            ->first()) {
            config(['visibility.has_subscription' => true]);
            config(['subscription_status' => $subscription->subscription_status]);
        } else {
            $subscription = [];
        }

        //page
        $page = $this->pageSettings('show');

        //url resource
        request()->merge([
            'resource_query' => "payment_tenant_id=$id",
        ]);

        //show the item
        return view('landlord/customer/wrapper', compact('page', 'customer', 'subscription'))->render();

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        $packages = \App\Models\Landlord\Package::Where('package_status', 'active')->get();

        //create options
        config(['visibility.send_welcome_email_checkbox' => true]);

        //page
        $html = view('landlord/customers/modal/add-edit-inc', compact('packages'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXCustomerChangePlan',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * process for creating a new account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateValidation $request, CreateTenantRepository $createtenantrepo) {

        //defaults
        $free_trial = 'no';
        $subscription_trial_end = null;
        $subscription_date_started = null;

        //get the package
        $package = \App\Models\Landlord\Package::Where('package_id', request('plan'))->first();

        //free packages
        if ($package->package_subscription_options == 'free') {
            $status = 'active';
            $subscription_amount = 0;
            $subscription_date_started = now();
        }

        //general settings for paid subscriptions
        if ($package->package_subscription_options == 'paid') {
            if (request('billing_cycle') == 'monthly') {
                $subscription_amount = $package->package_amount_monthly;
            } else {
                $subscription_amount = $package->package_amount_yearly;
            }
        }

        //paid packages - free trial
        if ($package->package_subscription_options == 'paid' && request('free_trial') == 'yes') {
            $status = 'free-trial';
            $free_trial = 'yes';
            $subscription_trial_end = \Carbon\Carbon::now()->addDays(request('free_trial_days'))->format('Y-m-d');
        }

        //paid packages - free trial
        if ($package->package_subscription_options == 'paid' && request('free_trial') == 'no') {
            $status = 'awaiting-payment';
        }

        //generate a customer password (or for demo mode use 'growcrm')
        if (config('app.application_demo_mode')) {
            $password = 'growcrm';
        } else {
            $password = random_string(10);
        }

        $encrypted_password = Hash::make($password);

        //create tenant
        $customer = new \App\Models\Landlord\Tenant();
        $customer->domain = strtolower(request('account_name') . '.' . config('system.settings_base_domain'));
        $customer->subdomain = request('account_name');
        $customer->tenant_creatorid = auth()->id();
        $customer->tenant_name = request('full_name');
        $customer->tenant_email = request('email_address');
        $customer->tenant_status = $status;
        $customer->tenant_email_local_email = strtolower(request('account_name') . '@' . config('system.settings_email_domain'));
        $customer->tenant_email_forwarding_email = request('email_address');
        $customer->tenant_email_config_type = 'local';
        $customer->tenant_email_config_status = 'pending';
        $customer->tenant_password = $encrypted_password; //(hashed password)
        $customer->tenant_updating_current_version = config('system.settings_version');
        $customer->save();

        //temp authentication key
        $auth_key = Str::random(30);

        //create tenant database
        if (!$createtenantrepo->createTenant($customer, $package, $auth_key)) {
            $customer->delete();
            abort(409, __('lang.request_failed_see_logs'));
        }

        //account url
        $protocol = (request()->secure()) ? 'https://' : 'http://';
        $account_url = $protocol . $customer->domain . "/auth?id_key=$auth_key";

        //create subscription
        $subscription = new \App\Models\Landlord\Subscription();
        $subscription->subscription_creatorid = auth()->id();
        $subscription->subscription_customerid = $customer->tenant_id;
        $subscription->subscription_uniqueid = str_unique();
        $subscription->subscription_type = $package->package_subscription_options;
        $subscription->subscription_amount = $subscription_amount;
        $subscription->subscription_trial_end = $subscription_trial_end;
        $subscription->subscription_date_started = $subscription_date_started;
        $subscription->subscription_package_id = $package->package_id;
        $subscription->subscription_payment_method = request('subscription_payment_method');
        $subscription->subscription_status = $status;
        $subscription->subscription_gateway_billing_cycle = request('billing_cycle');
        $subscription->save();

        /** ----------------------------------------------
         * record event
         * ----------------------------------------------*/
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = $customer->tenant_id;
        $event->event_type = 'account-created';
        $event->event_creator_type = 'customer';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = $package->package_name;
        $event->event_payload_3 = '';
        $event->save();

        /** ----------------------------------------------
         * send email to customer & Admin
         * ----------------------------------------------*/
        if (request('send_welcome_email') == 'on') {
            $data = [
                'subscription_type' => $subscription->subscription_type,
                'account_name' => $customer->domain,
                'customer_name' => $customer->tenant_name,
                'customer_id' => $customer->tenant_id,
                'account_url' => $account_url,
                'password' => $password,
            ];
            //customer
            $mail = new \App\Mail\Landlord\Customer\NewCustomerWelcome($customer, $data, $package);
            $mail->build();
        }

        //redirect
        $jsondata['redirect_url'] = url('app-admin/customers');

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the customer
        $customer = \App\Models\Landlord\Tenant::Where('tenant_id', $id)->first();

        //page
        $html = view('landlord/customers/modal/basic-edit', compact('customer'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXCustomerBasicEdit',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateValidation $request, $id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot edit the main demo accounts. You can create new ones for testing');
        }

        //get the customer
        $customer = \App\Models\Landlord\Tenant::Where('tenant_id', $id)->first();

        //store record
        $customer->tenant_name = request('full_name');
        $customer->tenant_email = request('email_address');
        $customer->domain = strtolower(request('account_name') . '.' . config('system.settings_base_domain'));
        $customer->subdomain = strtolower(request('account_name'));
        $customer->save();

        //count rows
        $customers = $this->tenantsrepo->search();
        $count = count($customers);

        //get friendly row
        $customers = $this->tenantsrepo->search($id);
        $customer = $customers->first();

        /** ----------------------------------------------
         * record event [comment]
         * see database table to details of each key
         * ----------------------------------------------*/
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = auth()->id();
        $event->event_type = 'account-updated';
        $event->event_creator_type = 'admin';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = '';
        $event->event_payload_3 = '';
        $event->save();

        //payload
        $payload = [
            'customers' => $customers,
            'customer' => $customer,
            'count' => $count,
            'page' => $this->pageSettings(),
        ];

        //render
        return new UpdateResponse($payload);

    }

    /**
     * Show the customer subscription details
     * @return blade view | ajax view
     */
    public function showSubscription($id) {

        //get the customers subscription
        if ($subscription = \App\Models\Landlord\Subscription::Where('subscription_customerid', $id)
            ->where('subscription_archived', 'no')
            ->leftJoin('packages', 'packages.package_id', '=', 'subscriptions.subscription_package_id')
            ->first()) {
            config(['visibility.has_subscription' => true]);
        } else {
            $subscription = [];
            config(['visibility.has_subscription' => false]);
        }

        //page
        $html = view('landlord/subscriptions/details', compact('subscription'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#dynamic-content-container',
            'action' => 'replace',
            'value' => $html,
        ];

        $jsondata['dom_visibility'][] = [
            'selector' => '.list-page-actions-containers',
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#list-page-actions-container-customer',
            'action' => 'show',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * Show the customer subscription details
     * @return blade view | ajax view
     */
    public function showEmailSettings($id) {

        //get the customers subscription
        if (!$customer = \App\Models\Landlord\Tenant::Where('tenant_id', $id)->first()) {
            abort(404);
        }

        $jsondata = [];

        //page view
        if (request('source') == 'page') {
            $html = view('landlord/customer/email/email-settings', compact('customer'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#dynamic-content-container',
                'action' => 'replace',
                'value' => $html,
            ];

            $jsondata['dom_visibility'][] = [
                'selector' => '.list-page-actions-containers',
                'action' => 'hide',
            ];
        }

        //list view (modal)
        if (request('source') == 'list') {
            $html = view('landlord/customer/email/email-settings', compact('customer'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#commonModalBody',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        //render
        return response()->json($jsondata);

    }

    /**
     * mark that the custoners email has been done
     *
     * @return \Illuminate\Http\Response
     */
    public function markEmailSettingsDone($id) {

        //get the item
        if (!$customer = \App\Models\Landlord\Tenant::Where('tenant_id', $id)->first()) {
            abort(404);
        }

        //update customer
        $customer->tenant_email_config_status = 'completed';
        $customer->save();

        //count pending
        $count = \App\Models\Landlord\Tenant::where('tenant_email_config_status', 'pending')->count();

        if ($count == 0) {
            $jsondata['dom_visibility'][] = [
                'selector' => "#menu_tenant_email_config_status",
                'action' => 'hide',
            ];
        }

        $jsondata['dom_visibility'][] = [
            'selector' => ".email_settings_pending_$id",
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => "#email_settings_completed_$id",
            'action' => 'show',
        ];

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubscriptionsRepository $subscriptionsrepo, $id) {

        //get the record
        $customer = \App\Models\Landlord\Tenant::Where('tenant_id', $id)->first();

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot delete the main demo accounts. You can create new ones for testing');
        }

        //schedule for cronjob - delete database
        $schedule = new \App\Models\Landlord\Scheduled();
        $schedule->scheduled_type = 'delete-database';
        $schedule->scheduled_payload_1 = $customer->database;
        $schedule->save();

        //delete record
        $customer->delete();

        //delete subsciption locally and at schedule for deleting at the payment gateay
        if ($subscription = \App\Models\Landlord\Subscription::Where('subscription_customerid', $id)->first()) {
            if ($subscription->subscription_status == 'active' || $subscription->subscription_status == 'failed') {
                if ($subscription->subscription_gateway_id != '' && $subscription->subscription_gateway_name != '') {
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
            $subscription->delete();
        }

        //delete any other subscriptions
        \App\Models\Landlord\Subscription::Where('subscription_customerid', $id)->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#customer_' . $id,
            'action' => 'slideup-slow-remove',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        if (request('source') == 'page') {
            $jsondata['redirect_url'] = url('/app-admin/customers');
            request()->session()->flash('success-notification', __('lang.request_has_been_completed'));
        }

        //response
        return response()->json($jsondata);

    }

    /**
     * Show evet timeline
     * @return blade view | ajax view
     */
    public function events($id) {

        //get events
        $events = \App\Models\Landlord\Event::Where('event_customer_id', $id)
            ->leftJoin("users", "users.id", "=", "events.event_creatorid")
            ->leftJoin("tenants", "tenants.tenant_id", "=", "events.event_customer_id")->get();

        //page
        $html = view('landlord/events/event', compact('events'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#customer-content-container',
            'action' => 'replace',
            'value' => $html,
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function editPassword() {

        //page
        $html = view('landlord/customers/modal/update-password')->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXCustomerUpdatePassword',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdatePasswordValidation $request, $id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot delete the main demo accounts. You can create new ones for testing');
        }

        //get the customer
        if (!$customer = Tenant::Where('tenant_id', $id)->first()) {
            abort(404);
        }

        //reset
        Tenant::forgetCurrent();

        //get the customer from landlord db
        try {
            //swicth to this tenants DB
            $customer->makeCurrent();

            //update teh default users password
            if ($user = \App\Models\User::on('tenant')->Where('id', 1)->first()) {
                $user->password = Hash::make(request('password'));
                $user->save();
            }

        } catch (Exception $e) {
            abort(409, $e->getMessage());
        }

        /** ----------------------------------------------
         * record event
         * ----------------------------------------------*/
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = auth()->id();
        $event->event_type = 'password-updated';
        $event->event_creator_type = 'admin';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = '';
        $event->event_payload_3 = '';
        $event->save();

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        $jsondata['skip_dom_reset'] = true;

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function setStatusActive() {

        //page
        $html = view('landlord/customers/modal/set-active')->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXCustomerUpdatePassword',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function updateStatusActive(SetActiveValidation $request, $id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot update the main demo accounts. You can create new ones for testing');
        }

        //get the customer
        if (!$customer = Tenant::Where('tenant_id', $id)->first()) {
            abort(404);
        }

        //get the subscription
        if (!$subscription = \App\Models\Subscription::Where('subscription_customerid', $id)->first()) {
            abort(409, __('lang.no_subscription_exists_for_customer'));
        }

        //mark as subscription as active
        $subscription->subscription_status = 'active';
        $subscription->subscription_date_next_renewal = request('expiry_date');
        $subscription->save();

        //mark as customer as active
        //reset existing account owner
        \App\Models\Landlord\Tenant::where('tenant_id', $id)
            ->update(['tenant_status' => 'active']);

        //reset
        Tenant::forgetCurrent();

        //get the customer from landlord db
        try {
            //swicth to this tenants DB
            $customer->makeCurrent();

            //update teh default users password
            if ($settings = \App\Models\Settings::on('tenant')->Where('settings_id', 1)->first()) {
                $settings->settings_saas_status = 'active';
                $settings->save();
            }

        } catch (Exception $e) {
            abort(409, $e->getMessage());
        }

        /** ----------------------------------------------
         * record event
         * ----------------------------------------------*/
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = auth()->id();
        $event->event_type = 'account-updated';
        $event->event_creator_type = 'admin';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = '';
        $event->event_payload_3 = '';
        $event->save();

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        $jsondata['redirect_url'] = url("app-admin/customers/$id");

        request()->session()->flash('success-notification-long', __('lang.request_has_been_completed'));

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function syncAccount() {

        //page
        $html = view('landlord/customers/modal/sync-account')->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * Sync various items of the tenants database, with the landlord database
     *
     * @return \Illuminate\Http\Response
     */
    public function updateSyncAccount($id) {

        //get the customer
        if (!$customer = Tenant::Where('tenant_id', $id)->first()) {
            abort(404);
        }

        //get tenant
        $tenants = $this->tenantsrepo->search($id);
        $tenant = $tenants->first();

        //reset
        Tenant::forgetCurrent();

        //get the customer from landlord db
        try {
            //swicth to this tenants DB
            $customer->makeCurrent();

            //update teh default users password
            if ($settings = \App\Models\Settings::on('tenant')->Where('settings_id', 1)->first()) {
                $settings->settings_saas_status = ($tenant->subscription_status != '') ? $tenant->subscription_status : 'cancelled';
                $settings->settings_saas_tenant_id = $id;
                $settings->settings_saas_package_id = (is_numeric($tenant->package_id)) ? $tenant->package_id : null;
                $settings->settings_saas_package_limits_clients = (is_numeric($tenant->package_limits_clients)) ? $tenant->package_limits_clients : 0;
                $settings->settings_saas_package_limits_team = (is_numeric($tenant->package_limits_team)) ? $tenant->package_limits_team : 0;
                $settings->settings_saas_package_limits_projects = (is_numeric($tenant->package_limits_projects)) ? $tenant->package_limits_projects : 0;
                $settings->settings_modules_projects = ($tenant->package_module_projects == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_tasks = ($tenant->package_module_tasks == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_invoices = ($tenant->package_module_invoices == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_leads = ($tenant->package_module_leads == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_knowledgebase = ($tenant->package_module_knowledgebase == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_estimates = ($tenant->package_module_estimates == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_expenses = ($tenant->package_module_expense == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_subscriptions = ($tenant->package_module_subscriptions == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_tickets = ($tenant->package_module_tickets == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_calendar = ($tenant->package_module_calendar == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_timetracking = ($tenant->package_module_timetracking == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_reminders = ($tenant->package_module_reminders == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_proposals = ($tenant->package_module_proposals == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_contracts = ($tenant->package_module_contracts == 'yes') ? 'enabled' : 'disabled';
                $settings->settings_modules_messages = ($tenant->package_module_messages == 'yes') ? 'enabled' : 'disabled';
                $settings->save();
            }

        } catch (Exception $e) {
            abort(409, $e->getMessage());
        }

        /** ----------------------------------------------
         * record event
         * ----------------------------------------------*/
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = auth()->id();
        $event->event_type = 'account-synced';
        $event->event_creator_type = 'admin';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = '';
        $event->event_payload_3 = '';
        $event->save();

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        $jsondata['redirect_url'] = url("app-admin/customers/$id");

        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * login as the customer
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function LoginAsCustomer($id) {

        //get the customer from landlord db
        if (!$customer = Tenant::Where('tenant_id', $id)->first()) {
            abort(404);
        }

        //reset
        Tenant::forgetCurrent();

        try {
            //swicth to this tenants DB
            $customer->makeCurrent();

            $key = str_unique();

            //add the onetime login key to the tenant database
            \App\Models\Settings::on('tenant')->where('settings_id', 1)
                ->update(['settings_saas_onetimelogin_key' => $key]);

        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::info("logging in as the customer failed - error: $error", ['process' => '[authenticate-login-as-customer]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, __('lang.error_check_logs_for_details'));
        }

        //reset
        Tenant::forgetCurrent();

        //redirect to the customers url and auto login
        $url = 'https://' . $customer->domain . "/access?id_key=$key";
        return redirect($url);

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
                __('lang.customers'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.customers'),
            'heading' => __('lang.customers'),
            'page' => 'customers',
            'mainmenu_customers' => 'active',
        ];

        //return
        return $page;
    }
}