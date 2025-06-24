<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Payment;
use Illuminate\Http\Request;
use Log;

class PaymentsRepository {

    /**
     * The leads repository instance.
     */
    protected $payment;

    /**
     * Inject dependecies
     */
    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object payments collection
     */
    public function search($id = '') {

        $payments = $this->payment->newQuery();

        // all client fields
        $payments->selectRaw('*');

        //joins
        $payments->leftJoin('tenants', 'tenants.tenant_id', '=', 'payments.payment_tenant_id');

        //default where
        $payments->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_payment_id')) {
            $payments->where('payment_id', request('filter_payment_id'));
        }
        if (is_numeric($id)) {
            $payments->where('payment_id', $id);
        }

        //filters: tenant
        if (request()->filled('payment_tenant_id')) {
            $payments->where('payment_tenant_id', request('payment_tenant_id'));
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $payments->where(function ($query) {
                $query->orWhere('payment_created', '=', date('Y-m-d', strtotime(request('search_query'))));
                if (is_numeric(request('search_query'))) {
                    $query->orWhere('payment_amount', '=', request('search_query'));
                }
                $query->orWhere('tenant_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('payment_transaction_id', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('payment_gateway', 'LIKE', '%' . request('search_query') . '%');
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('payments', request('orderby'))) {
                $payments->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'customer':
                $payments->orderBy('tenant_name', request('sortorder'));
                break;
            case 'domain':
                $payments->orderBy('domain', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $payments->orderBy('payment_id', 'desc');
        }

        // Get the results and return them.
        return $payments->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $payment = new $this->payments;

        //data
        $payment->payment_categoryid = request('payment_categoryid');
        $payment->payment_creatorid = auth()->id();

        //save and return id
        if ($payment->save()) {
            return $payment->payment_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$payment = $this->payments->find($id)) {
            return false;
        }

        //general
        $payment->payment_categoryid = request('payment_categoryid');

        //save
        if ($payment->save()) {
            return $payment->payment_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }

}