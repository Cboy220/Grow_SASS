<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for clients
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Client;
use App\Models\ClientExpectation;
use App\Models\Feedback;
//use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class ClientRepository {

    /**
     * The clients repository instance.
     */
    protected $clients;

    /**
     * The tag repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * Inject dependecies
     */
    public function __construct(Client $clients, TagRepository $tagrepo, UserRepository $userrepo) {
        $this->clients = $clients;
        $this->tagrepo = $tagrepo;
        $this->userrepo = $userrepo;

    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object clients collection
     */
    public function search($id = '', $data = []) {

        $clients = $this->clients->newQuery();

        $clients->leftJoin('pinned', function ($join) {
            $join->on('pinned.pinnedresource_id', '=', 'clients.client_id')
                ->where('pinned.pinnedresource_type', '=', 'client');
            if (auth()->check()) {
                $join->where('pinned.pinned_userid', auth()->id());
            }
        });

        // all client fields
        $clients->selectRaw('*');

        //count: clients projects by status
        foreach (config('settings.project_statuses') as $key => $value) {
            $clients->countProjects($key);
        }
        $clients->countProjects('all');
        $clients->countProjects('pending');

        //count: clients invoices by status
        foreach (config('settings.invoice_statuses') as $key => $value) {
            $clients->countInvoices($key);
        }
        $clients->countInvoices('all');

        //sum: clients invoices by status
        foreach (config('settings.invoice_statuses') as $key => $value) {
            $clients->sumInvoices($key);
        }
        $clients->sumInvoices('all');

        //count_pending_projects
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM projects
                                      WHERE project_clientid = clients.client_id
                                      AND project_type = 'project'
                                      AND project_status NOT IN('completed'))
                                      AS count_pending_projects");

        //count_pending_projects
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM projects
                                      WHERE project_clientid = clients.client_id
                                      AND project_type = 'project'
                                      AND project_status NOT IN('draft'))
                                      AS count_all_projects");

        //count_completed_projects
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM projects
                                      WHERE project_clientid = clients.client_id
                                      AND project_type = 'project'
                                      AND project_status ='completed')
                                      AS count_completed_projects");

        //count_pending_tasks
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM tasks
                                      WHERE task_clientid = clients.client_id
                                      AND task_status NOT IN(2))
                                      AS count_pending_tasks");

        //count_completed_tasks
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM tasks
                                      WHERE task_clientid = clients.client_id
                                      AND task_status = 2)
                                      AS count_completed_tasks");

        //count_tickets_open
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM tickets
                                      WHERE ticket_clientid = clients.client_id
                                      AND ticket_status NOT IN(2))
                                      AS count_tickets_open");

        //count_tickets_closed
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM tickets
                                      WHERE ticket_clientid = clients.client_id
                                      AND ticket_status = 2)
                                      AS count_tickets_closed");

        //sum_estimates_accepted
        $clients->selectRaw("(SELECT COALESCE(SUM(bill_final_amount), 0.00)
                                      FROM estimates
                                      WHERE bill_clientid = clients.client_id
                                      AND bill_estimate_type = 'estimate'
                                      AND bill_status = 'accepted')
                                      AS sum_estimates_accepted");

        //sum_estimates_declined
        $clients->selectRaw("(SELECT COALESCE(SUM(bill_final_amount), 0.00)
                                      FROM estimates
                                      WHERE bill_clientid = clients.client_id
                                      AND bill_estimate_type = 'estimate'
                                      AND bill_status = 'declined')
                                      AS sum_estimates_declined");

        //sum_invoices_all
        $clients->selectRaw("(SELECT COALESCE(SUM(bill_final_amount), 0.00)
                                      FROM invoices
                                      WHERE bill_clientid = clients.client_id
                                      AND bill_invoice_type = 'onetime'
                                      AND bill_status NOT IN('draft'))
                                      AS sum_invoices_all");

        $clients->selectRaw("(SELECT COALESCE(SUM(bill_final_amount), 0.00)
                                      FROM invoices
                                      WHERE bill_clientid = clients.client_id
                                      AND bill_invoice_type = 'onetime'
                                      AND bill_status NOT IN('draft'))
                                      AS sum_invoices_all_x");

        //sum_all_payments
        $clients->selectRaw("(SELECT COALESCE(SUM(payment_amount), 0.00)
                                      FROM payments
                                      WHERE payment_clientid = clients.client_id
                                      AND payment_type = 'invoice')
                                      AS sum_all_payments");

        //sum_outstanding_balance
        $clients->selectRaw('(SELECT COALESCE(sum_invoices_all_x - sum_all_payments, 0.00))
                                      AS sum_outstanding_balance');

        //sum_subscriptions_active
        $clients->selectRaw("(SELECT COALESCE(SUM(subscription_final_amount), 0.00)
                                      FROM subscriptions
                                      WHERE subscription_clientid = clients.client_id
                                      AND subscription_status = 'active')
                                      AS sum_subscriptions_active");

        //count_proposals_accepted
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_client_id = clients.client_id
                                      AND doc_status = 'accepted')
                                      AS count_proposals_accepted");

        //count_proposals_declined
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_client_id = clients.client_id
                                      AND doc_status = 'declined')
                                      AS count_proposals_declined");

        //sum_contracts
        $clients->selectRaw("(SELECT COALESCE(SUM(doc_value), 0.00)
                                      FROM contracts
                                      WHERE doc_client_id = clients.client_id
                                      AND doc_provider_signed_status = 'signed'
                                      AND doc_signed_status = 'signed')
                                      AS sum_contracts");

        //sum_hours_worked
        $clients->selectRaw("(SELECT COALESCE(SUM(timer_time), 0)
                                      FROM timers
                                      WHERE timer_clientid = clients.client_id
                                      AND timer_status = 'stopped')
                                      AS sum_hours_worked");

        //count_users
        $clients->selectRaw("(SELECT COUNT(*)
                                      FROM users
                                      WHERE clientid = clients.client_id
                                      AND type = 'client')
                                      AS count_users");

        //join: primary contact
        $clients->leftJoin('users', function ($join) {
            $join->on('users.clientid', '=', 'clients.client_id');
            $join->on('users.account_owner', '=', DB::raw("'yes'"));
        });

        //join: client category
        $clients->leftJoin('categories', 'categories.category_id', '=', 'clients.client_categoryid');

        //join: users reminders - do not do this for cronjobs
        if (auth()->check()) {
            $clients->leftJoin('reminders', function ($join) {
                $join->on('reminders.reminderresource_id', '=', 'clients.client_id')
                    ->where('reminders.reminderresource_type', '=', 'client')
                    ->where('reminders.reminder_userid', '=', auth()->id());
            });
        }

        //default where
        $clients->whereRaw("1 = 1");

        //ignore system client
        $clients->where('client_id', '>', 0);

        //filters: id
        if (request()->filled('filter_client_id')) {
            $clients->where('client_id', request('filter_client_id'));
        }
        if (is_numeric($id)) {
            $clients->where('client_id', $id);
        }

        //filter: status
        if (request()->filled('filter_client_status')) {
            $clients->where('client_status', request('filter_client_status'));
        }

        //filter: created date (start)
        if (request()->filled('filter_date_created_start')) {
            $clients->whereDate('client_created', '>=', request('filter_date_created_start'));
        }

        //filter: created date (end)
        if (request()->filled('filter_date_created_end')) {
            $clients->whereDate('client_created', '<=', request('filter_date_created_end'));
        }

        //filter: contacts
        if (is_array(request('filter_client_contacts')) && !empty(array_filter(request('filter_client_contacts'))) && !empty(array_filter(request('filter_client_contacts')))) {
            $clients->whereHas('users', function ($query) {
                $query->whereIn('id', request('filter_client_contacts'));
            });
        }

        //filter: catagories
        if (is_array(request('filter_client_categoryid')) && !empty(array_filter(request('filter_client_categoryid'))) && !empty(array_filter(request('filter_client_categoryid')))) {
            $clients->whereHas('category', function ($query) {
                $query->whereIn('category_id', request('filter_client_categoryid'));
            });
        }

        //filter: tags
        if (is_array(request('filter_tags')) && !empty(array_filter(request('filter_tags'))) && !empty(array_filter(request('filter_tags')))) {
            $clients->whereHas('tags', function ($query) {
                $query->whereIn('tag_title', request('filter_tags'));
            });
        }

        //custom fields filtering
        if (request('action') == 'search') {
            if ($fields = \App\Models\CustomField::Where('customfields_type', 'clients')->Where('customfields_show_filter_panel', 'yes')->get()) {
                foreach ($fields as $field) {
                    //field name, as posted by the filter panel (e.g. filter_ticket_custom_field_70)
                    $field_name = 'filter_' . $field->customfields_name;
                    if ($field->customfields_name != '' && request()->filled($field_name)) {
                        if (in_array($field->customfields_datatype, ['number', 'decimal', 'dropdown', 'date', 'checkbox'])) {
                            $clients->Where($field->customfields_name, request($field_name));
                        }
                        if (in_array($field->customfields_datatype, ['text', 'paragraph'])) {
                            $clients->Where($field->customfields_name, 'LIKE', '%' . request($field_name) . '%');
                        }
                    }
                }
            }
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query')) {
            $clients->where(function ($query) {
                $query->Where('client_id', '=', request('search_query'));
                $query->orwhere('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_phone', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_website', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_billing_street', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_billing_city', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_billing_state', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_billing_zip', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_billing_country', 'LIKE', '%' . request('search_query') . '%');
                $query->orwhere('client_custom_field_1', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('client_created', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('client_status', '=', request('search_query'));
                $query->orWhereHas('tags', function ($query) {
                    $query->where('tag_title', 'LIKE', '%' . request('search_query') . '%');
                });
                $query->orWhereHas('category', function ($query) {
                    $query->where('category_name', 'LIKE', '%' . request('search_query') . '%');
                });
            });

        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('clients', request('orderby'))) {
                $clients->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'contact':
                $clients->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('first_name', request('sortorder'));
                break;
            case 'count_projects':
                $clients->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('count_projects_all', request('sortorder'));
                break;
            case 'sum_invoices':
                $clients->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('sum_invoices_all', request('sortorder'));
                break;
            case 'category':
                $clients->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('category_name', request('sortorder'));
                break;
            }

            //all others
            $list = [
                'count_pending_projects',
                'count_completed_projects',
                'count_pending_tasks',
                'count_completed_tasks',
                'count_tickets_open',
                'count_tickets_closed',
                'sum_estimates_accepted',
                'sum_estimates_declined',
                'sum_invoices_all_x',
                'sum_all_payments',
                'sum_outstanding_balance',
                'sum_subscriptions_active',
                'count_proposals_accepted',
                'count_proposals_declined',
                'sum_contracts',
                'sum_hours_worked',
                'count_users',
            ];
            foreach ($list as $key) {
                if (request('orderby') == $key) {
                    $clients->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                        ->orderBy($key, request('sortorder'));
                }
            }
        } else {
            //default sorting
            $clients->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                ->orderBy('client_company_name', 'asc');
        }

        //eager load
        $clients->with([
            'tags',
            'users',
        ]);

        //stats - count all
        if (isset($data['stats']) && $data['stats'] == 'count_clients') {
            return $clients->count();
        }
        //stats - count all
        if (isset($data['stats']) && $data['stats'] == 'count_all_projects') {
            return $clients->get()->sum('count_all_projects');
        }
        if (isset($data['stats']) && $data['stats'] == 'sum_payments') {
            return $clients->get()->sum('sum_all_payments');
        }
        if (isset($data['stats']) && $data['stats'] == 'sum_invoices') {
            return $clients->get()->sum('sum_invoices_all');
        }

        //we are not paginating (e.g. when doing exports)
        if (isset($data['no_pagination']) && $data['no_pagination'] === true) {
            return $clients->get();
        }

        // Get the results and return them.
        return $clients->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new client record [API]
     * @return mixed object|bool  object or process outcome
     */
    public function create($data = []) {

        //save new user
        $client = new $this->clients;

        /** ----------------------------------------------
         * create the client
         * ----------------------------------------------*/
        $client->client_creatorid = Auth()->user()->id;
        $client->client_company_name = request('client_company_name');
        $client->client_description = request('client_description');
        $client->client_phone = request('client_phone');
        $client->client_website = request('client_website');
        $client->client_vat = request('client_vat');
        $client->client_billing_street = request('client_billing_street');
        $client->client_billing_city = request('client_billing_city');
        $client->client_billing_state = request('client_billing_state');
        $client->client_billing_zip = request('client_billing_zip');
        $client->client_billing_country = request('client_billing_country');
        $client->client_categoryid = (request()->filled('client_categoryid')) ? request('client_categoryid') : 2; //default

        //module settings
        $client->client_app_modules = request('client_app_modules');
        if (request('client_app_modules') == 'custom') {
            if (config('system.settings_modules_projects') == 'enabled') {
                $client->client_settings_modules_projects = (request('client_settings_modules_projects') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_invoices') == 'enabled') {
                $client->client_settings_modules_invoices = (request('client_settings_modules_invoices') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_payments') == 'enabled') {
                $client->client_settings_modules_payments = (request('client_settings_modules_payments') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_knowledgebase') == 'enabled') {
                $client->client_settings_modules_knowledgebase = (request('client_settings_modules_knowledgebase') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_estimates') == 'enabled') {
                $client->client_settings_modules_estimates = (request('client_settings_modules_estimates') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_subscriptions') == 'enabled') {
                $client->client_settings_modules_subscriptions = (request('client_settings_modules_subscriptions') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_tickets') == 'enabled') {
                $client->client_settings_modules_tickets = (request('client_settings_modules_tickets') == 'on') ? 'enabled' : 'disabled';
            }
        }

        //save
        if (!$client->save()) {
            Log::error("record could not be saved - database error", ['process' => '[ClientRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //apply custom fields data
        $this->applyCustomFields($client->client_id);

        /** ----------------------------------------------
         * add client tags
         * ----------------------------------------------*/
        $this->tagrepo->add('client', $client->client_id);

        /** ------------------------------------------------------------------------------------
         * create the default user - or check if there is a [contact] already with this email
         * -----------------------------------------------------------------------------------*/
        if ($user = \App\Models\User::Where('email', request('email'))->Where('type', 'contact')->first()) {

            //password
            $password = str_random(7);

            //update contact into client primary user
            $user->type = 'client';
            $user->role_id = 2;
            $user->account_owner = 'yes';
            $user->clientid = $client->client_id;
            $user->creatorid = Auth()->user()->id;
            $user->unique_id = str_unique();
            $user->timezone = config('system.settings_system_timezone');
            $user->password = bcrypt($password);
            $user->save();

        } else {
            request()->merge([
                'account_owner' => 'yes',
                'role_id' => 2,
                'type' => 'client',
                'clientid' => $client->client_id,
            ]);
            $password = str_random(7);
            if (!$user = $this->userrepo->create(bcrypt($password), 'user')) {
                Log::error("default client user could not be added - database error", ['process' => '[ClientRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(409);
            }
        }

        /** ----------------------------------------------
         * send welcome email
         * ----------------------------------------------*/
        if (isset($data['send_email']) && $data['send_email'] == 'yes') {
            $emaildata = [
                'password' => $password,
            ];
            $mail = new \App\Mail\UserWelcome($user, $emaildata);
            $mail->build();
        }

        //return client id
        if (isset($data['return']) && $data['return'] == 'id') {
            return $client->client_id;
        } else {
            return $client;
        }
    }

    /**
     * Create a new client
     * @return mixed object|bool client object or failed
     */
    public function signUp() {

        //save new user
        $client = new $this->clients;

        //data
        $client->client_company_name = request('client_company_name');
        $client->client_creatorid = 0;

        //save and return id
        if ($client->save()) {
            return $client;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[ClientRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id client id
     * @return mixed int|bool client id or failed
     */
    public function update($id) {

        //get the record
        if (!$client = $this->clients->find($id)) {
            Log::error("client record could not be found", ['process' => '[ClientRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'client_id' => $id ?? '']);
            return false;
        }

        //general
        $client->client_company_name = request('client_company_name');
        $client->client_phone = request('client_phone');
        $client->client_website = request('client_website');
        $client->client_vat = request('client_vat');

        //description
        if (auth()->user()->is_team) {
            $client->client_description = request('client_description');
            $client->client_categoryid = request('client_categoryid');
        }

        //billing address
        $client->client_billing_street = request('client_billing_street');
        $client->client_billing_city = request('client_billing_city');
        $client->client_billing_state = request('client_billing_state');
        $client->client_billing_zip = request('client_billing_zip');
        $client->client_billing_country = request('client_billing_country');

        //shipping address
        if (config('system.settings_clients_shipping_address') == 'enabled') {
            $client->client_shipping_street = request('client_shipping_street');
            $client->client_shipping_city = request('client_shipping_city');
            $client->client_shipping_state = request('client_shipping_state');
            $client->client_shipping_zip = request('client_shipping_zip');
            $client->client_shipping_country = request('client_shipping_country');
        }

        //module permissions
        $client->client_app_modules = request('client_app_modules');
        if (auth()->user()->is_team) {
            if (config('system.settings_modules_projects') == 'enabled') {
                $client->client_settings_modules_projects = (request('client_settings_modules_projects') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_invoices') == 'enabled') {
                $client->client_settings_modules_invoices = (request('client_settings_modules_invoices') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_payments') == 'enabled') {
                $client->client_settings_modules_payments = (request('client_settings_modules_payments') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_knowledgebase') == 'enabled') {
                $client->client_settings_modules_knowledgebase = (request('client_settings_modules_knowledgebase') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_estimates') == 'enabled') {
                $client->client_settings_modules_estimates = (request('client_settings_modules_estimates') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_subscriptions') == 'enabled') {
                $client->client_settings_modules_subscriptions = (request('client_settings_modules_subscriptions') == 'on') ? 'enabled' : 'disabled';
            }
            if (config('system.settings_modules_tickets') == 'enabled') {
                $client->client_settings_modules_tickets = (request('client_settings_modules_tickets') == 'on') ? 'enabled' : 'disabled';
            }
        }

        //status
        if (auth()->user()->is_team) {
            $client->client_status = request('client_status');
        }

        //save
        if ($client->save()) {

            //apply custom fields data
            if (auth()->user()->is_team) {
                $this->applyCustomFields($client->client_id);
            }

            return $client->client_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[ClientRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * various feeds for ajax auto complete
     * @param string $type (company_name)
     * @param string $searchterm
     * @return object client model object
     */
    public function autocompleteFeed($type = '', $searchterm = '') {

        //validation
        if ($type == '' || $searchterm == '') {
            return [];
        }

        //start
        $query = $this->clients->newQuery();

        //ignore system client
        $query->where('client_id', '>', 0);

        //feed: company names
        if ($type == 'company_name') {
            $query->selectRaw('client_company_name AS value, client_id AS id');
            $query->where('client_company_name', 'LIKE', '%' . $searchterm . '%');
        }

        //return
        return $query->get();
    }

    /**
     * update a record
     * @param int $id record id
     * @return bool process outcome
     */
    public function updateLogo($id) {

        //get the user
        if (!$client = $this->clients->find($id)) {
            return false;
        }

        //update logo
        $client->client_logo_folder = request('logo_directory');
        $client->client_logo_filename = request('logo_filename');

        //save
        if ($client->save()) {
            return true;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[ClientRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update model wit custom fields data (where enabled)
     */
    public function applyCustomFields($id = '') {

        //custom fields
        $fields = \App\Models\CustomField::Where('customfields_type', 'clients')->get();
        foreach ($fields as $field) {
            if ($field->customfields_standard_form_status == 'enabled') {
                $field_name = $field->customfields_name;
                \App\Models\Client::where('client_id', $id)
                    ->update([
                        "$field_name" => request($field_name),
                    ]);
            }
        }
    }

    /**
     * This is a method for getting client health informations from feedbacks and expectations.
     */
    public function getCustomerSuccessStats($clientId = 0)
    {
        $now = now();

        // === Expectations ===
        $expectationsQuery = ClientExpectation::query();
        if ($clientId > 0) {
            $expectationsQuery->where('client_id', $clientId);
        }

        $expectations = $expectationsQuery->get();
        $totalWeight = $expectations->sum('weight');
        $fulfilledWeight = $expectations->where('status', 'fulfilled')->sum('weight');
        $expectationPercent = $totalWeight > 0 ? round(($fulfilledWeight / $totalWeight) * 100) : 0;

        // === Feedback Score (Weighted Normalized to 10) ===
        $feedbackScoreQuery = DB::table('feedback_details as d')
            ->join('feedbacks as f', 'f.feedback_id', '=', 'd.feedback_id')
            ->join('feedback_queries as q', 'q.feedback_query_id', '=', 'd.feedback_query_id');

        if ($clientId > 0) {
            $feedbackScoreQuery->where('f.client_id', $clientId);
        }

        $averageFeedback = $feedbackScoreQuery
            ->selectRaw('ROUND(SUM(q.weight * d.value) * 10 / NULLIF(SUM(q.weight * q.range), 0), 2) as total_marks')
            ->value('total_marks') ?? 0;

        // === Health Status ===
        if ($expectationPercent >= 70 && $averageFeedback >= 7) {
            $status = 'green';
        } elseif (
            ($expectationPercent >= 40 && $expectationPercent <= 69) ||
            ($averageFeedback >= 5 && $averageFeedback <= 6)
        ) {
            $status = 'yellow';
        } else {
            $status = 'red';
        }

        // === Latest Comments ===
        $commentQuery = DB::table('feedbacks')
            ->whereNotNull('comment');

        if ($clientId > 0) {
            $commentQuery->where('client_id', $clientId);
        }

        $recentComments = $commentQuery
            ->orderByDesc('feedback_created')
            ->limit(3)
            ->pluck('comment');

        return [
            'expectation_percent' => $expectationPercent,
            'average_feedback' => $averageFeedback,
            'health_status' => $status,
            'recent_comments' => $recentComments,
        ];
    }

    /**
     * Get a client with all related data for AI analysis.
     *
     * @param int $clientId
     * @return Client|null
     */
    public function getClientWithRelations($clientId)
    {
        return Client::with([
            'creator',
            'projects.tasks',
            'projects.invoices',
            'projects.estimates',
            'projects.contracts',
            'projects.expenses',
            'projects.payments',
            'projects.milestones',
            'projects.tickets',
            'projects.files',
            'projects.tags',
            'projects.comments',
            'users.role',
            'invoices',
            'estimates',
            'notes',
            'proposals',
            'contracts',
            'expenses',
            'files',
            'payments',
            'tags',
            'tickets',
            'category',
            'feedbacks.feedbackDetails',
            'clientExpectations',
        ])->find($clientId);
    }

    /**
     * Generate a detailed prompt for OpenAI based on a Client instance.
     *
     * @param Client $client
     * @return string
     */
    public function generateOpenAIPrompt(Client $client)
    {
        $lines = [];
        $lines[] = "You are an expert business analyst AI. Here is detailed information about a client from our CRM system:";
        $lines[] = "\nClient Profile:";
        $lines[] = "- Name: {$client->name}";
        $lines[] = "- Industry: " . ($client->industry ?? 'N/A');
        $lines[] = "- Joined: {$client->client_created}";
        $lines[] = "- Category: " . ($client->category->name ?? 'N/A');
        $lines[] = "- Number of Projects: " . ($client->projects->count() ?? 0);
        $lines[] = "- Number of Users: " . ($client->users->count() ?? 0);
        $lines[] = "\nRecent Feedback:";
        $feedbacks = $client->feedbacks->take(5);
        if ($feedbacks->isEmpty()) {
            $lines[] = "- No feedback available.";
        } else {
            foreach ($feedbacks as $feedback) {
                $details = $feedback->feedbackDetails->pluck('content')->implode('; ');
                $date = $feedback->created_at ?? $feedback->feedback_created ?? '';
                $lines[] = "- \"{$details}\" ({$date})";
            }
        }
        $lines[] = "\nExpectations:";
        $expectations = $client->clientExpectations;
        if ($expectations->isEmpty()) {
            $lines[] = "- No expectations set.";
        } else {
            foreach ($expectations as $expectation) {
                $lines[] = "- \"{$expectation->title}\": Status: {$expectation->status}, Due: {$expectation->due_date}";
            }
        }
        $lines[] = "\nInvoices:";
        $lines[] = "- Total: " . ($client->invoices->count() ?? 0)
            . ", Paid: " . ($client->invoices->where('status', 'paid')->count() ?? 0)
            . ", Overdue: " . ($client->invoices->where('status', 'overdue')->count() ?? 0);
        $lines[] = "\nPlease analyze this client and provide:";
        $lines[] = "1. A summary of the client's current status and relationship with us.";
        $lines[] = "2. Key risks or opportunities based on feedback and expectations.";
        $lines[] = "3. Suggestions for improving our relationship with this client.";
        return implode("\n", $lines);
    }
}