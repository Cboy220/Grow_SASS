<?php

/** --------------------------------------------------------------------------------
 * This repository manages the creation of a new tenant database and also populating
 * it
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;
use DB;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;
use Spatie\Multitenancy\Models\Tenant;

class DatabaseRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /**
     * create a new database
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function createDatabase() {

        Log::info("creating a new tenant database - started", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //database name
        switch (env('DB_METHOD')) {
        case 'mysql_user':
            return $this->tenantDatabaseMysqlUser();
        case 'cpanel':
            return $this->tenantDatabaseCpanel();
        case 'plesk':
            return $this->tenantDatabasePlesk();
        default:
            return false;
            Log::error("creating a new tenant database - failed - a valid (DB_METHOD) was not found in the .env file", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }
    }

    /**
     * delete a new database
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteDatabase($database_name) {

        Log::info("deleteing a database ($database_name) - started", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //database name
        switch (env('DB_METHOD')) {
        case 'mysql_user':
            return $this->deleteDatabaseDirect($database_name);
        case 'cpanel':
            return $this->deleteDatabaseCpanel($database_name);
        case 'plesk':
            return $this->deleteDatabasePlesk($database_name);
        default:
            return false;
            Log::error("deleteting database ($database_name) - failed - a valid (DB_METHOD) was not found in the .env file", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }
    }

    /**
     * full process for creating mysql database during runtime for a tenant
     *  - create a new database
     *  - grant permissions on the database
     *
     * @return mixed database name or false
     */
    public function tenantDatabaseMysqlUser() {

        Log::info("create tenant database (method: mysql_user)  - started", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //create the database
        if (!$database_name = $this->createDatabaseDirect(env('DB_METHOD_PREFIX'))) {
            Log::error("create tenant database failed - database could not be created", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //add user to database - does not seem to be required because the mysql user that created the database already has permissions on the database
        /*
        if (!$this->grantDatabaseDirect($database_name, env('DB_USERNAME'), env('DB_HOST'))) {
        Log::error("create tenant database failed - granting database permission failed", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
        }
         */

        //all ok
        Log::info("create tenant database ($database_name) - completed", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $database_name;

    }

    /**
     * full process for creating mysql database during runtime for a tenant
     *  - create a new database
     *  - grant permissions on the database
     *
     * @return mixed database name or false
     */
    public function tenantDatabaseCpanel() {

        Log::info("create tenant database (method: cpanel) - started", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //create a new database
        $data = [
            'cpanel_api_key' => env('DB_METHOD_CPANEL_API_KEY'),
            'cpanel_username' => env('DB_METHOD_CPANEL_USERNAME'),
            'cpanel_url' => env('DB_METHOD_CPANEL_URL'),
        ];
        if (!$database_name = $this->createDatabaseCpanel($data)) {
            Log::error("create tenant database - failed - database could not be created", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //grant privileges on database
        $data['mysql_database'] = $database_name;
        $data['mysql_user'] = env('DB_METHOD_CPANEL_USER');
        if (!$this->privilegesCpanelDatabase($data)) {
            Log::error("create tenant database - failed - database could not be created", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //all ok
        Log::info("create tenant database ($database_name) - completed", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $database_name;
    }

    /**
     * full process for creating mysql database during runtime for a tenant
     *  - create a new database
     *  - grant permissions on the database
     *
     * @return mixed database name or false
     */
    public function tenantDatabasePlesk() {

        Log::info("create tenant database (method: plesk) - started", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //create a new database
        $data = [
            'plesk_api_key' => env('DB_METHOD_PLESK_API_KEY'),
            'plesk_url' => env('DB_METHOD_PLESK_URL'),
            'plesk_domain' => env('DB_METHOD_PLESK_DOMAIN'),
            'plesk_prefix' => env('DB_METHOD_PREFIX'),
            'create_database_user' => false,
        ];
        if (!$database = $this->createDatabasePlesk($data)) {
            Log::error("create tenant database - failed - database could not be created", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //set the database name
        $database_name = $database['database_name'];

        //all ok
        Log::info("create tenant database ($database_name) - completed", ['process' => '[database-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return $database_name;
    }

    /**
     * create a new database using the 'user/direct' method
     *  - database name and prefix must have maximum of 64 characters
     *
     * @param string prefix database prefix
     * @return mixed database name or false
     */
    public function createDatabaseDirect($prefix = '') {

        //database name
        $database_name = databaseName($prefix);

        Log::info("creating database started", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //create a new database
        try {
            DB::connection('tenant')->statement("CREATE DATABASE $database_name");
        } catch (Exception $e) {
            Log::critical("creating database failed (" . $e->getMessage() . ")", ['process' => '[create-tenant-database][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'database_name' => $database_name, 'tenant_id' => $customer->tenant_id]);
            return false;
        }

        Log::info("database ($database_name) created", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //return
        return $database_name;

    }

    /**
     * delete a database from the server
     *
     * @param string database_name db name
     * @return bool
     */
    public function deleteDatabaseDirect($database_name = '') {

        //valiate
        if ($database_name == '') {
            Log::error("deleting a database failed - no database name was provided", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //delete a new database
        try {
            DB::statement('DROP DATABASE IF EXISTS ' . $database_name);
        } catch (Exception $e) {
            Log::critical("deleting a database ($database_name) failed - (" . $e->getMessage() . ")", ['process' => '[create-tenant-database][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'database_name' => $database_name]);
            return false;
        }

        Log::info("deleting database ($database_name) - completed", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //return
        return true;

    }

    /**
     * create a new database using the Cpanel UAPI https://api.docs.cpanel.net/cpanel/tokens/
     *  - database name and prefix must have maximum of 64 characters
     *
     * @param array data
     * @return mixed database name or false
     */
    public function createDatabaseCpanel($data = '') {

        //validation
        if (!isset($data['cpanel_api_key'])) {
            Log::error("creating database failed - Cpanel API key is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['cpanel_username'])) {
            Log::error("creating database failed - Cpanel user name is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['cpanel_url'])) {
            Log::error("creating database failed - Cpanel URL is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //database name
        $database_name = databaseName($data['cpanel_username'] . '_');
        $database_name = strtolower($database_name);

        Log::info("creating database started", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //cpanel api details
        $cpanel_username = $data['cpanel_username'];
        $cpanel_api_key = $data['cpanel_api_key'];
        $url = cpanelCleanURL($data['cpanel_url']);
        $cpanel_url = "$url/execute/Mysql/create_database";

        //attempt to create the databse
        try {

            //connect to cpnel UAPI
            $response = Http::withHeaders([
                'Authorization' => "cpanel $cpanel_username:$cpanel_api_key",
            ])->withoutVerifying()->get($cpanel_url, [
                'name' => $database_name,
            ]);

            //get the json payload
            $response_data = $response->json();

            //check results
            if ($response->successful()) {
                if (isset($response_data['status']) && $response_data['status'] == 1) {
                    Log::info("creating database ($database_name) completed", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                    //success - return database name
                    return $database_name;
                }
            }

            //api error
            Log::error("creating database failed - cpanel api error", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'errors' => $response]);
            return false;

        } catch (Exception $e) {
            Log::error("creating database failed - " . $e->getMessage(), ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //error
        return false;
    }

    /**
     * delete a database from the server
     *
     * @param string database_name db name
     * @return bool
     */
    public function deleteDatabaseCpanel($database_name = '') {

        //api settings
        $cpanel_username = env('DB_METHOD_CPANEL_USERNAME');
        $cpanel_api_key = env('DB_METHOD_CPANEL_API_KEY');
        $cpanel_url = env('DB_METHOD_CPANEL_URL');

        //validate
        if ($database_name == '') {
            Log::error("deleting a database failed - no database name was provided", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //validate
        if ($cpanel_username == '') {
            Log::error("deleting a database failed - no cpanel username was provided", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //validate
        if ($cpanel_api_key == '') {
            Log::error("deleting a database failed - no cpanel api key was provided", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //validate
        if ($cpanel_url == '') {
            Log::error("deleting a database failed - no cpanel url was provided", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //endpoint url
        $url = $cpanel_url . '/execute/Mysql/delete_database';

        //attempt to create the databse
        try {

            //connect to cpnel UAPI
            $response = Http::withHeaders([
                'Authorization' => "cpanel $cpanel_username:$cpanel_api_key",
            ])->withoutVerifying()->get($url, [
                'name' => $database_name,
            ]);

            //get the json payload
            $response_data = $response->json();

            //check results
            if ($response->successful()) {
                if (!isset($response_data['cpanelresult']['error'])) {
                    Log::info("deleting database ($database_name) completed", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                    //success - return database name
                    return $database_name;
                }
            }

            //api error
            $error = isset($response_data['cpanelresult']['error']) ? $response_data['cpanelresult']['error'] : 'Unknown API error';
            Log::error("deleting database failed - cpanel api error: $error", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'errors' => $response]);
            return false;

        } catch (Exception $e) {
            Log::error("deleting database failed - " . $e->getMessage(), ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        Log::info("deleting database ($database_name) - completed", ['process' => '[database-repository][direct]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //return
        return true;

    }

    /**
     * create a new mysql user in Cpanel, using UAPI https://api.docs.cpanel.net/cpanel/tokens/
     * mysql username will be prefixed by the cpanel user name
     *
     * @param array data
     * @return mixed database name or false
     */
    public function createMySQLUserCpanel($data = '') {

        //validation
        if (!isset($data['cpanel_api_key'])) {
            Log::error("creating mysql user failed - Cpanel API key is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['cpanel_username'])) {
            Log::error("creating mysql user failed - Cpanel user name is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['cpanel_url'])) {
            Log::error("creating mysql user failed - Cpanel URL is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //database name
        $mysql_user = cpanelMysqlUsername($data['cpanel_username']);
        $mysql_password = generatePassword(12);

        Log::info("creating mysql user started", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //cpanel api details
        $cpanel_username = $data['cpanel_username'];
        $cpanel_api_key = $data['cpanel_api_key'];
        $url = cpanelCleanURL($data['cpanel_url']);
        $cpanel_url = "$url/execute/Mysql/create_user";

        //attempt to create the mysql user
        try {

            //connect to cpnel UAPI
            $response = Http::withHeaders([
                'Authorization' => "cpanel $cpanel_username:$cpanel_api_key",
            ])->withoutVerifying()->get($cpanel_url, [
                'name' => $mysql_user,
                'password' => $mysql_password,
            ]);

            //get the json payload
            $response_data = $response->json();

            //check results
            if ($response->successful()) {
                if (isset($response_data['status']) && $response_data['status'] == 1) {
                    Log::info("creating mysql user ($mysql_user) completed", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                    //success - return database name
                    return [
                        'username' => $mysql_user,
                        'password' => $mysql_password,
                    ];
                }
            }

            //api error
            Log::error("creating mysql user failed - cpanel api error", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'errors' => $response]);
            return false;

        } catch (Exception $e) {
            Log::error("creating mysql user failed - " . $e->getMessage(), ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //error
        return false;
    }

    /**
     * create a new mysql user in Cpanel, using UAPI https://api.docs.cpanel.net/cpanel/tokens/
     * mysql username will be prefixed by the cpanel user name
     *
     * @param array data
     * @return mixed database name or false
     */
    public function privilegesCpanelDatabase($data = '') {

        //validation
        if (!isset($data['cpanel_api_key'])) {
            Log::error("settings mysql privileges failed - Cpanel API key is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['cpanel_username'])) {
            Log::error("settings mysql privileges failed - Cpanel user name is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['cpanel_url'])) {
            Log::error("settings mysql privileges failed - Cpanel URL is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['mysql_user'])) {
            Log::error("settings mysql privileges failed - Mysql user is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['mysql_database'])) {
            Log::error("settings mysql privileges failed - mysql database is missing", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //database name
        $mysql_user = $data['mysql_user'];
        $mysql_database = $data['mysql_database'];

        Log::info("settings mysql privileges on ($mysql_database) started", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
        Log::info("note - this process requires a valid 'cpanel mysql user' see .env file for details", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //cpanel api details
        $cpanel_username = $data['cpanel_username'];
        $cpanel_api_key = $data['cpanel_api_key'];
        $url = cpanelCleanURL($data['cpanel_url']);
        $cpanel_url = "$url/execute/Mysql/set_privileges_on_database";

        //attempt to create the mysql user
        try {

            //connect to cpnel UAPI
            $response = Http::withHeaders([
                'Authorization' => "cpanel $cpanel_username:$cpanel_api_key",
            ])->withoutVerifying()->get($cpanel_url, [
                'user' => $mysql_user,
                'database' => $mysql_database,
                'privileges' => 'ALL PRIVILEGES',
            ]);

            //get the json payload
            $response_data = $response->json();

            //check results
            if ($response->successful()) {
                if (isset($response_data['status']) && $response_data['status'] == 1) {
                    Log::info("settings mysql privileges ($mysql_database) completed", ['process' => '[database-repository][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    //success - return database name
                    return true;
                }
            }

            //api error
            Log::error("settings mysql privileges failed - cpanel api error", ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'errors' => $response]);
            return false;

        } catch (Exception $e) {
            Log::error("settings mysql privileges failed - " . $e->getMessage(), ['process' => '[create-tenant-database][cpanel]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //error
        return false;
    }

    /**
     * create a plesk api key
     *
     * @param  array  $data payload
     * @return string database name
     */
    public function createPleskAPIKey($data = []) {

        Log::info("creating plesk API Key started", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validation
        if (!isset($data['plesk_username'])) {
            Log::error("creating plesk API Key failed - Plesk [username] is missing", ['process' => '[create-tenant-database][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['plesk_password'])) {
            Log::error("creating plesk API Key failed - Plesk [password] is missing", ['process' => '[create-tenant-database][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        // api endpoint
        $endpoint = $data['plesk_url'] . '/api/v2/auth/keys';

        try {
            $response = Http::withBasicAuth($data['plesk_username'], $data['plesk_password'])
                ->post($endpoint, [
                    'description' => 'Grow CRM API Key',
                ]);

            // Handle the response
            if ($response->successful()) {
                $response_data = $response->json();
                Log::info("creating plesk API Key (" . $response_data['key'] . ") completed", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return $response_data['key'];
            } else {
                $error = $response->body();
                Log::error("creating plesk API Key failed - error: $error", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::error("creating plesk API Key failed - error: $error", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

    }

    /**
     * create a plesk database
     *
     * @param  array  $data payload
     * @return string database name
     */
    public function createDatabasePlesk($data = []) {

        Log::info("creating database started", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        //validation
        if (!isset($data['plesk_api_key'])) {
            Log::error("creating database failed - Plesk [api key] is missing", ['process' => '[create-tenant-database][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['plesk_domain'])) {
            Log::error("creating database failed - Plesk [domain] is missing", ['process' => '[create-tenant-database][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //validation
        if (!isset($data['plesk_url'])) {
            Log::error("creating database failed - Plesk [plesk url] is missing", ['process' => '[create-tenant-database][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        // api endpoint
        $endpoint = $data['plesk_url'] . '/api/v2/databases';

        //create plesk database user
        if ($data['create_database_user'] === true) {
            if (!$user = $this->createPleskDatabaseUser($data)) {
                Log::error("creating database failed", ['process' => '[create-tenant-database][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
                return false;
            }
        } else {
            //just some null values
            $user['database_username'] = null;
            $user['database_passwod'] = null;
        }

        //database name
        $database_name = databaseName($data['plesk_prefix']);
        $database_name = strtolower($database_name);

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $data['plesk_api_key'],
            ])->post($endpoint, [
                'name' => $database_name,
                'type' => 'mysql',
                'parent_domain' => [
                    'name' => $data['plesk_domain'],
                ],
                'server_id' => 1,
            ]);

            // Handle the response
            if ($response->successful()) {
                $response_data = $response->json();
                Log::info("creating database ($database_name) completed", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

                //record database details for future use
                if (env('SETUP_STATUS') == 'COMPLETED') {
                    $db = new \App\Models\Landlord\Database();
                    $db->database_name = $database_name;
                    $db->database_type = 'plesk';
                    $db->database_payload_1 = $response_data['id'];
                    $db->save();
                }

                //return database
                return [
                    'database_name' => $database_name,
                    'database_username' => $user['database_username'],
                    'database_passwod' => $user['database_passwod'],
                ];
            } else {
                if ($response->status() === 401) {
                    Log::error("creating database failed - error: authentications failed", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    return false;
                }
                Log::error("creating database failed - error: " . $response->body(), ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::error("creating database failed - error: $error", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }

    /**
     * create plesk database user. User will have global access to all databases on the server
     *
     * @param  array  $data payload
     * @return array
     */
    private function createPleskDatabaseUser($data) {

        //username and password
        $password = generateSecurePassword();
        $username = databaseUserName();

        Log::info("creating a plesk database user ($username) - started", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);

        // api endpoint
        $endpoint = $data['plesk_url'] . "/api/v2/dbusers";

        //get a list of all domains and look for the specified one
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $data['plesk_api_key'],
            ])->post($endpoint, [
                'login' => $username,
                'password' => $password,
                'parent_domain' => [
                    'name' => $data['plesk_domain'],
                ],
                'server_id' => 1,
            ]);

            // Handle the response
            if ($response->successful()) {
                //get the json payload
                $response_data = $response->json();
                Log::info("user (" . $response_data['login'] . ") has been created", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return [
                    'database_username' => $username,
                    'database_passwod' => $password,
                ];

            } else {
                if ($response->status() === 401) {
                    Log::error("creating a plesk database user failed - error: authentications failed", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    return false;
                }
                Log::error("creating a plesk database user failed - error: " . $response->body(), ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::error("creating a plesk database user failed - error: $error", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * delete plesk database
     *
     * @param  array  $data payload
     * @return array
     */
    private function deleteDatabasePlesk($database_name) {

        Log::info("deleating a plesk database ($database_name) - started", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //api settings
        $plesk_api_key = env('DB_METHOD_PLESK_API_KEY');
        $plesk_url = env('DB_METHOD_PLESK_URL');

        //validate
        if ($database_name == '') {
            Log::error("deleting a database failed - no database name was provided", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //validate
        if ($plesk_api_key == '') {
            Log::error("deleting a database ($database_name) failed - a plesk api key name was provided", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //validate
        if ($plesk_url == '') {
            Log::error("deleting a database ($database_name) failed - the plesk url was provided", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        //get the database details (this record was created when the database was first created)
        if (!$db = \App\Models\Landlord\Database::On('landlord')->Where('database_name', $database_name)->Where('database_type', 'plesk')->first()) {
            Log::error("deleting a database ($database_name) failed - a record of this database could not be found in the (databases) table", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        }

        // api endpoint
        $endpoint = $plesk_url . "/api/v2/databases/" . $db->database_payload_1;

        //get a list of all domains and look for the specified one
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $plesk_api_key,
            ])->delete($endpoint);

            // Handle the response
            if ($response->successful()) {
                Log::info("database ($database_name) has been deleted", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return true;
            } else {
                if ($response->status() === 401) {
                    Log::error("deleting a plesk database ($database_name) failed - error: authentications failed", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    return false;
                }
                Log::error("deleting a plesk database ($database_name) failed - error: " . $response->body(), ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                return false;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            Log::error("deleting a plesk database ($database_name) failed - error: $error", ['process' => '[database-repository][plesk]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}