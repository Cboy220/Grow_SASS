<?php

/** --------------------------------------------------------------------------------
 * [NOTES Aug 2022]
 *   - The provider must run before all other servive providers in (config/app.php)
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UpdateServiceProvider extends ServiceProvider {

    /** -------------------------------------------------------------------------------
     * [21 July 2024] This is nolonger used in the SaaS version.
     * \application\app\Http\Middleware\Landlord\Updates.php is used instead
     * This provider will be kept empty, but not deleted as it is called in kernel
     * ----------------------------------------------------------------------------- */
    public function boot() {

        //do nothing
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
