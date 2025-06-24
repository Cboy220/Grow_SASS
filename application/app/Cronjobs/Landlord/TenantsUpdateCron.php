<?php

/** -------------------------------------------------------------------------------------------------
 * TenantsCronStatus
 * This cronjob is just used to record whether the tenant cronjobs have executed
 * This cron actually executes during the 'tenants' cron jobs run
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord;
use DB;
use Exception;
use Illuminate\Support\Facades\Schema;
use Log;

class TenantsUpdateCron {

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
        if (Schema::connection('landlord')->hasColumn('tenants', 'tenant_updating_current_version')) {
            $this->updateTenantsDB();
        }

    }

    /**
     * Update each tenant database
     */
    public function updateTenantsDB() {

        Log::info("[UPDATING] - tenants updating process has started. Looking for tenants to update - started", ['process' => '[update-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //current version
        $target_system_version = config('system.settings_version');

        //check if we have an x.sql file to match this version
        $filepath = BASE_DIR . "/updates/$target_system_version.sql";

        //only do the update if the file exists
        if (!file_exists($filepath)) {
            //log as info and not error
            Log::info("[UPDATING] - tenants updating process halted. The sql file ($target_system_version.sql) could not be found.It may not be required for this version ($target_system_version)", ['process' => '[update-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return;
        }

        //counts
        $count_passed = 0;
        $count_failed = 0;

        //get all customers with a version less than the system version and are not in (failed) or (processing) status
        $limit = 5;
        $customers = \App\Models\Landlord\Tenant::on('landlord')
            ->where('tenant_updating_status', 'completed')
            ->where(function ($query) use ($target_system_version) {
                $query->where('tenant_updating_current_version', '<', $target_system_version)
                    ->orWhereNull('tenant_updating_current_version');
            })
            ->take($limit)
            ->get();

        //count
        $count = $customers->count();

        //count how many we are updating
        if ($count == 0) {
            Log::info("[UPDATING] - no tenants were found that are eligable for an update to version ($target_system_version)", ['process' => '[update-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return;
        } else {
            Log::info("[UPDATING] - found ($count) tenants that are eligable for an update to version ($target_system_version)", ['process' => '[update-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //mark each tenant as updating
        foreach ($customers as $customer) {
            $customer->update([
                'tenant_updating_status' => 'processing',
                'tenant_updating_target_version' => $target_system_version,
            ]);
        }

        //update each tenant
        foreach ($customers as $customer) {

            \Spatie\Multitenancy\Models\Tenant::forgetCurrent();

            Log::info("[UPDATING] - updating database for tenant id (" . $customer->tenant_id . ") - domain (" . $customer->domain . ") - started", ['process' => '[update-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //get the customer from landlord db
            if ($tenant = \Spatie\Multitenancy\Models\Tenant::Where('tenant_id', $customer->tenant_id)->first()) {
                try {

                    /** ---------------------------------------------------------------------------------------------------------------------------
                     * 14 APRIL 2025 - V2.9
                     *
                     * Starting this version, the SQL file is executed section by section
                     *  - Each section is identfied by '-- [SQL BLOCK]' tag
                     *  - This enables only single failure points and not the whole file
                     *  - When different versions are merged, these blocks are added by the Python combining app
                     *  - If the file does not have any '-- [SQL BLOCK]' sections, the entire file will be treated as one block and processed
                     * 
                     * [NOTES]
                     *  - Ensure the SQL file ends with a '-- [SQL BLOCK]'
                     * --------------------------------------------------------------------------------------------------------------------------*/

                    //swicth to this tenants DB
                    $tenant->makeCurrent();

                    // Read the contents of the SQL file
                    $sql_content = file_get_contents($filepath);

                    // Split the SQL content into blocks based on the "-- [SQL BLOCK]" marker found in the sal file
                    $sql_blocks = preg_split('/-- \[SQL BLOCK\].*?(?:\R|$)/', $sql_content, -1, PREG_SPLIT_NO_EMPTY);

                    // If there were no "-- [SQL BLOCK]" markers, treat the entire file as a single block
                    if (count($sql_blocks) == 0) {
                        $sql_blocks = [$sql_content];
                    }

                    // Loop through each version block and execute it
                    foreach ($sql_blocks as $sql_block) {
                        try {
                            // Skip empty blocks
                            if (trim($sql_block) === '') {
                                continue;
                            }

                            // Execute the entire version block as a single operation
                            DB::connection('tenant')->unprepared($sql_block);

                        } catch (Exception $e) {
                            // Log the error but continue with the next version block
                            Log::error("[UPDATING] - Error executing SQL block for tenant {$customer->tenant_id}: " . $e->getMessage());
                        }
                    }

                    //update tenant record (in landlord db)
                    $customer->tenant_updating_status = 'completed';
                    $customer->tenant_updating_current_version = $target_system_version;
                    $customer->save();

                    //log this event
                    $log = new \App\Models\Landlord\Updatelog();
                    $log->setConnection('landlord');
                    $log->updateslog_tenant_id = $customer->tenant_id;
                    $log->updateslog_tenant_database = $customer->database;
                    $log->updateslog_current_version = $customer->tenant_updating_current_version;
                    $log->updateslog_target_version = $target_system_version;
                    $log->updateslog_status = 'completed';
                    $log->save();

                } catch (Exception $e) {

                    //update tenant record (in landlord db)
                    $customer->tenant_updating_status = 'failed';
                    $customer->tenant_updating_log = $e->getMessage();
                    $customer->save();

                    //log this error
                    $log = new \App\Models\Landlord\Updatelog();
                    $log->setConnection('landlord');
                    $log->updateslog_tenant_id = $customer->tenant_id;
                    $log->updateslog_tenant_database = $customer->database;
                    $log->updateslog_current_version = $customer->tenant_updating_current_version;
                    $log->updateslog_target_version = $target_system_version;
                    $log->updateslog_status = 'failed';
                    $log->updateslog_notes = $e->getMessage();
                    $log->save();

                    $count_failed++;
                    Log::error("[UPDATING] - updating database for tenant id (" . $customer->tenant_id . ") - domain (" . $customer->domain . ") - failed - see crm log table", ['process' => '[update-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                }
            }

        }

        Log::info("[UPDATING] - tenants updating process has finshed. passed ($count_passed) - failed ($count_failed)", ['process' => '[update-tenant-databases]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

    }

}