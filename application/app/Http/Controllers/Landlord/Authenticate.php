<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for authentication
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Authentication\AuthenticateResponse;
use App\Mail\Landlord\Admin\ForgotPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Validator;

class Authenticate extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //guest
        $this->middleware('guest')->except([
            'updatePassword',
        ]);

        //logged in
        $this->middleware('auth')->only([
            'updatePassword',
        ]);
    }

    /**
     * Display the login form
     * @return \Illuminate\Http\Response
     */
    public function logIn() {

        //show login page
        Auth::logout();
        return view('landlord/authentication/login');
    }

    /**
     * Display the forgot password form
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword() {
        //show login page
        return view('landlord/authentication/forgotpassword');
    }

    /**
     * Display the reset password form
     * @return \Illuminate\Http\Response
     */
    public function resetPassword() {

        //1 hour expiry
        $expiry = \Carbon\Carbon::now()->subHours(1);

        //validate code
        if (\App\Models\User::Where('forgot_password_token', request('token'))
            ->where('forgot_password_token_expiry', '>=', $expiry)
            ->doesntExist()) {
            //set flass session
            request()->session()->flash('error-notification-longer', __('lang.url_expired_or_invalid'));
            //redirect
            return redirect('forgotpassword');
        }

        //show login page
        return view('landlord/authentication/resetpassword');
    }

    /**
     * process login request
     * @return \Illuminate\Http\Response
     */
    public function logInAction(Request $request) {

        //check credentials
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {
            if (auth()->user()->status != 'active') {
                auth()->logout();
                abort(409, __('lang.account_has_been_suspended'));
            }
        } else {
            //login failed message
            abort(409, __('lang.invalid_login_details'));
        }

        $payload = [
            'type' => request('action'),
        ];

        //show the form
        return new AuthenticateResponse($payload);
    }

    /**
     * process forgot password request
     * @return \Illuminate\Http\Response
     */
    public function forgotPasswordAction() {

        //validation
        if (!$user = \App\Models\User::Where('email', request('email'))->first()) {
            abort(409, __('lang.account_not_found'));
        }

        $code = Str::random(50);

        //update user - set expiry to 3 Hrs
        $user->forgot_password_token = $code;
        $user->forgot_password_token_expiry = \Carbon\Carbon::now()->addHours(3);
        $user->save();

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
         Mail::to($user->email)->send(new ForgotPassword($user));


        //set flash session
        request()->session()->flash('success-notification-longer', __('lang.password_reset_email_sent'));

        //back to login
        $jsondata['redirect_url'] = url('app-admin/login');
        return response()->json($jsondata);
    }

    /**
     * process reset password request
     * @return \Illuminate\Http\Response
     */
    public function resetPasswordAction() {

        //1 hour expiry
        $expiry = \Carbon\Carbon::now()->subHours(1);

        $messages = [];

        //validate code
        if (\App\Models\User::Where('forgot_password_token', request('token'))
            ->where('forgot_password_token_expiry', '>=', $expiry)
            ->doesntExist()) {
            //set flass session
            request()->session()->flash('error-notification-longer', __('lang.url_expired_or_invalid'));
            //back to login
            $jsondata['redirect_url'] = url('forgotpassword');
            //redirect
            return response()->json($jsondata);
        }

        //validate password match
        $validator = Validator::make(request()->all(), [
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        $user = \App\Models\User::Where('forgot_password_token', request('token'))->first();
        $user->password = Hash::make(request('password'));
        $user->forgot_password_token = '';
        $user->save();

        //set flass session
        request()->session()->flash('success-notification-longer', __('lang.password_reset_success'));
        //back to login
        $jsondata['redirect_url'] = url('app-admin/login');
        return response()->json($jsondata);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //Login
        if ($section == 'login') {
            $page = [
                'meta_title' => __('lang.login_to_you_account'),
            ];
        }

        //Signup
        if ($section == 'signup') {
            $page = [
                'meta_title' => __('lang.create_a_new_account'),
            ];
        }

        //Forgot Password
        if ($section == 'forgot-password') {
            $page = [
                'meta_title' => __('lang.forgot_password'),
            ];
        }
        //return
        return $page;
    }

}