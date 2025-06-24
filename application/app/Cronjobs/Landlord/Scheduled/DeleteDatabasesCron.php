<?php

/** ---------------------------------------------------------------------------------------------------
 * Delete Databases
 * Delete databases that had marked for deletion
 *
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Landlord\Scheduled;
use App\Repositories\Landlord\DatabaseRepository;
use Illuminate\Support\Facades\Log;

class DeleteDatabasesCron {

    //repositories
    protected $databaserepo;

    public function __invoke(DatabaseRepository $databaserepo) {

        //[MT] - run this cron for landlord only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current()) {
                return;
            }
        }

        //boot config settings for landlord (not needed for tenants) (delete as needed)
        runtimeLandlordCronConfig();

        $this->databaserepo = $databaserepo;

        //delete databases
        $this->deleteDatabases();
    }

    /**
     * Look for scheduled tasks to update [plan names] at the payment gateway
     * These are changes that were initiated when landlord updated some details about a package
     *  - Updates will be done at all payment gateways, one by one
     *
     * @notes
     * The schedule processing is set to 1 task per cycle. This is important when more payment gateways are enabled
     * in order to avoid server timeouts
     *
     * @return null
     */
    private function deleteDatabases() {

        //log that its run
        Log::info("Cronjob has started - (Delete Databases)", ['process' => '[landlord-cronjob][delete-databases-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //get some scheduled tasks
        $limit = 1;
        $count = 0;
        
        if ($scheduled = \App\Models\Landlord\Scheduled::on('landlord')->Where('scheduled_type', 'delete-database')
            ->Where('scheduled_status', 'new')
            ->Where('scheduled_attempts', '<=', 3)
            ->take($limit)->get()) {

            //loop through each one
            foreach ($scheduled as $schedule) {

                //database
                $database_name = $schedule->scheduled_payload_1;

                //log
                Log::info("found a mysql database ($database_name) scheduled for deletion", ['process' => '[landlord-cronjob][delete-databases-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'schedule_id' => $schedule->scheduled_id]);

                //set to processing
                $schedule->scheduled_status = 'processing';
                $schedule->save();

                //delete database
                if (!$this->databaserepo->deleteDatabase($database_name)) {

                    //limit reached - marke for manual action
                    if ($schedule->scheduled_attempts == 2) {
                        $schedule->scheduled_manual_action_required = 'yes';
                        $schedule->scheduled_comments = __('lang.database_needs_to_be_deleted_manually');
                    }

                    //save
                    $schedule->scheduled_attempts = $schedule->scheduled_attempts + 1;
                    $schedule->save();
                    Log::error("mysql database ($database_name) could not be deleted", ['process' => '[landlord-cronjob][delete-databases-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'schedule_id' => $schedule->scheduled_id]);
                    continue;
                }

                $count ++;

                $schedule->scheduled_status = 'completed';
                $schedule->save();
                Log::info("mysql database ($database_name) deleted", ['process' => '[landlord-cronjob][delete-databases-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'schedule_id' => $schedule->scheduled_id]);
            }
        }

        Log::info("Cronjob has finished - Databases Deleted ($count)", ['process' => '[landlord-cronjob][delete-databases-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return;
    }

}