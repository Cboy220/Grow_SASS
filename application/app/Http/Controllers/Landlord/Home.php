<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Home\IndexResponse;

class Home extends Controller {

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

        //top panel stats
        $stats = $this->topStats();

        //[income][yearly]
        $payload['income'] = $this->yearlyIncome([
            'period' => 'this_year',
        ]);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'stats' => $stats,
            'income' => $this->yearlyIncome(),
        ];

        //show the form
        return new IndexResponse($payload);
    }

    /**
     * top stats
     *
     * @param  int  $id
     * @return array
     */
    public function topStats() {

        //vars
        $stats = [];

        //dates
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $this_year_start = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
        $this_year_end = \Carbon\Carbon::now()->endOfYear()->format('Y-m-d');
        $this_month_start = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $this_month_end = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

        //income - today
        $stats['income_today'] = \App\Models\Landlord\Payment::where('payment_date', $today)->sum('payment_amount');

        //income - this month
        $stats['income_this_month'] = \App\Models\Landlord\Payment::where('payment_date', '>=', $this_month_start)
            ->where('payment_date', '<=', $this_month_end)
            ->sum('payment_amount');

        //income - this year
        $stats['income_this_year'] = \App\Models\Landlord\Payment::where('payment_date', '>=', $this_year_start)
            ->where('payment_date', '<=', $this_year_end)
            ->sum('payment_amount');

        //count records
        $stats['count_customers'] = \App\Models\Landlord\Tenant::count();

        //return
        return $stats;
    }

    /**
     * yearly income graph
     *
     * @param  int  $id
     * @return array
     */
    public function yearlyIncome() {

        $year = \Carbon\Carbon::now()->format('Y');

        //vars
        $stats = [
            'total' => 0,
            'monthly' => [],
            'year' => $year,
        ];

        //every month of the year
        for ($i = 1; $i <= 12; $i++) {
            //amount
            $start_date = \Carbon\Carbon::create($year, $i)->startOfMonth()->format('Y-m-d');
            $end_date = \Carbon\Carbon::create($year, $i)->lastOfMonth()->format('Y-m-d');

            //amount
            $amount = \App\Models\Landlord\Payment::where('payment_date', '>=', $start_date)
                ->where('payment_date', '<=', $end_date)
                ->sum('payment_amount');

            //get income for the month
            $stats['monthly'][] = $amount;
            //running total
            $stats['total'] += $amount;
        }

        return $stats;

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
                __('lang.sales'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.home'),
            'heading' => __('lang.home'),
            'page' => 'home',
            'mainmenu_home' => 'active',
        ];

        //return
        return $page;
    }
}