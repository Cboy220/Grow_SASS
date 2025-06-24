<?php

/** -------------------------------------------------------------------------------------------------
 * TEMPLATE
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * Syncany changes the main admin (id:1) makes in their dashboard (name & email) and update the
 * user are synsced every 24hrs
 * landlord tenants database
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord;
use Exception;
use Log;
use Spatie\Multitenancy\Models\Tenant;

class SyncUsersCron {

    public function __invoke() {

        //[MT] - landlord only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current()) {
                return;
            }
        }

        //[MT] - run config settings for landlord
        runtimeLandlordCronConfig();
        
        //log that its run
        Log::info("Cronjob has started - (Sync SaaS Account Users)", ['process' => '[landlord-cronjob][sync-user-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //update the package features on the tenant database
        $this->syncTenants();

        //forget
        \Spatie\Multitenancy\Models\Tenant::forgetCurrent();
    }

    /**
     * connect to each tenants database and get their name and email Update the landlord database
     *  @return array filename & filepath
     */
    public function syncTenants() {

        //get customers that have not been synced in last 24hrs
        $limit = 5;
        $customers = \App\Models\Landlord\Tenant::On('landlord')
            ->Where('tenant_sync_status', 'awaiting-sync')
            ->take($limit)
            ->get();

        $count =0;
        
        foreach ($customers as $customer) {

            \Spatie\Multitenancy\Models\Tenant::forgetCurrent();

            //skip 'unsubscribed'
            if ($customer->tenant_status == 'unsubscribed') {
                continue;
            }

            //get the customer from landlord db
            if ($tenant_customer = \Spatie\Multitenancy\Models\Tenant::Where('tenant_id', $customer->tenant_id)->first()) {
                try {
                    //swicth to this tenants DB
                    $tenant_customer->makeCurrent();

                    //update settings
                    \App\Models\Users::On('tenant')
                        ->where('id', 1)
                        ->update([
                            'password' => $customer->tenant_status,
                        ]);

                } catch (Exception $e) {
                    Log::error("error updating customers subscription status (" . $e->getMessage() . ")", ['process' => '[landlord-cronjob][sync-user-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
                }
                $count ++;
            }

        }

        Log::info("Cronjob has finished - Synced Users ($count)", ['process' => '[landlord-cronjob][sync-user-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

    }
}