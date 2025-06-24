<?php

/** --------------------------------------------------------------------------------
 * This repository manages the creation of a new tenant database and also populating
 * it
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories\Landlord;
use App\Repositories\Landlord\DatabaseRepository;
use DB;
use Exception;
use Log;
use Spatie\Multitenancy\Models\Tenant;

class CreateTenantRepository {

    /**
     * Inject dependecies
     */
    public function __construct(DatabaseRepository $databaserepo) {

        $this->databaserepo = $databaserepo;

    }

    /**
     * create the tenant database and populate it
     *
     * @return string database name
     */
    public function createTenant($customer = [], $package = [], $auth_key = '') {

        //create database
        if ($database_name = $this->databaserepo->createDatabase()) {
            \App\Models\Landlord\Tenant::where('tenant_id', $customer->tenant_id)
                ->update([
                    'database' => $database_name,
                ]);
        } else {
            return false;
        }

        //populate the database
        if (!$this->configureDB($customer, $package, $auth_key)) {
            return false;
        }

        //return new database information
        return true;

    }

    /**
     * this method does the followng
     *
     *   - imports teh tenant sql file into the database
     *   - update the new database
     *   - save temp login key for admin user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function configureDB($customer = [], $package = [], $auth_key = '') {

        //vars
        $tenant_id = $customer->tenant_id;

        Log::info("importing sql for customer id ($tenant_id)", ['process' => '[create-tenant-database]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);

        //import sql file
        $sql_file = BASE_DIR . '/growsaas-tenant.sql';

        //validate file
        if (!is_file($sql_file)) {
            Log::critical("tenant sql file is missing", ['process' => '[create-tenant-database]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'sql_file' => $sql_file]);
            return false;
        }

        //determine account status action
        if ($customer->tenant_status == 'awaiting-payment') {
            $redirect = 'payment';
        } else {
            $redirect = 'home';
        }

        //default customer settings
        $defaults = \App\Models\Landlord\Defaults::On('landlord')->Where('defaults_id', 1)->first();

        //reset
        \Spatie\Multitenancy\Models\Tenant::forgetCurrent();

        //get the customer from landlord db
        if ($new_customer = \Spatie\Multitenancy\Models\Tenant::On('landlord')->Where('tenant_id', $tenant_id)->first()) {
            try {
                //swicth to this tenants DB
                $new_customer->makeCurrent();

                $current = \Spatie\Multitenancy\Models\Tenant::current();

                //import the sql file into the tenants database
                DB::connection('tenant')->unprepared(file_get_contents($sql_file));

                //update general settings
                DB::connection('tenant')
                    ->table('settings')
                    ->where('settings_id', 1)
                    ->update([
                        'settings_company_name' => $customer->subdomain,
                        'settings_company_address_line_1' => null,
                        'settings_company_state' => null,
                        'settings_company_city' => null,
                        'settings_company_zipcode' => null,
                        'settings_company_country' => null,
                        'settings_company_telephone' => null,
                        'settings_email_from_address' => $customer->tenant_email,
                        'settings_email_from_name' => $customer->tenant_name,
                        'settings_email_server_type' => ($defaults->defaults_email_delivery == 'smtp_only') ? 'smtp': 'sendmail',
                        'settings_saas_tenant_id' => $customer->tenant_id,
                        'settings_saas_status' => $customer->tenant_status,
                        'settings_saas_onetimelogin_key' => $auth_key,
                        'settings_saas_onetimelogin_destination' => $redirect,
                        'settings_saas_package_id' => $package->package_id,
                        'settings_saas_package_limits_clients' => $package->package_limits_clients,
                        'settings_saas_package_limits_team' => $package->package_limits_team,
                        'settings_saas_package_limits_projects' => $package->package_limits_projects,
                        'settings_modules_projects' => ($package->package_module_projects == 'yes') ? 'enabled' : 'disabled',
                        'settings_saas_email_server_type' => 'local',
                        'settings_saas_email_forwarding_address' => $customer->tenant_email,
                        'settings_saas_email_local_address' => $customer->tenant_email_local_email,
                        'settings_modules_tasks' => ($package->package_module_tasks == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_invoices' => ($package->package_module_invoices == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_payments' => 'enabled',
                        'settings_modules_leads' => ($package->package_module_leads == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_knowledgebase' => ($package->package_module_knowledgebase == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_estimates' => ($package->package_module_estimates == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_expenses' => ($package->package_module_expense == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_notes' => 'enabled',
                        'settings_modules_subscriptions' => ($package->package_module_subscriptions == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_tickets' => ($package->package_module_tickets == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_calendar' => ($package->package_module_calendar == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_timetracking' => ($package->package_module_timetracking == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_reminders' => ($package->package_module_reminders == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_proposals' => ($package->package_module_proposals == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_contracts' => ($package->package_module_contracts == 'yes') ? 'enabled' : 'disabled',
                        'settings_modules_messages' => ($package->package_module_messages == 'yes') ? 'enabled' : 'disabled',

                        //defaults
                        'settings_system_language_default' => $defaults->defaults_language_default,
                        'settings_system_timezone' => $defaults->defaults_timezone,
                        'settings_system_date_format' => $defaults->defaults_date_format,
                        'settings_system_datepicker_format' => $defaults->defaults_datepicker_format,
                        'settings_system_currency_code' => $defaults->defaults_currency_code,
                        'settings_system_currency_symbol' => $defaults->defaults_currency_symbol,
                        'settings_system_currency_position' => $defaults->defaults_currency_position,
                        'settings_system_decimal_separator' => $defaults->defaults_decimal_separator,
                        'settings_system_thousand_separator' => $defaults->defaults_thousand_separator,
                    ]);

                //add onboading content
                \App\Models\Settings2::On('tenant')->where('settings2_id', 1)
                    ->update([
                        'settings2_onboarding_status' => config('system.settings_onboarding_status'),
                        'settings2_onboarding_content' => config('system.settings_onboarding_content'),
                        'settings2_onboarding_view_status' => 'unseen',
                    ]);

                /** -------------------------------------------------------------------------
                 * Create the users [space]
                 * -------------------------------------------------------------------------*/
                $count = 1;
                //create a space for this user
                $space = new \App\Models\Project();
                $space->setConnection('tenant');
                $space->project_uniqueid = str_unique();
                $space->project_id = -(time() + $count);
                $space->project_type = 'space';
                $space->project_creatorid = 0;
                $space->project_title = 'My Space';
                $space->project_reference = 'default-user-space';
                $space->save();

                //assign the user to the new space
                $assigned = new \App\Models\ProjectAssigned();
                $assigned->setConnection('tenant');
                $assigned->projectsassigned_projectid = $space->project_id;
                $assigned->projectsassigned_userid = 1;
                $assigned->save();

                //create a default folder for the [files] feature, in the space
                $folder = new \App\Models\FileFolder();
                $folder->setConnection('tenant');
                $folder->filefolder_creatorid = 0;
                $folder->filefolder_projectid = $space->project_id;
                $folder->filefolder_name = 'Default';
                $folder->filefolder_default = 'yes';
                $folder->filefolder_system = 'no';
                $folder->save();

                //update profile with space id
                DB::connection('tenant')
                    ->table('users')
                    ->where('id', 1)
                    ->update([
                        'space_uniqueid' => $space->project_uniqueid,
                        'unique_id' => str_unique(),
                    ]);

                /** -------------------------------------------------------------------------
                 * Create the team [space]
                 * -------------------------------------------------------------------------*/
                $count++;
                //create the team space
                $space = new \App\Models\Project();
                $space->setConnection('tenant');
                $space->project_uniqueid = str_unique();
                $space->project_id = -(time() - $count);
                $space->project_type = 'space';
                $space->project_creatorid = 0;
                $space->project_title = 'Team Space';
                $space->project_reference = 'default-team-space';
                $space->save();

                //create a default folder for the [files] feature, in the space
                $folder = new \App\Models\FileFolder();
                $folder->setConnection('tenant');
                $folder->filefolder_creatorid = 0;
                $folder->filefolder_projectid = $space->project_id;
                $folder->filefolder_name = 'Default';
                $folder->filefolder_default = 'yes';
                $folder->filefolder_system = 'no';
                $folder->save();

                //save the unique id of the space, in the settings table
                \App\Models\Settings2::On('tenant')->where('settings2_id', 1)
                    ->update([
                        'settings2_spaces_team_space_id' => $space->project_uniqueid,
                    ]);

                //assign user to the space
                $assigned = new \App\Models\ProjectAssigned();
                $assigned->setConnection('tenant');
                $assigned->projectsassigned_projectid = $space->project_id;
                $assigned->projectsassigned_userid = 1;
                $assigned->save();

                //update user profile
                $tmp = explode(" ", $customer->tenant_name);
                $firstname = $tmp[0];
                $lastname = trim(str_replace($firstname, '', $customer->tenant_name));
                DB::connection('tenant')
                    ->table('users')
                    ->where('id', 1)
                    ->update([
                        'first_name' => $firstname,
                        'last_name' => $lastname,
                        'email' => $customer->tenant_email,
                        'password' => $customer->tenant_password,
                        'welcome_email_sent' => 'yes',
                        'created' => now(),
                        'last_seen' => now(),
                        'pref_language' => $defaults->defaults_language_default,
                    ]);

            } catch (Exception $e) {
                Log::critical("error importing sql file into database (" . $e->getMessage() . ")", ['process' => '[create-tenant-database]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'tenant_id' => $tenant_id]);
                return false;
            }
        }

        return true;

    }

}