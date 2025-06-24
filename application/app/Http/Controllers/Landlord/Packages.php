<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\Packages\StoreUpdateValidation;
use App\Repositories\Landlord\PackagesRepository;
use App\Repositories\Landlord\StripeRepository;
use Illuminate\Support\Facades\Log;

class Packages extends Controller {

    //repositories
    protected $packagesrepo;
    protected $striperepo;

    public function __construct(
        PackagesRepository $packagesrepo,
        StripeRepository $striperepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //repositories
        $this->packagesrepo = $packagesrepo;
        $this->striperepo = $striperepo;

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //page
        $page = $this->pageSettings();

        request()->merge([
            'orderby' => 'package_amount_monthly',
            'sortorder' => 'asc',
        ]);

        //show admin only buttons etc
        config(['visibility.landlord' => true]);

        //get customers
        $packages = $this->packagesrepo->search();

        return view('landlord/packages/wrapper', compact('page', 'packages'))->render();
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //set visibilities
        config([
            'visibility.payment_options' => true,
            'visibility.package_type' => true,
        ]);

        //page
        $html = view('landlord/packages/modal/add-edit-inc')->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXPackagesCreate',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resoure
     *
     * [NOTES]
     *   - Plans are created automatically at the payment gateway
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateValidation $request) {

        //store record
        $package = new \App\Models\Landlord\Package();
        $package->package_name = request('package_name');
        $package->package_subscription_options = request('package_subscription_options');
        $package->package_amount_monthly = request('package_amount_monthly');
        $package->package_amount_yearly = request('package_amount_yearly');
        $package->package_featured = (request('package_featured') == 'on') ? 'yes' : 'no';
        $package->package_limits_clients = request('package_limits_clients');
        $package->package_limits_projects = request('package_limits_projects');
        $package->package_limits_team = request('package_limits_team');
        $package->package_module_tasks = (request('package_module_tasks') == 'on') ? 'yes' : 'no';
        $package->package_module_invoices = (request('package_module_invoices') == 'on') ? 'yes' : 'no';
        $package->package_module_leads = (request('package_module_leads') == 'on') ? 'yes' : 'no';
        $package->package_module_knowledgebase = (request('package_module_knowledgebase') == 'on') ? 'yes' : 'no';
        $package->package_module_estimates = (request('package_module_estimates') == 'on') ? 'yes' : 'no';
        $package->package_module_expense = (request('package_module_expense') == 'on') ? 'yes' : 'no';
        $package->package_module_subscriptions = (request('package_module_subscriptions') == 'on') ? 'yes' : 'no';
        $package->package_module_tickets = (request('package_module_tickets') == 'on') ? 'yes' : 'no';
        $package->package_module_calendar = (request('package_module_calendar') == 'on') ? 'yes' : 'no';
        $package->package_module_timetracking = (request('package_module_timetracking') == 'on') ? 'yes' : 'no';
        $package->package_module_reminders = (request('package_module_reminders') == 'on') ? 'yes' : 'no';
        $package->package_module_proposals = (request('package_module_proposals') == 'on') ? 'yes' : 'no';
        $package->package_module_contracts = (request('package_module_contracts') == 'on') ? 'yes' : 'no';
        $package->package_module_messages = (request('package_module_messages') == 'on') ? 'yes' : 'no';
        //default module
        $package->package_module_projects = 'yes';
        $package->save();

        //reset featured
        if (request('package_featured') == 'on') {
            \App\Models\Landlord\Package::whereNotIn('package_id', [$package->package_id])
                ->update(['package_featured' => 'no']);
        }

        //prepend content on top of list or show full table
        $jsondata['redirect_url'] = url('app-admin/packages');

        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        return response()->json($jsondata);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the package
        if (!$package = \App\Models\Landlord\Package::Where('package_id', $id)->first()) {
            abort(404);
        }

        //show payment gateway plans
        if ($package->package_subscription_options != 'free') {
            config([
                'visibility.payment_gateways' => true,
                'visibility.payment_options' => true,
            ]);
        }

        //editing mode
        config([
            'visibility.payment_gateways_editing_mode' => true,
        ]);

        //page
        $html = view('landlord/packages/modal/add-edit-inc', compact('package'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXPackagesCreate',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     *   [PAYMENT GATEWAY NOTES]
     *   - If price or plan name change, they are changed automatically at the gateway
     *   - To change plan ID, you will need to used the 'advanced edit' option
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateValidation $request, $id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot update the main demo plans. You can create new ones for testing');
        }

        //save old plan
        $old_package = \App\Models\Landlord\Package::Where('package_id', $id)->first();

        //update record
        $package = \App\Models\Landlord\Package::Where('package_id', $id)->first();
        $package->package_name = request('package_name');
        $package->package_amount_monthly = request('package_amount_monthly');
        $package->package_amount_yearly = request('package_amount_yearly');
        $package->package_featured = (request('package_featured') == 'on') ? 'yes' : 'no';
        $package->package_limits_clients = request('package_limits_clients');
        $package->package_limits_projects = request('package_limits_projects');
        $package->package_limits_team = request('package_limits_team');
        $package->package_module_tasks = (request('package_module_tasks') == 'on') ? 'yes' : 'no';
        $package->package_module_invoices = (request('package_module_invoices') == 'on') ? 'yes' : 'no';
        $package->package_module_leads = (request('package_module_leads') == 'on') ? 'yes' : 'no';
        $package->package_module_knowledgebase = (request('package_module_knowledgebase') == 'on') ? 'yes' : 'no';
        $package->package_module_estimates = (request('package_module_estimates') == 'on') ? 'yes' : 'no';
        $package->package_module_expense = (request('package_module_expense') == 'on') ? 'yes' : 'no';
        $package->package_module_subscriptions = (request('package_module_subscriptions') == 'on') ? 'yes' : 'no';
        $package->package_module_tickets = (request('package_module_tickets') == 'on') ? 'yes' : 'no';
        $package->package_module_calendar = (request('package_module_calendar') == 'on') ? 'yes' : 'no';
        $package->package_module_timetracking = (request('package_module_timetracking') == 'on') ? 'yes' : 'no';
        $package->package_module_reminders = (request('package_module_reminders') == 'on') ? 'yes' : 'no';
        $package->package_module_proposals = (request('package_module_proposals') == 'on') ? 'yes' : 'no';
        $package->package_module_contracts = (request('package_module_contracts') == 'on') ? 'yes' : 'no';
        $package->package_module_messages = (request('package_module_messages') == 'on') ? 'yes' : 'no';
        $package->package_sync_status = 'awaiting-sync';
        $package->save();

        //update gateways (scheduled)
        $this->scheduleGatewayUpdates($package, $old_package);

        //reset featured
        if (request('package_featured') == 'on') {
            \App\Models\Landlord\Package::whereNotIn('package_id', [$id])
                ->update(['package_featured' => 'no']);
        }

        //prepend content on top of list or show full table
        $jsondata['redirect_url'] = url('app-admin/packages');

        request()->session()->flash('success-notification', __('lang.success_changes_take_time'));

        return response()->json($jsondata);

    }

    /**
     * schedule changes to be done at each payment gateway
     *    - plane 'name' changes
     *    - plan 'price'changes
     *
     * @param  object $package
     * @param  object $old_package
     * @return bool
     */
    public function scheduleGatewayUpdates($package, $old_package) {

        //not needed for free plan
        if ($package->package_subscription_options == 'free') {
            return;
        }

        //update plan name (product name)
        if ($package->package_name != $old_package->package_name) {

            //delete previously scheduled changes
            \App\Models\Landlord\Scheduled::Where('scheduled_type', 'update-plan-name')
                ->Where('scheduled_status', 'new')
                ->Where('scheduled_payload_1', $package->package_id)
                ->delete();

            //schedule for cronjob
            $schedule = new \App\Models\Landlord\Scheduled();
            $schedule->scheduled_gateway = 'all';
            $schedule->scheduled_type = 'update-plan-name';
            $schedule->scheduled_payload_1 = $package->package_id;
            $schedule->save();
        }

        //update plan price (monthly)
        if ($package->package_amount_monthly != $old_package->package_amount_monthly) {

            //delete previously scheduled changes
            \App\Models\Landlord\Scheduled::Where('scheduled_type', 'update-plan-monthly-price')
                ->Where('scheduled_status', 'new')
                ->Where('scheduled_payload_1', $package->package_id)
                ->delete();

            //schedule for cronjob
            $schedule = new \App\Models\Landlord\Scheduled();
            $schedule->scheduled_gateway = 'all';
            $schedule->scheduled_type = 'update-plan-monthly-price';
            $schedule->scheduled_payload_1 = $package->package_id;
            $schedule->save();
        }

        //update plan price (yearly)
        if ($package->package_amount_yearly != $old_package->package_amount_yearly) {

            //delete previously scheduled changes
            \App\Models\Landlord\Scheduled::Where('scheduled_type', 'update-plan-yearly-price')
                ->Where('scheduled_status', 'new')
                ->Where('scheduled_payload_1', $package->package_id)
                ->delete();

            //schedule for cronjob
            $schedule = new \App\Models\Landlord\Scheduled();
            $schedule->scheduled_gateway = 'all';
            $schedule->scheduled_type = 'update-plan-yearly-price';
            $schedule->scheduled_payload_1 = $package->package_id;
            $schedule->save();
        }

        //end
        return true;
    }

    /**
     * delete a package
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot delete the main demo plans. You can create new ones for testing');
        }

        //get the record
        $packages = $this->packagesrepo->search($id);
        $package = $packages->first();

        //check if there are any subsriptions on this package
        if (\App\Models\Landlord\Subscription::Where('subscription_package_id', $id)->exists()) {
            abort(409, __('lang.package_has_subscriptions_cannot_delete'));
        }

        //package has active subscriptions
        if ($package->count_subscriptions > 0) {
            abort(409, __('lang.package_has_subscriptions_cannot_delete'));
        }

        //delete package
        $package->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#package_' . $id,
            'action' => 'hide',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

    /**
     * archive a package
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function archive($id) {

        //[demo check]
        if (config('app.application_demo_mode') && in_array($id, [1, 2, 3])) {
            abort(409, 'Demo Mode: You cannot update the main demo plans. You can create new ones for testing');
        }

        //check if file exists in the database
        if (!$package = \App\Models\Landlord\Package::Where('package_id', $id)->first()) {
            abort(404);
        }

        $package->package_status = 'archived';
        $package->save();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#package_' . $id,
            'action' => 'hide',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

    /**
     * archive a package
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id) {

        //check if file exists in the database
        if (!$package = \App\Models\Landlord\Package::Where('package_id', $id)->first()) {
            abort(404);
        }

        $package->package_status = 'active';
        $package->package_featured = 'no';
        $package->save();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#package_' . $id,
            'action' => 'hide',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
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
                __('lang.packages'),
                (request('package_status') == 'archived') ? __('lang.archived') : __('lang.active'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.packages'),
            'heading' => __('lang.packages'),
            'page' => 'packages',
            'mainmenu_packages' => 'active',
        ];

        //return
        return $page;
    }
}