<?php

/** ---------------------------------------------------------------------------------------------------
 * Various cleanup and sanity task
 *
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord;
use Illuminate\Support\Facades\Log;

class CleanupCron {

    public function __invoke() {

        //[MT] - run this cron for landlord only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current()) {
                return;
            }
        }

        //boot config settings for landlord (not needed for tenants) (delete as needed)
        runtimeLandlordCronConfig();

        //requeue scheduled tasks that are stuck in 'processing'
        $this->requeueStuckScheduledTasks();
    }

    /**
     * Look for scheduled tasks that are stuck in the 'processing' status. If they still have some 'scheduled_attempts' left
     * update to [new] status so that they can be tried again
     *
     * @return null
     */
    private function requeueStuckScheduledTasks() {

        //log that its run
        Log::info("Cleaup process - requeuing scheduled items that are stuck in 'processing' - started", ['process' => '[landlord-cronjob][cleanup-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get the date_time for 1 hour ago
        $x_hours_ago = \Carbon\Carbon::now()->subHours(1)->format('Y-m-d H:i:s');

        //reset existing account owner
        \App\Models\Landlord\Scheduled::on('landlord')->Where('scheduled_status', 'processing')
            ->Where('scheduled_attempts', '<', 3)
            ->Where('scheduled_updated', '<', $x_hours_ago)
            ->update(['scheduled_status' => 'new']);

        Log::info("Cleaup process - requeuing scheduled items that are stuck in 'processing' - completed", ['process' => '[landlord-cronjob][cleanup-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return;
    }

}