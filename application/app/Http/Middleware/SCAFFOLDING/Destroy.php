<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] pre-check processes for fooos
 *
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Fooos;
use Closure;
use Log;

class Destroy {

    /**
     * This 'bulk actions' middleware does the following
     *   1. If the request was for a sinle item
     *         - single item actions must have a query string '?id=123'
     *         - this id will be merged into the expected 'ids' request array (just as if it was a bulk request)
     *   2. loop through all the 'ids' that are in the post request
     *
     * HTML for the checkbox is expected to be in this format:
     *   <input type="checkbox" name="ids[{{ $fooo->fooo_id }}]"
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //NOTE: F4 change [action-bar] to the action name e.g. [change-status]

        //for a single item request - merge into an $ids[x] array and set as if checkox is selected (on)
        if (is_numeric($request->route('fooo'))) {
            $ids[$request->route('fooo')] = 'on';
            request()->merge([
                'ids' => $ids,
            ]);
        }

        //loop through each fooo and check permissions
        if (is_array(request('ids'))) {

            //validate each item in the list exists
            foreach (request('ids') as $id => $value) {
                //only checked items
                if ($value == 'on') {
                    //validate
                    if (!$fooo = \App\Models\Fooo::Where('fooo_id', $id)->first()) {
                        abort(409, __('lang.one_of_the_selected_items_nolonger_exists'));
                    }
                }
            }

            //permission: does user have permission edit fooos
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_fooos < 3) {
                    abort(403, __('lang.permission_denied_for_this_item') . " - #$id");
                }
            }
            //client - no permissions
            if (auth()->user()->is_client) {
                //some checks here
            }

        } else {
            //no items were passed with this request
            Log::error("no delete items were sent with this list", ['middleware.delete.fooos', config('app.debug_ref'), basename(__FILE__), __line__]);
            abort(409);
        }

        //all is on - passed
        return $next($request);
    }
}
