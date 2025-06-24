<?php

/** ---------------------------------------------------------------------------------------------------
 * Delete Databases
 * Delete databases that had marked for deletion
 *
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord\Scheduled;
use Exception;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class UpdateEmailDomain {

    public function __invoke() {

        //[MT] - run this cron for landlord only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current()) {
                return;
            }
        }

        //boot config settings for landlord (not needed for tenants) (delete as needed)
        runtimeLandlordCronConfig();

        //update the email domain for each tenant
        $this->updateDomain();
    }

    /**
     * Look for scheduled tasks to update the email domain for each tenant
     *
     * @return null
     */
    private function updateDomain() {

        //log that its run
        Log::info("Cronjob has started - (Update Email Domain)", ['process' => '[landlord-cronjob][update-email-domain]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get some scheduled tasks
        $limit = 1;

        if ($scheduled = \App\Models\Landlord\Scheduled::on('landlord')->Where('scheduled_type', 'update-email-domain')
            ->Where('scheduled_status', 'new')
            ->first()) {

            $scheduled->scheduled_status = 'processing';
            $scheduled->save();

            //count
            $count = 0;

            //get all customers
            if ($customers = \App\Models\Landlord\Tenant::on('landlord')->get()) {
                foreach ($customers as $customer) {

                    //update the landlord database
                    $customer->tenant_email_local_email = $customer->subdomain . '@' . $scheduled->scheduled_payload_1;
                    $customer->save();

                    Tenant::forgetCurrent();

                    //get the customer from landlord db
                    if ($tenant = Tenant::Where('tenant_id', $customer->tenant_id)->first()) {
                        try {
                            //swicth to this tenants DB
                            $tenant->makeCurrent();

                            //update the email domain
                            \App\Models\Settings::on('tenant')->where('settings_id', 1)
                                ->update([
                                    'settings_saas_email_local_address' => $customer->tenant_email_local_email,
                                ]);

                            $count++;

                        } catch (Exception $e) {
                            Log::info("Update email domain for this tenant - ID (" . $customer->tenant_id . ") failed - error: " . $e->getMessage(), ['process' => '[landlord-cronjob][update-email-domain]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        }
                    }
                }
            }

            //finished
            $scheduled->scheduled_status = 'completed';
            $scheduled->save();

            Log::info("Cronjob finished - (Update Email Domain) - [$count] customers updated", ['process' => '[landlord-cronjob][update-email-domain]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return;
        }

        Log::info("Cronjob finished - (Update Email Domain) - no scheduled tasks where found", ['process' => '[landlord-cronjob][update-email-domain]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return;
    }

}