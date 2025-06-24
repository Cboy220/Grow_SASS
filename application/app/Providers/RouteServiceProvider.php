<?php

/** --------------------------------------------------------------------------------
 * This service provider routes the application
 * @package    Grow CRM
 * @author     Laravel
 *----------------------------------------------------------------------------------*/

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot() {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map() {

        $this->mapApiRoutes();

        //[NEXTLOOPS] if setup has not been completed - run setup routes
        if (!$this->isSetupComplete()) {
            return $this->mapSetupRoutes();
        }

        //[NEXTLOOP] set the route file for the [frontend]
        if ($this->isFrontendRequest()) {
            return $this->mapFrontendRoutes();
        }

        //[NEXTLOOP] set the route file for the [landlord]
        if ($this->isLandlordRequest()) {
            return $this->mapLandlordRoutes();
        }

        $this->mapTenantRoutes();
    }

    /**
     * [NEXTLOOP]
     * Define the "tenant" routes for the application.
     *
     * @return void
     */
    protected function mapTenantRoutes() {
        Route::middleware('tenant')
            ->namespace($this->namespace)
            ->group(
                function ($router) {
                    require base_path('routes/web.php');
                    require base_path('routes/custom/web.php');
                    require base_path('routes/tenant/tenant.php');
                }
            );
        Route::middleware('account')
            ->namespace($this->namespace)
            ->group(
                function ($router) {
                    require base_path('routes/account.php');
                    require base_path('routes/custom/web.php');
                }
            );
    }

    /**
     * //[MT]
     *
     * Define the "landlord" routes for the application.
     *
     * @return void
     */
    protected function mapSetupRoutes() {
        Route::middleware('install')
            ->namespace($this->namespace)
            ->group(
                function ($router) {
                    require base_path('routes/install/web.php');
                }
            );
    }

    /**
     * //[MT]
     *
     * Define the "landlord" routes for the application.
     *
     * @return void
     */
    protected function mapLandlordRoutes() {
        Route::middleware('landlord')
            ->namespace($this->namespace)
            ->group(
                function ($router) {
                    require base_path('routes/landlord/web.php');
                    require base_path('routes/custom/web.php');
                }
            );
    }

    /**
     * //[MT]
     *
     * Define the "landlord" routes for the application.
     *
     * @return void
     */
    protected function mapFrontendRoutes() {
        Route::middleware('frontend')
            ->namespace($this->namespace)
            ->group(
                function ($router) {
                    require base_path('routes/frontend/web.php');
                }
            );
    }

    /**
     * //[MT]
     * Define the "landlord" routes for the application.
     *
     * @return void
     */
    private function isSetupComplete() {

        //landloard domain is not specified in .env file
        if (env('SETUP_STATUS') == 'COMPLETED') {
            return true;
        }

        return false;
    }

    /**
     * //[MT]
     * Define the "landlord" routes for the application. To do this, we do the following checks
     *
     *  (1) Check if the domain name of the current request matches the LANDLORD_DOMAIN in .env file
     *  (2) check if the current url contains '/app-admin'
     *
     * @return void
     */
    private function isLandlordRequest() {

        //landloard domain is not specified in .env file
        if (env('LANDLORD_DOMAIN') == '') {
            return true;
        }

        //setup has not completed
        if (env('SETUP_STATUS') != 'COMPLETED') {
            return false;
        }

        //does the url contain '/app-admin' part
        if (strpos($this->app->request->url(), '/app-admin') === false) {
            return false;
        }

        //domains list
        $domains_list = explode(',', preg_replace('/\s+/', '', env('LANDLORD_DOMAIN')));

        //get the request host
        $host = str_replace('www.', '', $this->app->request->getHost());

        //request url matches the landlord one in .env
        if (in_array($host, $domains_list)) {
            return true;
        }

        return false;
    }

    /**
     * //[MT]
     * Define the "frontend" routes for the application. To do this, we do the following checks
     *
     *  (1) Check if the domain name of the current request matches the FRONTEND_DOMAIN in .env file
     *  (2) check if the current url contains '/app-admin'
     *
     * @return void
     */
    private function isFrontendRequest() {

        //frontend domain is not specified in .env file
        if (env('FRONTEND_DOMAIN') == '') {
            return true;
        }

        //setup has not completed
        if (env('SETUP_STATUS') != 'COMPLETED') {
            return false;
        }

        //the url must not contain '/app-admin' part
        if (strpos($this->app->request->url(), '/app-admin') !== false) {
            return false;
        }

        //get the request host
        $host = str_replace('www.', '', $this->app->request->getHost());

        //request url matches the frontend one in .env
        if ($host == trim(strtolower(env('FRONTEND_DOMAIN')))) {
            return true;
        }

        return false;
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes() {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(
                function ($router) {
                    require base_path('routes/api.php');
                    require base_path('routes/custom/api.php');
                }
            );
    }
}
