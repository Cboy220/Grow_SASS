<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\Payments\StoreUpdate;
use App\Http\Responses\Landlord\Payments\IndexResponse;
use App\Http\Responses\Landlord\Payments\StoreResponse;
use App\Http\Responses\Landlord\Payments\UpdateResponse;
use App\Repositories\Landlord\PaymentsRepository;

class Payments extends Controller {

    //repositories
    protected $paymentsrepo;

    public function __construct(
        PaymentsRepository $paymentsrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //repositories
        $this->paymentsrepo = $paymentsrepo;

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get customers
        $payments = $this->paymentsrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'payments' => $payments,
        ];

        //show the form
        return new IndexResponse($payload);
    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //current renewal cycle
        $renewal_cycle = '';
        $renewal_date = '';

        //show customer dropdown
        if (request()->filled('payment_tenant_id')) {
            config([
                'visibility.customer_hidden_id' => true,
            ]);
        } else {
            config([
                'visibility.customer_dropdown' => true,
            ]);
        }

        if (request()->filled('payment_tenant_id')) {
            if ($subscription = \App\Models\Landlord\Subscription::Where('subscription_customerid', request('payment_tenant_id'))->first()) {
                $renewal_cycle = runtimeRenewalPeriodSelector($subscription->subscription_gateway_billing_cycle, 'period');
                $renewal_date = runtimeRenewalPeriodSelector($subscription->subscription_gateway_billing_cycle, 'date');
            }
        }

        //some dates
        $format = (config('system.settings_system_datepicker_format') == 'mm-dd-yyyy') ? 'm-d-Y' : 'd-m-Y';
        $datepicker = [
            'one_week_from_today_mysql' => \Carbon\Carbon::now()->addWeeks(1)->format('Y-m-d'),
            'one_week_from_today_picker' => \Carbon\Carbon::now()->addWeeks(1)->format($format),
            'one_month_from_today_mysql' => \Carbon\Carbon::now()->addMonths(1)->format('Y-m-d'),
            'one_month_from_today_picker' => \Carbon\Carbon::now()->addMonths(1)->format($format),
            'one_year_from_today_mysql' => \Carbon\Carbon::now()->addYears(1)->format('Y-m-d'),
            'one_year_from_today_picker' => \Carbon\Carbon::now()->addYears(1)->format($format),
        ];

        //page
        $html = view('landlord/payments/modals/add-edit-inc', compact('renewal_cycle', 'renewal_date', 'datepicker'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLPaymentCreate',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdate $request) {

        //get the tenant
        if (!$customer = \App\Models\Landlord\Tenant::Where('tenant_id', request('payment_tenant_id'))->first()) {
            abort(409, __('lang.customer_could_not_be_found'));
        }

        //get the tenant
        if (!$subscription = \App\Models\Landlord\Subscription::Where('subscription_customerid', request('payment_tenant_id'))->first()) {
            abort(409, __('lang.customer_not_subscribed_to_any_plan'));
        }

        //store record
        $payment = new \App\Models\Landlord\Payment();
        $payment->payment_date = request('payment_date');
        $payment->payment_subscription_id = $subscription->subscription_id;
        $payment->payment_transaction_id = request('payment_transaction_id');
        $payment->payment_tenant_id = request('payment_tenant_id');
        $payment->payment_gateway = request('payment_gateway');
        $payment->payment_amount = request('payment_amount');
        $payment->save();

        //update subscription (if specified)
        if (request('subscription_renewal_options') == 'on') {

            //update status
            if (in_array(request('subscription_status'), ['active', 'awaiting-payment', 'cancelled', 'failed'])) {
                //subscription
                \App\Models\Landlord\Subscription::where('subscription_customerid', request('payment_tenant_id'))
                    ->update(['subscription_status' => request('subscription_status')]);
                //tenant
                \App\Models\Landlord\Tenant::where('tenant_id', request('payment_tenant_id'))
                    ->update(['tenant_status' => request('subscription_status')]);
            }

            //update due dates
            if (request('subscription_renewal_period') != 'unchanged' && request()->filled('subscription_renewal_date')) {
                \App\Models\Landlord\Subscription::where('subscription_customerid', request('payment_tenant_id'))
                    ->update(['subscription_date_renewed' => now()]);
                \App\Models\Landlord\Subscription::where('subscription_customerid', request('payment_tenant_id'))
                    ->update(['subscription_date_next_renewal' => request('subscription_renewal_date')]);
            }
        }

        /** ----------------------------------------------
         * record event
         * ----------------------------------------------*/
        $event = new \App\Models\Landlord\Event();
        $event->event_creatorid = $customer->tenant_id;
        $event->event_type = 'subscription-paid';
        $event->event_creator_type = 'customer';
        $event->event_customer_id = $customer->tenant_id;
        $event->event_item_id = $customer->tenant_id;
        $event->event_payload_1 = $customer->tenant_name;
        $event->event_payload_2 = runtimeMoneyFormat(request('payment_amount'));
        $event->event_payload_3 = '';
        $event->save();

        /** ----------------------------------------------
         * send email to customer
         * ----------------------------------------------*/
        $data = [
            'amount' => runtimeMoneyFormat(request('payment_amount')),
        ];
        //customer
        $mail = new \App\Mail\Landlord\Customer\PaymentConfirmation($customer, $data, $payment);
        $mail->build();

        //count rows
        $payments = $this->paymentsrepo->search();

        //payload
        $payload = [
            'payments' => $payments,
            'count' => 1,
            'page' => $this->pageSettings(),
        ];

        //render
        return new StoreResponse($payload);

    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the payment
        if (!$payment = \App\Models\Landlord\Payment::Where('payment_id', $id)->first()) {
            abort(404);
        }

        //page
        $html = view('landlord/payments/modals/edit', compact('payment'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLPaymentCreate',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdate $request, $id) {

        //get the item
        if (!$payment = \App\Models\Landlord\Payment::Where('payment_id', $id)->first()) {
            abort(404);
        }

        //update record
        $payment->payment_amount = request('payment_amount');
        $payment->payment_date = request('payment_date');
        $payment->payment_gateway = request('payment_gateway');
        $payment->payment_transaction_id = request('payment_transaction_id');
        $payment->save();

        //get friendly row
        $payments = $this->paymentsrepo->search($id);

        //payload
        $payload = [
            'payments' => $payments,
            'payment' => $payments->first(),
            'page' => $this->pageSettings('update'),
        ];

        //return view
        return new UpdateResponse($payload);

    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the item
        if (!$payment = \App\Models\Landlord\Payment::Where('payment_id', $id)->first()) {
            abort(404);
        }

        //delete record
        $payment->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#payment_' . $id,
            'action' => 'slideup-slow-remove',
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
                __('lang.payments'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.payments'),
            'heading' => __('lang.payments'),
            'page' => 'payments',
            'mainmenu_payments' => 'active',
            'submenu_online' => 'active',
        ];

        if ($section == 'index' || $section == 'update') {
            if (!request()->filled('source')) {
                config([
                    'visibility.col_tenant_name' => true,
                    'visibility.col_payment_gateway' => true,
                ]);
            }
        }

        //return
        return $page;
    }
}