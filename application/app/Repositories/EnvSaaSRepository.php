<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all writing to the .env file
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use Log;

class EnvSaaSRepository {

    /**
     * The full file path to the .env file
     */
    protected $env_file_path;

    /**
     * Inject dependecies
     */
    public function __construct() {

        $this->env_file_path = BASE_DIR . '/application/.env';

        //validate
        if (!is_writable($this->env_file_path) || !is_file($this->env_file_path)) {
            Log::critical("the file is not writable (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }

    /**
     * Replace the database information in the .env file
     * save the file
     * @param array $data the payload
     * @return bool
     */
    public function updateLandlordDatabase($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //change LANDLORD_DB_DATABASE
        if (isset($data['LANDLORD_DB_DATABASE'])) {
            $new = "LANDLORD_DB_DATABASE=" . $data['LANDLORD_DB_DATABASE'];
            $env = preg_replace('/LANDLORD_DB_DATABASE=.*$/m', $new, $env);
        }

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        //failed
        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;

    }

    /**
     * Replace the [database method] information in the .env file
     * save the file
     * @param array $data the payload
     * @return bool
     */
    public function updateDatabaseMethodMsqlUser($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //change DB_METHOD
        if (isset($data['DB_METHOD'])) {
            $new = "DB_METHOD=" . $data['DB_METHOD'];
            $env = preg_replace('/DB_METHOD=.*$/m', $new, $env);
        }

        //change DB_METHOD_PREFIX
        if (isset($data['DB_METHOD_PREFIX'])) {
            $new = "DB_METHOD_PREFIX=" . $data['DB_METHOD_PREFIX'];
            $env = preg_replace('/DB_METHOD_PREFIX=.*$/m', $new, $env);
        }

        //change DB_METHOD_MYSQL_HOST
        if (isset($data['DB_METHOD_MYSQL_HOST'])) {
            $new = "DB_METHOD_MYSQL_HOST=" . $data['DB_METHOD_MYSQL_HOST'];
            $env = preg_replace('/DB_METHOD_MYSQL_HOST=.*$/m', $new, $env);
        }

        //change DB_METHOD_MYSQL_PORT
        if (isset($data['DB_METHOD_MYSQL_PORT'])) {
            $new = "DB_METHOD_MYSQL_PORT=" . $data['DB_METHOD_MYSQL_PORT'];
            $env = preg_replace('/DB_METHOD_MYSQL_PORT=.*$/m', $new, $env);
        }

        //change DB_METHOD_MYSQL_USER
        if (isset($data['DB_METHOD_MYSQL_USER'])) {
            $new = "DB_METHOD_MYSQL_USER=" . $data['DB_METHOD_MYSQL_USER'];
            $env = preg_replace('/DB_METHOD_MYSQL_USER=.*$/m', $new, $env);
        }

        //change DB_METHOD_MYSQL_PASSWORD
        if (isset($data['DB_METHOD_MYSQL_PASSWORD']) && $data['DB_METHOD_MYSQL_PASSWORD'] != '') {
            $new = 'DB_METHOD_MYSQL_PASSWORD="' . $data['DB_METHOD_MYSQL_PASSWORD'] . '"';
            $env = preg_replace('/DB_METHOD_MYSQL_PASSWORD=.*$/m', $new, $env);
        }

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        //failed
        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;

    }

    /**
     * Replace the [database method] information in the .env file
     * save the file
     * @param array $data the payload
     * @return bool
     */
    public function updateDatabaseMethodCpanel($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //change DB_METHOD
        if (isset($data['DB_METHOD'])) {
            $new = "DB_METHOD=" . $data['DB_METHOD'];
            $env = preg_replace('/DB_METHOD=.*$/m', $new, $env);
        }

        //change DB_METHOD_PREFIX
        if (isset($data['DB_METHOD_PREFIX'])) {
            $new = "DB_METHOD_PREFIX=" . $data['DB_METHOD_PREFIX'];
            $env = preg_replace('/DB_METHOD_PREFIX=.*$/m', $new, $env);
        }

        //change DB_METHOD_CPANEL_HOST
        if (isset($data['DB_METHOD_CPANEL_HOST'])) {
            $new = "DB_METHOD_CPANEL_HOST=" . $data['DB_METHOD_CPANEL_HOST'];
            $env = preg_replace('/DB_METHOD_CPANEL_HOST=.*$/m', $new, $env);
        }

        //change DB_METHOD_CPANEL_PORT
        if (isset($data['DB_METHOD_CPANEL_PORT'])) {
            $new = "DB_METHOD_CPANEL_PORT=" . $data['DB_METHOD_CPANEL_PORT'];
            $env = preg_replace('/DB_METHOD_CPANEL_PORT=.*$/m', $new, $env);
        }

        //change DB_METHOD_CPANEL_USER
        if (isset($data['DB_METHOD_CPANEL_USER'])) {
            $new = "DB_METHOD_CPANEL_USER=" . $data['DB_METHOD_CPANEL_USER'];
            $env = preg_replace('/DB_METHOD_CPANEL_USER=.*$/m', $new, $env);
        }

        //change DB_METHOD_CPANEL_PASSWORD
        if (isset($data['DB_METHOD_CPANEL_PASSWORD']) && $data['DB_METHOD_CPANEL_PASSWORD'] != '') {
            $new = 'DB_METHOD_CPANEL_PASSWORD="' . $data['DB_METHOD_CPANEL_PASSWORD'] . '"';
            $env = preg_replace('/DB_METHOD_CPANEL_PASSWORD=.*$/m', $new, $env);
        }

        //change DB_METHOD_CPANEL_USERNAME
        if (isset($data['DB_METHOD_CPANEL_USERNAME'])) {
            $new = "DB_METHOD_CPANEL_USERNAME=" . $data['DB_METHOD_CPANEL_USERNAME'];
            $env = preg_replace('/DB_METHOD_CPANEL_USERNAME=.*$/m', $new, $env);
        }

        //change DB_METHOD_CPANEL_API_KEY
        if (isset($data['DB_METHOD_CPANEL_API_KEY'])) {
            $new = "DB_METHOD_CPANEL_API_KEY=" . $data['DB_METHOD_CPANEL_API_KEY'];
            $env = preg_replace('/DB_METHOD_CPANEL_API_KEY=.*$/m', $new, $env);
        }

        //change DB_METHOD_CPANEL_URL
        if (isset($data['DB_METHOD_CPANEL_URL'])) {
            $new = "DB_METHOD_CPANEL_URL=" . $data['DB_METHOD_CPANEL_URL'];
            $env = preg_replace('/DB_METHOD_CPANEL_URL=.*$/m', $new, $env);
        }

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        //failed
        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;

    }

    /**
     * Replace the [database method] information in the .env file
     * save the file
     * @param array $data the payload
     * @return bool
     */
    public function updateDatabaseMethodPlesk($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //change DB_METHOD
        if (isset($data['DB_METHOD'])) {
            $new = "DB_METHOD=" . $data['DB_METHOD'];
            $env = preg_replace('/DB_METHOD=.*$/m', $new, $env);
        }

        //change DB_METHOD_PREFIX
        if (isset($data['DB_METHOD_PREFIX'])) {
            $new = "DB_METHOD_PREFIX=" . $data['DB_METHOD_PREFIX'];
            $env = preg_replace('/DB_METHOD_PREFIX=.*$/m', $new, $env);
        }

        //change DB_METHOD_PLESK_HOST
        if (isset($data['DB_METHOD_PLESK_HOST'])) {
            $new = "DB_METHOD_PLESK_HOST=" . $data['DB_METHOD_PLESK_HOST'];
            $env = preg_replace('/DB_METHOD_PLESK_HOST=.*$/m', $new, $env);
        }

        //change DB_METHOD_PLESK_PORT
        if (isset($data['DB_METHOD_PLESK_PORT'])) {
            $new = "DB_METHOD_PLESK_PORT=" . $data['DB_METHOD_PLESK_PORT'];
            $env = preg_replace('/DB_METHOD_PLESK_PORT=.*$/m', $new, $env);
        }

        //change DB_METHOD_PLESK_PASSWORD
        if (isset($data['DB_METHOD_PLESK_PASSWORD']) && $data['DB_METHOD_PLESK_PASSWORD'] != '') {
            $new = 'DB_METHOD_PLESK_PASSWORD="' . $data['DB_METHOD_PLESK_PASSWORD'] . '"';
            $env = preg_replace('/DB_METHOD_PLESK_PASSWORD=.*$/m', $new, $env);
        }

        //change DB_METHOD_PLESK_USERNAME
        if (isset($data['DB_METHOD_PLESK_USERNAME'])) {
            $new = "DB_METHOD_PLESK_USERNAME=" . $data['DB_METHOD_PLESK_USERNAME'];
            $env = preg_replace('/DB_METHOD_PLESK_USERNAME=.*$/m', $new, $env);
        }

        //change DB_METHOD_PLESK_API_KEY
        if (isset($data['DB_METHOD_PLESK_API_KEY'])) {
            $new = "DB_METHOD_PLESK_API_KEY=" . $data['DB_METHOD_PLESK_API_KEY'];
            $env = preg_replace('/DB_METHOD_PLESK_API_KEY=.*$/m', $new, $env);
        }

        //change DB_METHOD_PLESK_DOMAIN
        if (isset($data['DB_METHOD_PLESK_DOMAIN'])) {
            $new = "DB_METHOD_PLESK_DOMAIN=" . $data['DB_METHOD_PLESK_DOMAIN'];
            $env = preg_replace('/DB_METHOD_PLESK_DOMAIN=.*$/m', $new, $env);
        }

        //change DB_METHOD_PLESK_URL
        if (isset($data['DB_METHOD_PLESK_URL'])) {
            $new = "DB_METHOD_PLESK_URL=" . $data['DB_METHOD_PLESK_URL'];
            $env = preg_replace('/DB_METHOD_PLESK_URL=.*$/m', $new, $env);
        }

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        //failed
        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;

    }

    /**
     * Setup wizard process
     * save the .env file with updated information
     * @return bool
     */
    public function updateSettings($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //Landlord Domain
        $new = 'LANDLORD_DOMAIN="' . $data['LANDLORD_DOMAIN'] . '"';
        $env = preg_replace('/LANDLORD_DOMAIN=.*$/m', $new, $env);

        //frontend Domain
        $new = 'FRONTEND_DOMAIN="' . $data['FRONTEND_DOMAIN'] . '"';
        $env = preg_replace('/FRONTEND_DOMAIN=.*$/m', $new, $env);

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /**
     * Setup wizard process
     * save the .env file with updated information
     * @return bool
     */
    public function completeSetup($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        $domain_1 = request()->getHost();
        $domain_2 = $data['settings_base_domain'];

        if ($domain_1 == $domain_2) {
            $new = "LANDLORD_DOMAIN=\"$domain_1\"";
            $env = preg_replace('/LANDLORD_DOMAIN=.*$/m', $new, $env);
        } else {
            $new = "LANDLORD_DOMAIN=\"$domain_1, $domain_2\"";
            $env = preg_replace('/LANDLORD_DOMAIN=.*$/m', $new, $env);
        }

        //change APP_URL
        $new = "APP_URL=" . url('/');
        $env = preg_replace('/APP_URL=.*$/m', $new, $env);

        //change SETUP_STATUS
        $new = "SETUP_STATUS=COMPLETED\r\n";
        $env = preg_replace('/SETUP_STATUS=.*$/m', $new, $env);

        //change SESSION_DRIVER
        $new = "SESSION_DRIVER=database\r\n";
        $env = preg_replace('/SESSION_DRIVER=.*$/m', $new, $env);
        $env = preg_replace('/SESSION_DRIVER=file.*$/m', $new, $env);

        //change SESSION_DRIVER
        $new = "QUEUE_DRIVER=database\r\n";
        $env = preg_replace('/QUEUE_DRIVER=.*$/m', $new, $env);

        //enable logging
        $new = "APP_DEBUG=true\r\n";
        $env = preg_replace('/APP_DEBUG=.*$/m', $new, $env);

        //set app logging level
        $new = "APP_LOG_LEVEL=error\r\n";
        $env = preg_replace('/APP_LOG_LEVEL=.*$/m', $new, $env);

        //disable the debug toolbar
        $new = "APP_DEBUG_TOOLBAR=false\r\n";
        $env = preg_replace('/APP_DEBUG_TOOLBAR=.*$/m', $new, $env);

        //enable logging
        $new = "APP_DEBUG_JAVASCRIPT=false\r\n";
        $env = preg_replace('/APP_DEBUG_JAVASCRIPT=.*$/m', $new, $env);

        //enable logging
        $new = "APP_DEMO_MODE=false\r\n";
        $env = preg_replace('/APP_DEMO_MODE=.*$/m', $new, $env);

        //change APP_ENV
        $new = "APP_ENV=production\r\n";
        $env = preg_replace('/APP_ENV=.*$/m', $new, $env);

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /**
     * Setup wizard process
     * save the .env file with updated information
     * @return bool
     */
    public function updateAppKey($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //set a new app key
        $new = 'APP_KEY=' . $data['APP_KEY'];
        $env = preg_replace('/APP_KEY=.*$/m', $new, $env);

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

    /**
     * Update the frontend domain name
     * save the .env file with updated information
     * @return bool
     */
    public function updateFrontendDomain($domain = '') {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //frontend Domain
        $new = "FRONTEND_DOMAIN='$domain'";
        $env = preg_replace('/FRONTEND_DOMAIN=.*$/m', $new, $env);

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}