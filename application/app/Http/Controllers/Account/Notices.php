<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\Notices\NewPaymentResponse;
use App\Http\Responses\Account\Notices\NoNoticeResponse;
use App\Http\Responses\Account\Notices\SubscriptionCancelledFailedResponse;
use DB;
use Log;

class Notices extends Controller {

    /**
     * The foo repository instance.
     */
    protected $foorepo;

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get settings
        $settings = \App\Models\Settings::Where('settings_id', 1)->first();

        //try and get the customers subscription data from the landlord database
        if (!$subscription = DB::connection('landlord')
            ->table('subscriptions')
            ->where('subscription_customerid', $settings->settings_saas_tenant_id)
            ->where('subscription_archived', 'no')
            ->first()) {
            Log::info("unable to fetch the tenants subscription from landlord database - record not found", ['process' => '[tenant-notices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $settings->settings_saas_tenant_id]);
            if (request()->ajax()) {
                return response()->json(array(
                    'redirect_url' => '/app/settings/account/packages',
                ));
            } else {
                return redirect('/app/settings/account/packages');
            }
        }

        //get admin payment gateway settings
        if (!$landlord_settings = DB::connection('landlord')
            ->table('settings')
            ->where('settings_id', 'default')
            ->first()) {
            Log::critical("unable to fetch the landlord settimgs table", ['process' => '[tenant-notices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $settings->settings_saas_tenant_id]);
            abort(409, __('lang.error_message_with_code') . config('app.debug_ref'));
        }

        //get customer package
        if (!$package = DB::connection('landlord')
            ->table('packages')
            ->where('package_id', $settings->settings_saas_package_id)
            ->first()) {
            Log::critical("unable to fetch the customer plan from the landlord database", ['process' => '[tenant-notices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'plan_id' => $settings->settings_saas_package_id]);
            abort(409, __('lang.error_message_with_code') . config('app.debug_ref'));
        }

        //subscription is awaiting payment
        if ($settings->settings_saas_status == 'awaiting-payment') {
            $payload = [
                'page' => $this->pageSettings('index'),
                'subscription' => $subscription,
                'package' => $package,
                'landlord_settings' => $landlord_settings,
            ];
            return new NewPaymentResponse($payload);
        }

        //subscription is awaiting payment
        if ($settings->settings_saas_status == 'cancelled') {
            $payload = [
                'page' => $this->pageSettings('index'),
                'subscription' => $subscription,
                'package' => $package,
                'landlord_settings' => $landlord_settings,
            ];
            return new SubscriptionCancelledFailedResponse($payload);
        }

        //show the view
        $payload = [
            'page' => $this->pageSettings('index'),
        ];
        return new NoNoticeResponse($payload);

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
                __('lang.notices'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.billing'),
            'heading' => __('lang.billing'),
        ];

        return $page;
    }
}