<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\Signup\CreateAccount;
use App\Repositories\Landlord\CreateTenantRepository;
use Illuminate\Support\Str;

class Signup extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        //$this->middleware('guest');

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get packages
        $packages = \App\Models\Landlord\Package::Where('package_status', 'active')->Where('package_visibility', 'visible')->get();

        $page = $this->pageSettings('index');

        //main menu
        $mainmenu = \App\Models\Landlord\Frontend::Where('frontend_group', 'main-menu')->orderBy('frontend_name', 'asc')->get();

        //get the item
        $section = \App\Models\Landlord\Frontend::Where('frontend_name', 'page-signup')->first();

        return view('frontend/signup/page', compact('page', 'packages', 'mainmenu', 'section'))->render();

    }

    /**
     * process for creating a new account
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createAccount(CreateAccount $request, CreateTenantRepository $createtenantrepo) {

        //defaults
        $free_trial = 'no';
        $subscription_trial_end = null;
        $subscription_date_started = null;

        //validate terms
        if (config('system.settings_terms_of_service_status') == 'enabled') {
            if (request('signup_agree_terms') != 'on') {
                abort(409, __('lang.agree_to_terms_of_service'));
            }
        }

        //correct the plain id by removing extra strings
        $plan_id = str_replace(['monthly_', 'yearly_', 'free_'], '', request('plan'));

        //get the package
        if (!$package = \App\Models\Landlord\Package::Where('package_id', $plan_id)->first()) {
            abort(409, __('lang.package_not_found'));
        }

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
        if ($package->package_subscription_options == 'paid' && config('system.settings_free_trial') == 'yes') {
            $status = 'free-trial';
            $free_trial = 'yes';
            $subscription_trial_end = \Carbon\Carbon::now()->addDays(config('system.settings_free_trial_days'))->format('Y-m-d');
        }

        //paid packages - free trial
        if ($package->package_subscription_options == 'paid' && config('system.settings_free_trial') == 'no') {
            $status = 'awaiting-payment';
        }

        //create tenant
        $customer = new \App\Models\Landlord\Tenant();
        $customer->domain = strtolower(request('account_name') . '.' . config('system.settings_base_domain'));
        $customer->subdomain = strtolower(request('account_name'));
        $customer->tenant_creatorid = 0;
        $customer->tenant_name = request('full_name');
        $customer->tenant_email = request('email_address');
        $customer->tenant_status = $status;
        $customer->tenant_email_local_email = strtolower(request('account_name') . '@' . config('system.settings_email_domain'));
        $customer->tenant_email_forwarding_email = request('email_address');
        $customer->tenant_email_config_type = 'local';
        $customer->tenant_email_config_status = 'pending';
        $customer->tenant_password = bcrypt(request('password'));
        $customer->tenant_updating_current_version = config('system.settings_version');
        $customer->save();

        //temp authentication key
        $auth_key = Str::random(30);

        //create tenant database
        if (!$createtenantrepo->createTenant($customer, $package, $auth_key)) {
            $customer->delete();
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //redirect url
        $protocol = (request()->secure()) ? 'https://' : 'http://';
        $account_url = $protocol . $customer->domain . "/auth?id_key=$auth_key";

        //create subscription
        $subscription = new \App\Models\Landlord\Subscription();
        $subscription->subscription_uniqueid = str_unique();
        $subscription->subscription_creatorid = 0;
        $subscription->subscription_customerid = $customer->tenant_id;
        $subscription->subscription_type = $package->package_subscription_options;
        $subscription->subscription_amount = $subscription_amount;
        $subscription->subscription_trial_end = $subscription_trial_end;
        $subscription->subscription_date_started = $subscription_date_started;
        $subscription->subscription_package_id = $package->package_id;
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
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = $package->package_name;
        $event->event_payload_3 = '';
        $event->save();

        /** ----------------------------------------------
         * send email to customer & Admin
         * ----------------------------------------------*/
        $data = [
            'subscription_type' => $subscription->subscription_type,
            'account_name' => $customer->domain,
            'customer_name' => $customer->tenant_name,
            'customer_id' => $customer->tenant_id,
            'account_url' => $account_url,
            'password' => __('lang.as_set_during_signup'),
        ];

        //customer
        $mail = new \App\Mail\Landlord\Customer\NewCustomerWelcome($customer, $data, $package);
        $mail->build();

        //admin users
        if ($admins = \App\Models\User::On('landlord')->Where('type', 'admin')->get()) {
            foreach ($admins as $user) {
                $mail = new \App\Mail\Landlord\Admin\NewCustomerSignUp($user, $data, $package);
                $mail->build();
            }
        }

        //redirect
        $jsondata['redirect_url'] = $account_url;

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

        ];

        //return
        return $page;
    }
}