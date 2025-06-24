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
use Closure;

class InstallRedirect {

    /**
     * Redirect any 'none install' urls to the [/install] url
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if (request()->route()->getName() != 'install') {
            return redirect()->route('install');
        }

        return $next($request);
    }

}
