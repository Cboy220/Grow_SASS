<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Log;
use Modules\ActivityReport\Repositories\ActivityReportRepository;
use PDF;

class Test extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //admin
        $this->middleware('adminCheck');
    }

    /**
     * do something
     *
     * @return bool
     */
    public function index(ActivityReportRepository $reportrepo) {


    }

    /**
     * do something
     *
     * @return bool
     */
    public function index2(ActivityReportRepository $reportrepo) {

        $start_date = \Carbon\Carbon::now()->format('Y-m-d');
        $end_date = \Carbon\Carbon::now()->format('Y-m-d');

        //invoices
        $data = $reportrepo->invoicesReport($start_date, $end_date);
        $pdf = PDF::loadView('activityreport::reports/invoices', compact('data'));
        $filename = __('activityreport::lang.invoices_report') . '.pdf';
        return $pdf->download($filename);

        //contracts
        $data = $reportrepo->invoicesReport($start_date, $end_date);
        $pdf = PDF::loadView('activityreport::reports/invoices', compact('data'));
        $filename = __('activityreport::lang.invoices_report') . '.pdf';
        return $pdf->download($filename);

    }

}