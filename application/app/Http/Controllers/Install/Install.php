<?php

/** --------------------------------------------------------------------------------
 * This controller manages the business logic for the setup wizard
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Install\Responses\DatabaseResponse;
use App\Http\Controllers\Install\Responses\FinishResponse;
use App\Http\Controllers\Install\Responses\IndexResponse;
use App\Http\Controllers\Install\Responses\RequirementsResponse;
use App\Http\Controllers\Install\Responses\SettingsResponse;
use App\Http\Controllers\Install\Responses\UserResponse;
use App\Repositories\EnvSaaSRepository;
use App\Repositories\Landlord\DatabaseRepository;
use App\Repositories\SpaceRepository;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Validator;

class Install extends Controller {

    /**
     * The repository instances.
     */
    protected $spacerepo;
    protected $databaserepo;

    public function __construct(SpaceRepository $spacerepo, DatabaseRepository $databaserepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('guest');

        $this->spacerepo = $spacerepo;
        $this->databaserepo = $databaserepo;
    }

    /**
     * Display Setup first page
     * @return blade view | ajax view
     */
    public function index() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
        ];

        // Clear the application cache and session data
        Auth::logout();
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        request()->session()->flush();

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Display Setup first page
     * @return blade view | ajax view
     */
    public function serverInfo() {

        //show the view
        return view('pages/install/serverinfo');
    }

    /**
     * [source] https://laravel.com/docs/7.x/installation#server-requirements
     * PHP >= 7.2.5
     * BCMath PHP Extension
     * Ctype PHP Extension
     * Fileinfo PHP extension
     * JSON PHP Extension
     * Mbstring PHP Extension
     * OpenSSL PHP Extension
     * PDO PHP Extension
     * Tokenizer PHP Extension
     * XML PHP Extension
     * @return blade view | ajax view
     */
    public function checkRequirements() {

        $error['count'] = 0;

        //server requirements checks
        $requirements['php_version'] = version_compare(PHP_VERSION, '8.2.0', ">=") && version_compare(PHP_VERSION, '8.4.0', "<");
        $requirements['bcmath'] = extension_loaded("bcmath");
        $requirements['mysql'] = extension_loaded("mysqli");
        $requirements['ctype'] = extension_loaded("ctype");
        $requirements['fileinfo'] = extension_loaded("fileinfo");
        $requirements['json'] = extension_loaded("json");
        $requirements['mbstring'] = extension_loaded("mbstring");
        $requirements['openssl'] = extension_loaded("openssl");
        $requirements['pdo'] = defined('PDO::ATTR_DRIVER_NAME');
        $requirements['tokenizer'] = extension_loaded("tokenizer");
        $requirements['xml'] = extension_loaded("xml");
        $requirements['gd'] = extension_loaded("gd");
        $requirements['gd'] = true; //[TOTO] - remove
        $requirements['fileinfo'] = extension_loaded("fileinfo");

        //directory (writable checks)
        $requirements['dir_updates'] = is_writable(BASE_DIR . '/updates');
        $requirements['dir_storage'] = is_writable(BASE_DIR . '/storage');
        $requirements['dir_storage_avatars'] = is_writable(BASE_DIR . '/storage/avatars');
        $requirements['dir_storage_frontend'] = is_writable(BASE_DIR . '/storage/avatars');
        $requirements['dir_storage_logos'] = is_writable(BASE_DIR . '/storage/logos');
        $requirements['dir_storage_logos_clients'] = is_writable(BASE_DIR . '/storage/logos/clients');
        $requirements['dir_storage_logos_app'] = is_writable(BASE_DIR . '/storage/logos/app');
        $requirements['dir_storage_files'] = is_writable(BASE_DIR . '/storage/files');
        $requirements['dir_storage_temp'] = is_writable(BASE_DIR . '/storage/temp');
        $requirements['dir_app_storage_app'] = is_writable(BASE_DIR . '/application/storage/app');
        $requirements['dir_app_storage_app_public'] = is_writable(BASE_DIR . '/application/storage/app/public');
        $requirements['dir_app_storage_cache'] = is_writable(BASE_DIR . '/application/storage/cache');
        $requirements['dir_app_storage_cache_data'] = is_writable(BASE_DIR . '/application/storage/cache/data');
        $requirements['dir_app_storage_debugbar'] = is_writable(BASE_DIR . '/application/storage/debugbar');
        $requirements['dir_app_storage_framework'] = is_writable(BASE_DIR . '/application/storage/framework');
        $requirements['dir_app_storage_framework_cache'] = is_writable(BASE_DIR . '/application/storage/framework/cache');
        $requirements['dir_app_storage_framework_cache_data'] = is_writable(BASE_DIR . '/application/storage/framework/cache/data');
        $requirements['dir_app_storage_framework_sessions'] = is_writable(BASE_DIR . '/application/storage/framework/sessions');
        $requirements['dir_app_storage_framework_testing'] = is_writable(BASE_DIR . '/application/storage/framework/testing');
        $requirements['dir_app_storage_framework_views'] = is_writable(BASE_DIR . '/application/storage/framework/views');
        $requirements['dir_app_storage_logs'] = is_writable(BASE_DIR . '/application/storage/logs');
        $requirements['dir_app_bootstrap_cache'] = is_writable(BASE_DIR . '/application/bootstrap/cache');
        $requirements['dir_app_storage_app_purifier'] = is_writable(BASE_DIR . '/application/storage/app/purifier');
        $requirements['dir_app_storage_app_purifier_html'] = is_writable(BASE_DIR . '/application/storage/app/purifier/HTML');

        //files (writable checks)
        $requirements['dir_app_env'] = is_writable(BASE_DIR . '/application/.env');

        //check if we had errors
        foreach ($requirements as $key => $value) {
            if (!$value) {
                $error['count']++;
            }
        }

        //store purchase code in a session
        request()->session()->flash('settings_purchase_code', request('purchase_code'));

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'requirements' => $requirements,
            'error' => $error,
        ];

        //show the view
        return new RequirementsResponse($payload);
    }

    /**
     * Display database form page
     * @return blade view | ajax view
     */
    public function showDatabase() {

        //store purchase code in a session
        request()->session()->flash('settings_purchase_code', session('settings_purchase_code'));

        $error['count'] = 0;

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'error' => $error,
        ];

        //show the view
        return new DatabaseResponse($payload);
    }

    /**
     * Update database based on mysql user
     * @return blade view | ajax view
     */
    public function updateDatabaseMySQL(EnvSaaSRepository $envrepo) {

        //store purchase code in a session
        request()->session()->flash('settings_purchase_code', session('settings_purchase_code'));

        $error['count'] = 0;

        //custom error messages
        $messages = [
            'database_config_mysql_username.required' => 'MySQL Username is required',
            'database_config_mysql_host.required' => 'MySQL Host is required',
            'database_config_mysql_port.required' => 'MySQL Port is required',
        ];

        //validate
        //validate
        $validator = Validator::make(request()->all(), [
            'database_config_mysql_username' => 'required',
            'database_config_mysql_host' => 'required',
            'database_config_mysql_port' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //check if password has disallowed characters"
        if (request()->filled('database_config_mysql_password')) {
            if (stripos(request('database_config_mysql_password'), '"')) {
                abort(409, 'You MySQL password contains a disallowed character "');
            }
        }

        //get details
        $mysql_host = request('database_config_mysql_host');
        $mysql_username = request('database_config_mysql_username');
        $mysql_password = request('database_config_mysql_password');
        $mysql_port = request('database_config_mysql_port');
        $mysql_prefix = request('database_config_mysql_prefix');

        // connect to mysql server and create a new database
        try {
            $conn = new \mysqli($mysql_host, $mysql_username, $mysql_password, '', $mysql_port);
            if ($conn->connect_error) {
                abort(409, 'Error (DB001: Unable to connect to the MySQL server with the details provided');
            }

            // Create a new database
            $database_name = databaseName($mysql_prefix);
            $sql = "CREATE DATABASE $database_name";
            if ($conn->query($sql) === false) {
                abort(409, 'Error (DB002: Unable to create a database with the details provided');
            }
        } catch (Exception $e) {
            abort(409, 'Error (DB003: Unable to connect to the MySQL server with the details provided');
        }

        // close the connection
        $conn->close();

        //update the .env file (landlord database)
        $data = [
            'LANDLORD_DB_DATABASE' => $database_name,
        ];
        if (!$envrepo->updateLandlordDatabase($data)) {
            abort(409, __('Error (DB004:  Unable to save the (.env) file'));
        }

        //update the .env file (database method)
        $data = [
            'DB_METHOD' => 'mysql_user',
            'DB_METHOD_PREFIX' => $mysql_prefix,
            'DB_METHOD_MYSQL_HOST' => $mysql_host,
            'DB_METHOD_MYSQL_PORT' => $mysql_port,
            'DB_METHOD_MYSQL_USER' => $mysql_username,
            'DB_METHOD_MYSQL_PASSWORD' => $mysql_password,
        ];
        if (!$envrepo->updateDatabaseMethodMsqlUser($data)) {
            abort(409, __('Error (DB005:  Unable to save the (.env) file'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'error' => $error,
        ];

        //show the view
        return new SettingsResponse($payload);

    }

    /**
     * Update database based on cpanel uapi
     * @return blade view | ajax view
     */
    public function updateDatabaseCpanel(EnvSaaSRepository $envrepo) {

        //store purchase code in a session
        request()->session()->flash('settings_purchase_code', session('settings_purchase_code'));

        $error['count'] = 0;

        //custom error messages
        $messages = [
            'database_config_cpanel_username.required' => 'Cpanel username is required',
            'database_config_cpanel_api_key.required' => 'Cpanel API key is required',
            'database_config_cpanel_api_url.required' => 'Cpanel URL is required',
            'database_config_cpanel_host.required' => 'MySQL host is required',
            'database_config_cpanel_port.required' => 'MySQL port is required',
        ];

        //validate
        //validate
        $validator = Validator::make(request()->all(), [
            'database_config_cpanel_username' => 'required',
            'database_config_cpanel_api_key' => 'required',
            'database_config_cpanel_api_url' => 'required',
            'database_config_cpanel_host' => 'required',
            'database_config_cpanel_port' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //validate Cpanel URL
        if (!validateCpanelURL(request('database_config_cpanel_api_url'))) {
            abort(409, 'Invalid Cpanel URL. Make sure you are using the port (:2083) Example - https://yourdomain.com:2083');
        }

        //create a new database
        $data = [
            'cpanel_api_key' => request('database_config_cpanel_api_key'),
            'cpanel_username' => request('database_config_cpanel_username'),
            'cpanel_url' => request('database_config_cpanel_api_url'),
        ];
        if (!$database_name = $this->databaserepo->createDatabaseCpanel($data)) {
            abort(409, 'Error DB001 - Unable to create a database with the details provided');
        }

        //create new mysql user
        if (!$mysql_user = $this->databaserepo->createMySQLUserCpanel($data)) {
            abort(409, 'Unable to create a database with the details provided');
        }

        //grant privileges on database
        $data['mysql_database'] = $database_name;
        $data['mysql_user'] = $mysql_user['username'];
        if (!$this->databaserepo->privilegesCpanelDatabase($data)) {
            abort(409, 'Error DB002 - Unable to create a database with the details provided');
        }

        //get details
        $mysql_host = request('database_config_cpanel_host');
        $mysql_username = $mysql_user['username'];
        $mysql_password = $mysql_user['password'];
        $mysql_port = request('database_config_cpanel_port');
        $mysql_prefix = '';

        //update the .env file (landlord database)
        $data = [
            'LANDLORD_DB_DATABASE' => $database_name,
        ];
        if (!$envrepo->updateLandlordDatabase($data)) {
            abort(409, __('Error DB003:  Unable to save the (.env) file'));
        }

        //update the .env file (database method)
        $data = [
            'DB_METHOD' => 'cpanel',
            'DB_METHOD_PREFIX' => '',
            'DB_METHOD_CPANEL_HOST' => $mysql_host,
            'DB_METHOD_CPANEL_PORT' => $mysql_port,
            'DB_METHOD_CPANEL_USER' => $mysql_username,
            'DB_METHOD_CPANEL_PASSWORD' => $mysql_password,
            'DB_METHOD_CPANEL_USERNAME' => request('database_config_cpanel_username'),
            'DB_METHOD_CPANEL_API_KEY' => request('database_config_cpanel_api_key'),
            'DB_METHOD_CPANEL_URL' => cpanelCleanURL(request('database_config_cpanel_api_url')),
        ];
        if (!$envrepo->updateDatabaseMethodCpanel($data)) {
            abort(409, __('Error DB004:  Unable to save the (.env) file'));
        }

        //update the .env file (database method)
        $data = [
            'APP_KEY' => random_string(32),
        ];
        if (!$envrepo->updateAppKey($data)) {
            abort(409, __('Error (DB005:  Unable to save the (.env) file'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'error' => $error,
        ];

        //show the view
        return new SettingsResponse($payload);

    }

    /**
     * Update database based on plesk api data
     * @return blade view | ajax view
     */
    public function updateDatabasePlesk(EnvSaaSRepository $envrepo) {

        //store purchase code in a session
        request()->session()->flash('settings_purchase_code', session('settings_purchase_code'));

        $error['count'] = 0;

        //custom error messages
        $messages = [
            'database_config_plesk_username.required' => 'Plesl username is required',
            'database_config_plesk_password.required' => 'Plesk password is required',
            'database_config_plesk_domain.required' => 'Plesk domain is required',
            'database_config_plesk_url.required' => 'Plesk panel url is required',
            'database_config_plesk_host.required' => 'MySQL host is required',
            'database_config_plesk_port.required' => 'MySQL port is required',
        ];

        //validate
        //validate
        $validator = Validator::make(request()->all(), [
            'database_config_plesk_username' => 'required',
            'database_config_plesk_password' => 'required',
            'database_config_plesk_domain' => 'required',
            'database_config_plesk_url' => 'required',
            'database_config_plesk_host' => 'required',
            'database_config_plesk_port' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //validate Cpanel URL
        if (!validatePleskURL(request('database_config_plesk_url'))) {
            abort(409, 'Invalid Plesk panel URL. Make sure you are using the port (:2083) Example - https://yourdomain.com:8443');
        }

        //check if url has valid ssl certificate
        try {
            $response = Http::get(request('database_config_plesk_url'));

            //various (possible) html strings to look for in the page
            $str_1 = 'plesk-build';
            $str_2 = 'plesk-root';
            $str_3 = 'plesk-ui-library';

            //get the plesk login page
            $html = $response->body();

            //check variuos strings. If any is found, then we are likely on the login page
            if (is_numeric(strpos($html, $str_1)) || is_numeric(strpos($html, $str_2)) || is_numeric(strpos($html, $str_3))) {
                //all ok
            } else {
                abort(409, 'The Plesk URL is incorrect -OR- it does not have a valid SSL certificate');
            }
        } catch (RequestException $e) {
            abort(409, 'The Plesk URL is incorrect -OR- it does not have a valid SSL certificate');
        }

        //create Plesk API key
        $data = [
            'plesk_username' => request('database_config_plesk_username'),
            'plesk_password' => request('database_config_plesk_password'),
            'plesk_url' => request('database_config_plesk_url'),
        ];
        if (!$api_key = $this->databaserepo->createPleskAPIKey($data)) {
            abort(409, 'Error DB001 - Unable to create a database with the details provided');
        }

        //create a new database
        $data = [
            'plesk_domain' => request('database_config_plesk_domain'),
            'plesk_url' => request('database_config_plesk_url'),
            'plesk_prefix' => request('database_config_plesk_prefix'),
            'plesk_api_key' => $api_key,
            'create_database_user' => true,
        ];
        if (!$database = $this->databaserepo->createDatabasePlesk($data)) {
            abort(409, 'Error DB002 - Unable to create a database with the details provided');
        }

        //get details
        $mysql_host = request('database_config_plesk_host');
        $mysql_username = $database['database_username'];
        $mysql_password = $database['database_passwod'];
        $mysql_port = request('database_config_plesk_port');
        $mysql_prefix = request('database_config_plesk_prefix');

        //update the .env file (landlord database)
        $data = [
            'LANDLORD_DB_DATABASE' => $database['database_name'],
        ];
        if (!$envrepo->updateLandlordDatabase($data)) {
            abort(409, __('Error DB003:  Unable to save the (.env) file'));
        }

        //update the .env file (database method)
        $data = [
            'DB_METHOD' => 'plesk',
            'DB_METHOD_PREFIX' => $mysql_prefix,
            'DB_METHOD_PLESK_HOST' => $mysql_host,
            'DB_METHOD_PLESK_PORT' => $mysql_port,
            'DB_METHOD_PLESK_USERNAME' => $mysql_username,
            'DB_METHOD_PLESK_PASSWORD' => $mysql_password,
            'DB_METHOD_PLESK_API_KEY' => $api_key,
            'DB_METHOD_PLESK_DOMAIN' => request('database_config_plesk_domain'),
            'DB_METHOD_PLESK_URL' => pleskCleanURL(request('database_config_plesk_url')),
        ];
        if (!$envrepo->updateDatabaseMethodPlesk($data)) {
            abort(409, __('Error DB004:  Unable to save the (.env) file'));
        }

        //update the .env file (database method)
        $data = [
            'APP_KEY' => random_string(32),
        ];
        if (!$envrepo->updateAppKey($data)) {
            abort(409, __('Error (DB005:  Unable to save the (.env) file'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'error' => $error,
        ];

        //show the view
        return new SettingsResponse($payload);

    }

    /**
     * Update settings and display user form
     * @return blade view | ajax view
     */
    public function updateSettings(EnvSaaSRepository $envrepo) {

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_company_name' => 'required',
            'settings_system_timezone' => 'required',
            'settings_base_domain' => 'required',
            'settings_frontend_domain' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('Fill in all required fields'));
        }

        //import landlord sql file
        $sql_file = BASE_DIR . '/growsaas-landlord.sql';

        //validate file
        if (!is_file($sql_file)) {
            abort(409, __('Error(SET001): A required file (/growsaas-landlord.sql) is missing'));
        }

        //add this in the namespace at the top - ( use Exception; )
        try {
            DB::connection('landlord')->unprepared(file_get_contents($sql_file));
        } catch (Exception $e) {
            Log::error("failed to dump mysql database. error: " . $e->getMessage(), ['process' => '[install]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, 'Error (SET002: Unable to connect to the MySQL server with the details provided');
        }

        //update the .env file (database method)
        $data = [
            'LANDLORD_DOMAIN' => cleanURLDomain(request('settings_base_domain')),
            'FRONTEND_DOMAIN' => cleanURLDomain(request('settings_frontend_domain')),
        ];
        if (!$envrepo->updateSettings($data)) {
            abort(409, __('Error (SET003:  Unable to save the (.env) file'));
        }

        //update company name
        \App\Models\Landlord\Settings::on('landlord')->where('settings_id', 'default')
            ->update([
                'settings_company_name' => request('settings_company_name'),
                'settings_system_timezone' => request('settings_system_timezone'),
                'settings_base_domain' => cleanURLDomain(request('settings_base_domain')),
                'settings_frontend_domain' => cleanURLDomain(request('settings_frontend_domain')),
                'settings_email_domain' => cleanURLDomain(request('settings_base_domain')),
                'settings_purchase_code' => session('settings_purchase_code'),
            ]);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
        ];

        //show the view
        return new UserResponse($payload);
    }

    /**
     * Update admin user and display finish page
     * @return blade view | ajax view
     */
    public function updateUser(EnvSaaSRepository $envrepo) {

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('Fill in all required fields'));
        }

        //get settings
        if (!$settings = \App\Models\Landlord\Settings::On('landlord')->Where('settings_id', 'default')->first()) {
            abort(409, __('Error(User001): - Setup could not complete'));
        }

        //update default user
        if (!$user = \App\Models\User::On('landlord')->Where('id', 1)->first()) {
            abort(409, __('Error(User002): - Setup could not complete'));
        }
        $user->first_name = request('first_name');
        $user->last_name = request('last_name');
        $user->email = request('email');
        $user->password = bcrypt(request('password'));
        $user->created = now();
        $user->updated = now();
        $user->last_seen = now();
        $user->creatorid = 0;
        $user->save();

        //opt-in mailing list
        $this->mailingList();

        //delete setup sql file
        @unlink(BASE_DIR . '/growsaas-landlord.sql');

        //final .env file update
        if (!$envrepo->completeSetup([
            'settings_base_domain' => $settings->settings_base_domain,
        ])) {
            abort(409, __('Error(User003): - Setup could not complete'));
        }

        //cronjob path
        $cron_path_1 = '/usr/local/bin/php ' . BASE_DIR . '/application/artisan schedule:run >> /dev/null 2>&1';
        $cron_path_1 = str_replace('\\', '/', $cron_path_1);
        $cron_path_2 = '/usr/local/bin/php ' . BASE_DIR . '/application/artisan tenants:artisan schedule:run >> /dev/null 2>&1';
        $cron_path_2 = str_replace('\\', '/', $cron_path_2);

        //wildcard domain
        $wildcard_domain = '*.' . $settings->settings_base_domain;

        // Clear the application cache and session data
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        request()->session()->flush();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'cron_path_1' => $cron_path_1,
            'cron_path_2' => $cron_path_2,
            'wildcard_domain' => $wildcard_domain,
        ];

        //show the view
        return new FinishResponse($payload);
    }

    /**
     * some notes
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function mailingList() {

        //validate
        if (request('optin') != 'on') {
            return;
        }

        //connect to updates
        try {
            $response = Http::asForm()->post('https://updates.growcrm.io/mailinglist/add', [
                'product' => 'Grow CRM SaaS',
                'first_name' => request('first_name'),
                'last_name' => request('last_name'),
                'email' => request('email'),
                'ip' => request()->ip(),
                'url' => url()->current(),
                'domain' => request()->getHost(),
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            //nothing
        }

    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'page' => 'setup',
            'meta_title' => 'Application Setup',
        ];
        return $page;
    }

}