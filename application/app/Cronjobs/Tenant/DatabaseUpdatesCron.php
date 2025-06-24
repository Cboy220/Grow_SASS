<?php

/** -------------------------------------------------------------------------------------------------
 * TEMPLATE
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Tenant;

class DatabaseUpdatesCron {

    public function __invoke() {

        //[MT] - run config settings for landlord
        runtimeLandlordCronConfig();

        //do not run this if setup has not completed
        if (env('SETUP_STATUS') != 'COMPLETED') {
            //skip this provider
            return;
        }

    }

    /**
     * Update various customer subsccription statuses
     */
    public function runUpdates() {

        //TODO

    }

}