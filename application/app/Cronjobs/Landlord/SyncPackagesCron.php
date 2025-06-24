<?php

/** -------------------------------------------------------------------------------------------------
 * This cronjob is envoked will look for any packages that have been updated by the landlord and
 * will sync the changes with every tenants database
 *
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord;
use DB;
use Exception;
use Log;
use Spatie\Multitenancy\Models\Tenant;

class SyncPackagesCron {

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
        Log::info("Cronjob has started - (Syncronise Packages)", ['process' => '[landlord-cronjob][sync-packages-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //update the package features on the tenant database
        $this->SyncPackage();

        //forget
        \Spatie\Multitenancy\Models\Tenant::forgetCurrent();
    }

    /**
     * Sync any packages that have been updated with each tenants database
     *  @return array filename & filepath
     */
    public function SyncPackage() {

        //is there a package that needs to be synced (get just one at a time)
        if (!$package = \App\Models\Landlord\Package::On('landlord')
            ->Where('package_sync_status', 'awaiting-sync')
            ->first()) {
            Log::info("No packages were marked for update - finished", ['process' => '[landlord-cronjob][sync-packages-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return;
        }

        //mark processing
        $package->package_sync_status = 'syncing';
        $package->save();

        //are there any subscriptions using this package
        if (!$subscriptions = \App\Models\Landlord\Subscription::On('landlord')
            ->Where('subscription_package_id', $package->package_id)
            ->leftJoin('tenants', 'tenants.tenant_id', '=', 'subscriptions.subscription_customerid')
            ->get()) {

            //mark packages as synced
            $package->package_sync_status = 'synced';
            $package->package_sync_date = now();
            $package->save();

            return;
            Log::info("No subscriptions were found that use the updated package - finished", ['process' => '[landlord-cronjob][sync-packages-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //log count
        $count = 0;

        foreach ($subscriptions as $subscription) {

            \Spatie\Multitenancy\Models\Tenant::forgetCurrent();

            //get the customer from landlord db
            if ($customer = \Spatie\Multitenancy\Models\Tenant::Where('tenant_id', $subscription->tenant_id)->first()) {
                try {
                    //swicth to this tenants DB
                    $customer->makeCurrent();

                    //update the tenant record
                    DB::connection('tenant')
                        ->table('settings')
                        ->where('settings_id', 1)
                        ->update([
                            'settings_saas_package_limits_clients' => $package->package_limits_clients,
                            'settings_saas_package_limits_team' => $package->package_limits_team,
                            'settings_saas_package_limits_projects' => $package->package_limits_projects,
                            'settings_modules_projects' => ($package->package_module_projects == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_tasks' => ($package->package_module_tasks == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_invoices' => ($package->package_module_invoices == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_payments' => 'enabled',
                            'settings_modules_leads' => ($package->package_module_leads == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_knowledgebase' => ($package->package_module_knowledgebase == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_estimates' => ($package->package_module_estimates == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_expenses' => ($package->package_module_expense == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_notes' => 'enabled',
                            'settings_modules_subscriptions' => ($package->package_module_subscriptions == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_tickets' => ($package->package_module_tickets == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_calendar' => ($package->package_module_calendar == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_timetracking' => ($package->package_module_timetracking == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_reminders' => ($package->package_module_reminders == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_proposals' => ($package->package_module_proposals == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_contracts' => ($package->package_module_contracts == 'yes') ? 'enabled' : 'disabled',
                            'settings_modules_messages' => ($package->package_module_messages == 'yes') ? 'enabled' : 'disabled',
                        ]);

                    $count++;

                } catch (Exception $e) {
                    Log::error("error updating customers subscription status (" . $e->getMessage() . ")", ['process' => '[landlord-cronjob][sync-packages-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
                }
            }

        }

        Log::info("Cronjob has finished - updated ($count) tenant databases with the new package settings", ['process' => '[landlord-cronjob][sync-packages-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //mark package as synced
        $package->package_sync_status = 'synced';
        $package->package_sync_date = now();
        $package->save();

    }
}