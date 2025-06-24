<?php

/** -------------------------------------------------------------------------------------------------
 * TEMPLATE
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord;
use DB;
use Exception;
use Log;
use Spatie\Multitenancy\Models\Tenant;

class CustomerStatusCron {

    public function __invoke() {
        
        //[MT] - landlord only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current()) {
                return;
            }
        }

        
        //[MT] - run config settings for landlord
        runtimeLandlordCronConfig();

        //update statuses on the landlord DB
        $this->updateStatusesLandlordDB();

        //update statuses on the tenant DB
        $this->updateStatusesTenantDB();

        //forget
        \Spatie\Multitenancy\Models\Tenant::forgetCurrent();
    }

    /**
     * Update various customer subsccription statuses
     */
    public function updateStatusesLandlordDB() {

        $customers = \App\Models\Landlord\Tenant::on('landlord')->
            leftJoin('subscriptions', 'subscriptions.subscription_customerid', '=', 'tenants.tenant_id')
            ->get();

        //get settings
        $settings = \App\Models\Landlord\Settings::on('landlord')->where('settings_id', 'default')->first();

        foreach ($customers as $customer) {

            //[no subscription]
            if (!is_numeric($customer->subscription_id) && $customer->tenant_status != 'unsubscribed') {
                $customer->tenant_status = 'unsubscribed';
                $customer->save();
                continue;
            }

            //[free customer] - their status is not active
            if ($customer->subscription_type == 'free' && $customer->tenant_status != 'active') {
                $customer->tenant_status = 'active';
                $customer->save();
                continue;
            }

            //[wrong status]
            if ($customer->tenant_status != 'unsubscribed' && ($customer->tenant_status != $customer->subscription_status)) {
                $customer->tenant_status = $customer->subscription_status;
                $customer->save();
                continue;
            }

            //[free trial] check if free trial has an end date
            if ($customer->subscription_type == 'paid' && $customer->tenant_status == 'free-trial') {
                if ($customer->subscription_trial_end == '' || $customer->subscription_trial_end == '0000-00-00 00:00:00') {
                    $customer->tenant_status = 'awaiting-payment';
                    $customer->save();
                    continue;
                }
            }

            //[trial] check if free trial has not expired
            if ($customer->subscription_type == 'paid' && $customer->tenant_status == 'free-trial') {
                if (\Carbon\Carbon::parse($customer->subscription_trial_end)->isPast()) {
                    $customer->tenant_status = 'awaiting-payment';
                    $customer->save();
                    \App\Models\Landlord\Subscription::on('landlord')->where('subscription_customerid', $customer->tenant_id)->update(
                        [
                            'subscription_trial_end' => null,
                            'subscription_status' => 'awaiting-payment',
                        ]);
                    continue;
                }
            }

            //[expired] because no due date is set
            if ($customer->subscription_type == 'paid' && $customer->subscription_status == 'active') {
                if ($customer->subscription_date_next_renewal == '' || $customer->subscription_date_next_renewal == '0000-00-00 00:00:00') {
                    $customer->tenant_status = 'awaiting-payment';
                    $customer->save();
                    \App\Models\Landlord\Subscription::on('landlord')->where('subscription_customerid', $customer->tenant_id)->update(
                        [
                            'subscription_trial_end' => null,
                            'subscription_status' => 'awaiting-payment',
                        ]);
                    continue;
                }
            }

            //[expired] because the next_renewal date (+ allowance) has passed
            if ($customer->subscription_type == 'paid' && $customer->subscription_status == 'active') {
                if (\Carbon\Carbon::parse($customer->subscription_date_next_renewal)->addDays($settings->settings_system_renewal_grace_period)->isPast()) {
                    $customer->tenant_status = 'awaiting-payment';
                    $customer->save();
                    continue;
                }
            }

        }

    }

    /**
     * Update the tenant datase with new subscription and account status
     *  @return array filename & filepath
     */
    public function updateStatusesTenantDB() {

        $customers = \App\Models\Landlord\Tenant::on('landlord')->
            leftJoin('subscriptions', 'subscriptions.subscription_customerid', '=', 'tenants.tenant_id')
            ->get();

        foreach ($customers as $customer) {

            \Spatie\Multitenancy\Models\Tenant::forgetCurrent();

            //get the customer from landlord db
            if ($tenant_customer = \Spatie\Multitenancy\Models\Tenant::Where('tenant_id', $customer->tenant_id)->first()) {
                try {
                    //swicth to this tenants DB
                    $tenant_customer->makeCurrent();

                    //update the tenant record
                    DB::connection('tenant')
                        ->table('settings')
                        ->where('settings_id', 1)
                        ->update([
                            'settings_saas_status' => $customer->tenant_status,
                            'settings_saas_package_id' => $customer->subscription_package_id,
                        ]);

                    //forget tenant
                    Tenant::forgetCurrent();

                } catch (Exception $e) {
                    Log::critical("error updating customers subscription status (" . $e->getMessage() . ")", ['process' => '[customer-status-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
                }
            }

        }

    }
}