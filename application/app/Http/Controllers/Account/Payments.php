<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\PaymentsResponse;
use DB;
use Log;

class Payments extends Controller {

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

        //try and get the customers subscription data from the landlord database
        if (!$subscription = DB::connection('landlord')
            ->table('subscriptions')
            ->where('subscription_customerid', config('system.settings_saas_tenant_id'))
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

        //sorting
        $orderby = (request()->filled('orderby')) ? request('orderby') : 'payment_id';
        $sortorder = (request()->filled('sortorder')) ? request('sortorder') : 'asc';

        //paginated results
        $payments = DB::connection('landlord')
            ->table('payments')
            ->where('payment_tenant_id', config('system.settings_saas_tenant_id'))
            ->orderBy($orderby, $sortorder)
            ->paginate(config('system.settings_system_pagination_limits'));

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'payments' => $payments,
        ];

        //show the form
        return new PaymentsResponse($payload);
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
                __('lang.payment_history'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.billing'),
            'heading' => __('lang.billing'),
        ];

        return $page;
    }
}