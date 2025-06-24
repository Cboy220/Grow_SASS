<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Foos\IndexResponse;
use App\Repositories\Landlord\FoosRepository;

class Foos extends Controller {

    //repositories
    protected $foosrepo;

    public function __construct(
        FoosRepository $foosrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //repositories
        $this->foosrepo = $foosrepo;

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get foos
        $foos = $this->foosrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'foos' => $foos,
        ];

        //show the form
        return new IndexResponse($payload);
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
                __('lang.foos'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.foos'),
            'heading' => __('lang.foos'),
            'page' => 'foos',
            'mainmenu_foos' => 'active',
        ];

        

        //return
        return $page;
    }
}