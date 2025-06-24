<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for various feeds
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Repositories\Landlord\TenantsRepository;
use Log;

class Feed extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //no search term provided
        if (request('term') == '') {
            $results = [];
            return response()->json($results);
        }

    }

    /**
     * ajax search results for company name
     * @permissions team members only
     * @return \Illuminate\Http\Response
     */
    public function customerNames(TenantsRepository $tenantrepo) {

        $feed = $tenantrepo->autocompleteFeed(request('term'));

        return response()->json($feed);
    }

}