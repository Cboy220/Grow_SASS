<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use App\Models\Landlord\Event;
use Illuminate\Http\Request;
use Log;


class EventsRepository{



    /**
     * The leads repository instance.
     */
    protected $event;

    /**
     * Inject dependecies
     */
    public function __construct(Event $event) {
        $this->event = $event;
    }


        /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object events collection
     */
    public function search($id = '') {

        $events = $this->event->newQuery();

        // all client fields
        $events->selectRaw('*');

        //joins
        $events->leftJoin("users", "users.id", "=", "events.event_creatorid");
        $events->leftJoin("tenants", "tenants.tenant_id", "=", "events.event_customer_id");

        //default where
        $events->whereRaw("1 = 1");


        if (request()->filled('event_customer_id')) {
            $events->where('event_customer_id', request('event_customer_id'));
        }

        //sorting
        $events->orderBy('event_id', 'desc');


        // Get the results and return them.
        return $events->paginate(config('system.settings_system_pagination_limits'));
    }

}