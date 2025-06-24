<?php

/** ----------------------------------------------------------------------------------------
 * [SaaS]
 *
 * This middleware class handles precheck processes for setup processes
 *
 * @package    Grow CRM
 * @author     NextLoop
 * -----------------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Install;
use Auth;
use Closure;

class InstallSanity {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //Sanity - Logout users (just incase)
        try {
            Auth::logout();
        } catch (Exception $e) {

        }

        return $next($request);
    }

}