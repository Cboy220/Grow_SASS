<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Events\IndexResponse;
use App\Repositories\Landlord\EventsRepository;

class Events extends Controller {

    //repositories
    protected $eventsrepo;

    public function __construct(
        EventsRepository $eventsrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //repositories
        $this->eventsrepo = $eventsrepo;

    }
    /**
     * Display the dashboard home page
     * @return blade view | ajax view
     */
    public function index() {

        //get events
        $events = $this->eventsrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
            'events' => $events,
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
                __('lang.events'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.events'),
            'heading' => __('lang.events'),
            'page' => 'events',
            'mainmenu_events' => 'active',
        ];

        

        //return
        return $page;
    }
}