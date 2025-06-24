<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;

use Log;

class AccountRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /**
     * close customer account of current tenant from their settings page
     *
     * @return \Illuminate\Http\Response
     */
    public function closeMyAccount() {

        Log::info("closing customer account process - started", ['process' => '[close-customer-account]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validate
        if (!$tenant = \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', config('system.settings_saas_tenant_id'))->first()) {
            Log::error("tenany could not be found in the landlord database", ['process' => '[close-customer-account]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        Log::info("customer account was found. domain (" . $tenant->domain . ") - tenant id (" . $tenant->tenant_id . ")", ['process' => '[close-customer-account]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //data needed in controller
        $data = [
            'customer_name' => $tenant->tenant_name,
            'customer_email' => $tenant->tenant_email,
            'customer_url' => 'https://' . $tenant->domain,
            'database_name' => $tenant->database,
        ];

        //delete subscription
        \App\Models\Landlord\Subscription::On('landlord')->Where('subscription_customerid', $tenant->tenant_id)->delete();

        //delete payment
        \App\Models\Landlord\Payment::On('landlord')->Where('payment_tenant_id', $tenant->tenant_id)->delete();

        //delete tenant
        \App\Models\Landlord\Tenant::On('landlord')->Where('tenant_id', $tenant->tenant_id)->delete();

        Log::info("closing customer account (" . $tenant->domain . ") process - completed", ['process' => '[close-customer-account]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //email admin
        return $data;
    }

}