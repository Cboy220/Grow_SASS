<?php

/** -------------------------------------------------------------------------------------------------
 * TenantsCronStatus
 * This cronjob is just used to record whether the tenant cronjobs have executed
 * This cron actually executes during the 'tenants' cron jobs run
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord;

class TenantsCronStatus {

    public function __invoke() {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        //boot system settings
        runtimeLandlordCronConfig();

        //reset last cron run data (record in landlord db)
        \App\Models\Settings::On('landlord')->where('settings_id', 'default')
            ->update([
                'settings_cronjob_has_run_tenants' => 'yes',
                'settings_cronjob_last_run_tenants' => now(),
            ]);

    }

}