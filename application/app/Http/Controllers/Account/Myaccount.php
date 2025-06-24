<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\ChangePlanResponse;
use App\Http\Responses\Account\MyAccountResponse;
use App\Http\Responses\Account\UpdatePlanResponse;
use App\Repositories\Landlord\AccountRepository;
use App\Repositories\Landlord\SubscriptionsRepository;
use DB;
use Log;

class Myaccount extends Controller {

    //repositories
    protected $subscriptionsrepo;
    protected $accountrepo;

    public function __construct(SubscriptionsRepository $subscriptionsrepo, AccountRepository $accountrepo) {

        //parent
        parent::__construct();

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->subscriptionsrepo = $subscriptionsrepo;
        $this->accountrepo = $accountrepo;

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function show() {

        // Get the latest subscription ID for the given customer ID
        if (is_numeric(config('system.settings_saas_tenant_id'))) {
            $latest_subscription_id = \App\Models\Landlord\Subscription::On('landlord')->where('subscription_customerid', config('system.settings_saas_tenant_id'))
                ->orderBy('subscription_id', 'desc')
                ->value('subscription_id');
            if (is_numeric($latest_subscription_id)) {
                \App\Models\Landlord\Subscription::On('landlord')->where('subscription_customerid', config('system.settings_saas_tenant_id'))
                    ->where('subscription_id', '<', $latest_subscription_id)
                    ->where('subscription_archived', '<', 'no')
                    ->delete();
            }
        }

        //validate - cancelled and failed account status
        if (in_array(config('system.settings_saas_status'), ['cancelled', 'failed'])) {
            $jsondata['redirect_url'] = '/app/settings/account/notices';
            return response()->json($jsondata);
        }

        //get myaccount
        if (!$customer = DB::connection('landlord')
            ->table('tenants')
            ->where('tenant_id', config('system.settings_saas_tenant_id'))
            ->first()) {
            Log::critical("unable to retrieve customers (tenant) record from landlord database", ['process' => '[tenant-myaccount]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'customer_id' => config('system.settings_saas_tenant_id')]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //try and get the customers subscription data from the landlord database
        if (!$subscription = DB::connection('landlord')
            ->table('subscriptions')
            ->where('subscription_customerid', config('system.settings_saas_tenant_id'))
            ->where('subscription_archived', 'no')
            ->first()) {
            Log::info("unable to fetch the tenants subscription from landlord database - record not found", ['process' => '[tenant-myaccount]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => config('system.settings_saas_tenant_id')]);
            if (request()->ajax()) {
                return response()->json(array(
                    'redirect_url' => '/app/settings/account/packages',
                ));
            } else {
                return redirect('/app/settings/account/packages');
            }
        }

        //get packages
        if (!$package = DB::connection('landlord')
            ->table('packages')
            ->where('package_id', config('system.settings_saas_package_id'))
            ->first()) {
            Log::critical("unable to retrieve customers (subscription) record from landlord database", ['process' => '[tenant-myaccount]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'customer_id' => config('system.settings_saas_tenant_id')]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'customer' => $customer,
            'subscription' => $subscription,
            'package' => $package,
        ];

        //show the view
        return new MyAccountResponse($payload);

    }

    /**
     * Show the resource for the user to change their plan
     * @return blade view | ajax view
     */
    public function changePlan($id) {

        //get the package
        if (!$package = \App\Models\Landlord\Package::On('landlord')->Where('package_id', $id)->first()) {
            Log::error("changing plan failed - the package could not be found", ['process' => '[change-subscription-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'plain_id' => $id]);
            abort(409, __('lang.error_message_with_code') . config('app.debug_ref'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'package' => $package,
        ];

        //return the reposnse
        return new ChangePlanResponse($payload);

    }

    /**
     * some notes
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePlan($id) {

        //defaults
        $continue = true;

        //default limits status
        $over_limits = [];

        //get the package
        if (!$package = \App\Models\Landlord\Package::On('landlord')->Where('package_id', $id)->first()) {
            Log::error("changing plan failed - the package could not be found", ['process' => '[change-subscription-plan]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'plain_id' => $id]);
            abort(409, __('lang.error_message_with_code') . config('app.debug_ref'));
        }

        //[check limits] -  count current usage
        $count['projects'] = \App\Models\Project::where('project_type', 'project')->count();
        $count['clients'] = \App\Models\Client::count();
        $count['team'] = \App\Models\User::where('type', 'team')->count();

        //[check limits] - [projects] (exclude unlimited)
        if ($package->package_limits_projects > 0 && ($count['projects'] > $package->package_limits_projects)) {
            $continue = false;
            $over_limits[] = [
                'feature' => __('lang.projects'),
                'limits' => $package->package_limits_projects,
                'usage' => $count['projects'],
            ];
        }

        //[check limits] - [clients] (exclude unlimited)
        if ($package->package_limits_clients > 0 && ($count['clients'] > $package->package_limits_clients)) {
            $continue = false;
            $over_limits[] = [
                'feature' => __('lang.clients'),
                'limits' => $package->package_limits_clients,
                'usage' => $count['clients'],
            ];
        }

        //[check limits] - [team] (exclude unlimited)
        if ($package->package_limits_team > 0 && ($count['team'] > $package->package_limits_team)) {
            $continue = false;
            $over_limits[] = [
                'feature' => __('lang.team'),
                'limits' => $package->package_limits_team,
                'usage' => $count['team'],
            ];
        }

        //there in a limits error - show the error
        if (!$continue) {
            $payload = [
                'over_limits' => $over_limits,
                'show' => 'error',
            ];
            return new UpdatePlanResponse($payload);
        }

        //change the package and get the new subscription
        $data = [
            'customer_id' => config('system.settings_saas_tenant_id'),
            'package_id' => $package->package_id,
            'billing_cycle' => request('billing_cycle'),
            'billing_type' => 'automatic',
            'free_trial' => 'no',
            'free_trial_days' => 0,
        ];
        if (!$subscription = $this->subscriptionsrepo->changeCustomersPlan($data)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //update customer settings
        \App\Models\Settings::where('settings_id', 1)
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

        //package was free, just show success
        if ($subscription->subscription_type == 'free') {
            $payload = [
                'show' => 'success',
            ];
            return new UpdatePlanResponse($payload);
        }

        //package was paid, redirect to notices
        $payload = [
            'show' => 'payment-required',
        ];
        return new UpdatePlanResponse($payload);

    }

    /**
     * close users account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function closeAccount() {

        //close the account
        if (!$data = $this->accountrepo->closeMyAccount()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //schedule database for deleting
        $scheduled = new \App\Models\Landlord\Scheduled();
        $scheduled->setConnection('landlord');
        $scheduled->scheduled_type = 'delete-database';
        $scheduled->scheduled_payload_1 = $data['database_name'];
        $scheduled->save();

        /** ----------------------------------------------
         * send email to multiple admin
         * ----------------------------------------------*/
        if ($users = \App\Models\User::On('landlord')->Where('type', 'admin')->get()) {
            foreach ($users as $user) {
                $mail = new \App\Mail\Landlord\Admin\AccountClosed($user, $data, []);
                $mail->build();
            }
        }

        //message
        request()->session()->flash('success-notification-long', __('lang.request_has_been_completed'));

        //redirect to customers old url (to show 404)
        $jsondata['redirect_url'] = url('/login');

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

        $page = [
            'crumbs' => [
                __('lang.my_account'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.billing'),
            'heading' => __('lang.billing'),
        ];

        return $page;
    }
}