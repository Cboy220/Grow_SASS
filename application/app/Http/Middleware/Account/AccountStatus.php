<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [other] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Account;
use Closure;

class accountStatus {

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
        if (config('system.settings_type') == 'standalone') {
            return $next($request);
        }

        //get landlord settings
        $landlord_defaults = \App\Models\Landlord\Defaults::On('landlord')->Where('defaults_id', 1)->first();

        //maximum reached
        if (!in_array(config('system.settings_saas_status'), ['active', 'free-trial'])) {

            //client
            if (auth()->user()->is_client) {
                abort(409, __('lang.platform_not_currently_available'));
            }

            //regular team member
            if (auth()->user()->is_team && auth()->user()->role->role_id != 1) {
                abort(409, __('lang.platform_not_currently_available'));
            }

            //regular team member
            if (auth()->user()->is_team && auth()->user()->role->role_id == 1) {
                return redirect('/app/settings/account/notices');
            }
        }

        //smtp needs csetting up to continue
        if ($landlord_defaults->defaults_email_delivery == 'smtp_only' && config('system.settings_email_smtp_host') == '') {
            if (auth()->user()->is_team && auth()->user()->role->role_id == 1) {
                session(['smtp-required-warning' => 'show']);
                return redirect('/app/settings/account/email');
            }
        }

        return $next($request);

    }
}
