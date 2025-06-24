<?php

/** --------------------------------------------------------------------------------
 * The contraller generates the paynow buttons for each payment gateway
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Account\Pay;
use App\Http\Controllers\Controller;
use App\Http\Responses\Account\Pay\Offline\PayNowDetailsResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;

class Offline extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('FileSecurityCheck')->only([
            'attachPaymentProof',
        ]);

    }

    /**
     * show the offline payment details
     * @return \Illuminate\Http\Response
     */
    public function payNowDetails() {

        $landlord_settings = \App\Models\Landlord\Settings::On('landlord')->Where('settings_id', 'default')->first();

        $payload = [
            'landlord_settings' => $landlord_settings,
        ];

        //create the pay now button
        return new PayNowDetailsResponse($payload);
    }

    /**
     * attach proof of payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attachPaymentProof() {

        //save the file in its own folder in the temp folder
        if ($file = request()->file('file')) {

            //get admin settings
            if (!$settings = \App\Models\Landlord\Settings::On('landlord')->Where('settings_id', 'default')->first()) {
                Log::error("unable to fetch admin settings", ['process' => '[proof-of-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return response()->json([
                    'success' => true,
                ]);
            }

            //get the tenant details
            if (!$tenant = \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', config('system.settings_saas_tenant_id'))->first()) {
                Log::error("the tenant with id (" . config('system.settings_saas_tenant_id') . ") could not be found", ['process' => '[proof-of-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return response()->json([
                    'success' => true,
                ]);
            }

            //get subscription
            if (!$subscription = \App\Models\Landlord\Subscription::On('landlord')->Where('subscription_customerid', config('system.settings_saas_tenant_id'))->first()) {
                Log::error("the subscription for tenant with id (" . config('system.settings_saas_tenant_id') . ") could not be found", ['process' => '[proof-of-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return response()->json([
                    'success' => true,
                ]);
            }            

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file name
            $extension = $file->getClientOriginalExtension();
            $filename = "Payment.$extension";

            //create directory
            Storage::makeDirectory("files/$directory");

            //save file to directory
            Storage::putFileAs("files/$directory", $file, $filename);

            /** ----------------------------------------------
             * record proof
             * ----------------------------------------------*/
            $proof = new \App\Models\Landlord\ProofOfPayment();
            $proof->setConnection('landlord');
            $proof->proof_tenant_id = config('system.settings_saas_tenant_id');
            $proof->proof_amount = 'bar';
            $proof->proof_date = now();
            $proof->proof_directory = $directory;
            $proof->proof_filename = $filename;
            $proof->proof_amount = $subscription->subscription_amount;
            $proof->proof_status = 'unread';
            $proof->save();

            /** ----------------------------------------------
             * send email to multiple admin
             * ----------------------------------------------*/
            $data = [
                'directory' => $directory,
                'filename' => $filename,
                'customer_name' => $tenant->tenant_name,
                'customer_email' => $tenant->tenant_email,
                'customer_url' => 'https://' . $tenant->domain,
                'button_url' => 'https://' . $settings->settings_base_domain . '/app-admin',
                'customer_status' => runtimeLang($tenant->tenant_status),
            ];
            //send to users
            if ($users = \App\Models\User::On('landlord')->Where('type', 'admin')->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\Landlord\Admin\OfflinePayment($user, $data, []);
                    $mail->build();
                }
            }
        }

        //success (default)
        return response()->json([
            'success' => true,
        ]);

    }

}