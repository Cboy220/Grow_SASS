<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Payments\IndexOfflineResponse;
use App\Repositories\Landlord\ProofOfPaymentRepository;

class OfflinePayments extends Controller {

    //repositories
    protected $paymentsrepo;

    public function __construct(
        ProofOfPaymentRepository $paymentsrepo
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
        return new IndexOfflineResponse($payload);
    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the record
        if (!$proof = \App\Models\Landlord\ProofOfPayment::Where('proof_id', $id)->first()) {
            abort(404);
        }

        //delete record
        $proof->delete();

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
                __('lang.offline') . ' (' . __('lang.proof_of_payment') . ')',
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.payments'),
            'heading' => __('lang.payments'),
            'page' => 'payments',
            'mainmenu_payments' => 'active',
            'submenu_offline' => 'active',
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