<?php

/** ---------------------------------------------------------------------------------------------------------------
 * [NEXTLOOPS]
 * User roles - Primary admin (owner)
 *
 *
 *
 * @package    Grow CRM
 * @author     NextLoop
 * @revised    9 July 2021
 *--------------------------------------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Landlord;
use Closure;

class PrimaryAdmin {

    public $settings;

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate the user is the primay admin
        if (auth()->user()->primary_admin == 'yes') {
            return $next($request);

        }

        abort(403);

    }

}