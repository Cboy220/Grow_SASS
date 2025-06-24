<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Responses\Account\PackagesResponse;
use DB;

class Packages extends Controller {


    public function __construct() {

        //parent
        parent::__construct();

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

    }

    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function show() {

        //validate - cancelled and failed account status
        if (in_array(config('system.settings_saas_status'), ['cancelled', 'failed'])) {
            $jsondata['redirect_url'] = '/app/settings/account/notices';
            //return response()->json($jsondata);
        }

        //page
        $page = $this->pageSettings('packages');

        request()->merge([
            'orderby' => 'package_amount_monthly',
            'sortorder' => 'asc',
        ]);

        //show allpackages (including acrhived ones, incase that is the one the user is subscribed to) - will filter in blade
        $packages = DB::connection('landlord')
            ->table('packages')
            ->orderBy('package_amount_monthly', 'asc')
            ->orderBy('package_amount_yearly', 'asc')
            ->get();

        //payload
        $payload = [
            'packages' => $packages,
            'page' => $page,
        ];

        //show admin only buttons etc
        config(['visibility.tenant' => true]);

        //show the view
        return new PackagesResponse($payload);

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
        if ($subscription = \App\Models\Landlord\Subscription::Where('subscription_customerid', $id)->first()) {
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
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.pricing_plans'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.billing'),
            'heading' => __('lang.billing'),
        ];

        return $page;
    }
}