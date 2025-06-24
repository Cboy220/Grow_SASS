<?php

/** --------------------------------------------------------------------------------------------------------------------------
 * TenantsCronStatus
 * This cronjob is just used to run an sql fix/patch on all tenants at once
 * It looks for the file "patch.sql" in the "/updates" folder, it executes it on all
 * tenants at once and then deletes the file.
 * 
 * This cron is useful for general database updates/fixes that need to be applied to every single tenant database
 * 
 * @package    Grow CRM
 * @author     NextLoop
 * @runs       Every 5 minutes
 *--------------------------------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord;
use DB;
use Exception;
use Log;

class TenantsPatchingCron {

    public function __invoke() {

        //[MT] - landlord only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current()) {
                return;
            }
        }

        //[MT] - run config settings for landlord
        runtimeLandlordCronConfig();

        //only do this if the landord database is updated to v1.3 and above
        $this->patchTenantsDB();

    }

    /**
     * Update every tenant database
     */
    public function patchTenantsDB() {

        $count_patched = 0;
        $count_failed = 0;

        //check if we have a file named "patch.sql" in the "updates" foldewr
        $filepath = BASE_DIR . "/updates/patch.sql";

        //only do the update if the file exists
        if (!file_exists($filepath)) {
            return;
        }

        //save the sql file to variable (once) to save time
        $sql_query = file_get_contents($filepath);

        Log::info("tenants database patching cron - [patch.sql file found] - started", ['process' => '[patch-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get all customers with a version less than the system version and are not in (failed) or (processing) status
        $customers = \App\Models\Landlord\Tenant::on('landlord')->get();
        $count_tenants = count($customers);

        Log::info("number of tenants found ($count_tenants)", ['process' => '[patch-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //update each tenant
        foreach ($customers as $customer) {

            \Spatie\Multitenancy\Models\Tenant::forgetCurrent();

            Log::info("updating database for tenant id (" . $customer->tenant_id . ") - domain (" . $customer->domain . ") - started", ['process' => '[patch-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //get the customer from landlord db
            if ($tenant = \Spatie\Multitenancy\Models\Tenant::Where('tenant_id', $customer->tenant_id)->first()) {
                try {
                    //swicth to this tenants DB
                    $tenant->makeCurrent();

                    //execute the file - if there is an error, the error will be caught and the loop will continue to next tenant
                    DB::connection('tenant')->unprepared($sql_query);

                    $count_patched++;

                } catch (Exception $e) {
                    $count_failed++;
                    Log::error("updating database for tenant filed - tenant id (" . $customer->tenant_id . ") - domain (" . $customer->domain . ") - error: " . $e->getMessage(), ['process' => '[patch-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                }
            }
        }

        //delete the file
        try {
            unlink($filepath);
            Log::alert("tenant database patching [completed] - tenants found ($count_tenants) - tenants patched ($count_patched) - tenants failed ($count_failed)", ['process' => '[patch-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        } catch (Exception $e) {
            Log::error("the patch.sql file could not be deleted - error: " . $e->getMessage(), ['process' => '[patch-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }
    }

}