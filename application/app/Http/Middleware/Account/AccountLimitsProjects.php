<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [other] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Account;
use Closure;

class accountLimitsProjects {

    /**
     * Inject any dependencies here
     *
     */
    public function __construct() {

    }

    /**
     * This middleware does the following
     *   1. validates that the foo exists
     *   2. checks users permissions to [edit] the foo
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //[MT] only
        if(config('system.settings_type') == 'standalone'){
            return $next($request);
        }

        
        //ignore for unlimited
        if (config('system.settings_saas_package_limits_projects') == -1) {
            return $next($request);
        }

        //check limits
        $count = \App\Models\Project::Where('project_type', 'project')->count();
        $allowed = config('system.settings_saas_package_limits_projects');

        //maximum reached
        if($count >= $allowed){
            abort(409, __('lang.maximum_limit_reached'));
        }

        return $next($request);

    }
}
