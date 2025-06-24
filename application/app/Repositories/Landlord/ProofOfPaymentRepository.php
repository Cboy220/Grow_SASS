<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\ProofOfPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProofOfPaymentRepository {

    /**
     * The leads repository instance.
     */
    protected $payment;

    /**
     * Inject dependecies
     */
    public function __construct(ProofOfPayment $payment) {
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
        $payments->leftJoin('tenants', 'tenants.tenant_id', '=', 'proof_of_payments.proof_tenant_id');

        //default where
        $payments->whereRaw("1 = 1");

        //filters: tenant
        if (request()->filled('proof_tenant_id')) {
            $payments->where('proof_tenant_id', request('proof_tenant_id'));
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $payments->where(function ($query) {
                $query->orWhere('proof_date', '=', date('Y-m-d', strtotime(request('search_query'))));
                if (is_numeric(request('search_query'))) {
                    $query->orWhere('proof_amount', '=', request('search_query'));
                }
                $query->orWhere('tenant_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('proof_filename', 'LIKE', '%' . request('search_query') . '%');
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('proof_of_payments', request('orderby'))) {
                $payments->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'domain':
                $payments->orderBy('domain', request('sortorder'));
                break;
            case 'tenant_name':
                $payments->orderBy('tenant_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $payments->orderBy('proof_id', 'desc');
        }

        // Get the results and return them.
        return $payments->paginate(config('system.settings_system_pagination_limits'));
    }

}