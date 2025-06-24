<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for authentication
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OnetimeAuth extends Controller {

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The client instance.
     */
    protected $clientrepo;

    public function __construct() {

        //parent
        parent::__construct();

        //guest
        $this->middleware('guest');
    }

    /**
     * Login the admin user based on a key in the url and delete the key
     * @return \Illuminate\Http\Response
     */
    public function OnetimeAuthentication() {

        //reset
        Auth::logout();

        //get settings
        $settings = \App\Models\Settings::Where('settings_id', 1)->first();

        //get admin user
        $user = \App\Models\User::Where('id', 1)->first();

        //check if this is indeed first/ontime login
        if (request()->filled('id_key') && $settings->settings_saas_onetimelogin_key == request('id_key')) {

            //login the user
            Auth::login($user, true);

            //[awaiting-payment]
            if ($settings->settings_saas_onetimelogin_destination == 'payment') {
                //update db
                $settings->settings_saas_onetimelogin_key = null;
                $settings->settings_saas_onetimelogin_destination = null;
                $settings->save();
                return redirect('app/settings/account/notices');
            }

            //update db
            $settings->settings_saas_onetimelogin_key = null;
            $settings->settings_saas_onetimelogin_destination = null;
            $settings->save();
            return redirect('home');

        } else {
            //login
            return redirect('login');
        }
    }

}