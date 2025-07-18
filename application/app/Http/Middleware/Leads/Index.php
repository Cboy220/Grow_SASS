<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;

use App\Models\Lead;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] leads
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.leads')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //table config
        $this->tableConfig();

        //various frontend and visibility settings
        $this->fronteEnd();

        //set for dynamically loading the lead modal
        $this->dynamicLoad();

        //toggle layout
        $this->toggleKanbanView();

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_leads >= 1) {
                //[limit] - for users with only local level scope
                if (auth()->user()->role->role_leads_scope == 'own') {
                    request()->merge(['filter_created_by_or_assigned_to_me' => array(auth()->id())]);
                }
                //toggle 'my leads' button opntions
                $this->toggleOwnFilter();
                return $next($request);
            }
        }

        //client user
        if (auth()->user()->is_client) {
            abort(403);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][leads][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * Set the users tables column visibility preferences
     * 
     * @tablename - leads
     * 
     * @IMPORTANT - update the default colums here, whenver new features/columns are added to the resource
     *
     * @associations - current table to column name associations
     *   tableconfig_column_1 [lead_firstname lead_lastname] [no-delete]
     *   tableconfig_column_2 [lead_title] 
     *   tableconfig_column_3 [lead_created]
     *   tableconfig_column_4 [lead_value]
     *   tableconfig_column_5 [lead_status]
     *   tableconfig_column_6 [lead_assigned]
     *   tableconfig_column_7 [lead_category_name]
     *   tableconfig_column_8 [lead_company_name]
     *   tableconfig_column_9 [lead_email]
     *   tableconfig_column_10 [lead_phone]
     *   tableconfig_column_11 [lead_job_position]
     *   tableconfig_column_12 [lead_city]
     *   tableconfig_column_13 [lead_state]
     *   tableconfig_column_14 [lead_zip]
     *   tableconfig_column_15 [lead_country]
     *   tableconfig_column_16 [lead_last_contacted]
     *   tableconfig_column_17 [lead_converted_by_userid]
     *   tableconfig_column_18 [lead_converted_date]
     *   tableconfig_column_19 [lead_source]
     *
     */
    private function tableConfig() {

        //get current settings or create for user
        if (!$table = \App\Models\TableConfig::Where('tableconfig_userid', auth()->id())->Where('tableconfig_table_name', 'leads')->first()) {

            //create for this user and set the visible columns (by setting them to `null`)
            $table = new \App\Models\TableConfig();
            $table->tableconfig_userid = auth()->id();
            $table->tableconfig_table_name = 'leads';
            $table->tableconfig_column_1 = 'displayed';
            $table->tableconfig_column_2 = 'displayed';
            $table->tableconfig_column_3 = 'displayed';
            $table->tableconfig_column_4 = 'displayed';
            $table->tableconfig_column_5 = 'displayed';
            $table->tableconfig_column_6 = 'displayed';
            $table->tableconfig_column_7 = 'displayed';
            $table->save();
        }

        //get row
        $table = \App\Models\TableConfig::Where('tableconfig_userid', auth()->id())->Where('tableconfig_table_name', 'leads')->first();

        //default show some table columns
        config(['table' => $table]);

    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default show some table columns
        config([
            'visibility.leads_col_client' => true,
            'visibility.leads_col_category' => true,
            'visibility.leads_col_checkboxes' => true,
            'visibility.leads_kanban_actions' => true,
        ]);

        request()->merge([
            'resource_query' => 'ref=list',
        ]);

        //show toggle archived leads button
        if (auth()->user()->is_team) {
            config([
                'visibility.archived_leads_toggle_button' => true,
            ]);
        }

        //permissions -viewing
        if (auth()->user()->role->role_leads >= 1) {
            if (auth()->user()->is_team) {
                config([
                    //visibility
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                ]);
            }
            if (auth()->user()->is_client) {
                config([
                    //visibility
                    'visibility.list_page_actions_search' => true,
                    'visibility.leads_col_client' => false,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_leads >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.leads_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_leads >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
                'visibility.leads_checkboxes' => true,
            ]);
        }

        //columns visibility
        if (request('leadresource_type') == 'client') {
            config([
                //visibility
                'visibility.leads_col_client' => false,
                'visibility.filter_panel_client_lead' => false,
                'visibility.leads_col_category' => false,
            ]);
        }

        //visibility of 'filter assigned" in filter panel
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_leads_scope == 'global') {
                config([
                    //visibility
                    'visibility.filter_panel_assigned' => true,
                ]);
            }
        }

        //importing and exporting
        config([
            'visibility.list_page_actions_exporting' => (auth()->user()->role->role_content_export == 'yes') ? true : false,
            'visibility.list_page_actions_importing' => (auth()->user()->role->role_content_import == 'yes') ? true : false,
        ]);

    }

    function toggleOwnFilter() {

        //visibility of 'my leads" button - only users with globa scope need this button
        if (auth()->user()->role->role_leads_scope == 'global') {
            config([
                //visibility
                'visibility.own_leads_toggle_button' => true,
            ]);
        }

        //update show archived leads
        if (request('toggle') == 'pref_filter_show_archived_leads') {
            //toggle database settings
            auth()->user()->pref_filter_show_archived_leads = (auth()->user()->pref_filter_show_archived_leads == 'yes') ? 'no' : 'yes';
            auth()->user()->save();
        }

        //update 'own leads filter'
        if (request('toggle') == 'pref_filter_own_leads') {
            //toggle database settings
            auth()->user()->pref_filter_own_leads = (auth()->user()->pref_filter_own_leads == 'yes') ? 'no' : 'yes';
            auth()->user()->save();
        }

        //a filter panel search has been done with assigned  - so reset 'my leads' to 'no'
        if (request()->filled('filter_assigned')) {
            if (auth()->user()->pref_filter_own_leads == 'yes') {
                auth()->user()->pref_filter_own_leads = 'no';
                auth()->user()->save();
            }
        }

        //set
        if (auth()->user()->pref_filter_own_leads == 'yes') {
            request()->merge(['filter_my_leads' => auth()->id()]);
        }

        //set
        if (auth()->user()->pref_filter_show_archived_leads == 'yes') {
            request()->merge(['filter_show_archived_leads' => 'yes']);
        }

    }

    function toggleKanbanView() {
        //update 'own leads filter'
        if (request('toggle') == 'layout') {
            //toggle database settings
            auth()->user()->pref_view_leads_layout = (auth()->user()->pref_view_leads_layout == 'kanban') ? 'list' : 'kanban';
            auth()->user()->save();
        }

        //css setting for body
        if (auth()->user()->pref_view_leads_layout == 'kanban') {
            config([
                'settings.css_kanban' => 'kanban',
                'visibility.leads_kanban_actions' => true,
                'visibility.kanban_leads_sorting' => true,
            ]);
        }
    }

    /*
     * set the front end to load the modal dynamically
     */
    private function dynamicLoad() {
        //validate that the url is for loading a lead dynmically
        if (is_numeric(request()->route('lead')) && request()->segment(2) == 'v') {
            config([
                'visibility.dynamic_load_modal' => true,
                'settings.dynamic_trigger_dom' => '#dynamic-lead-content',
            ]);
        }
    }
}
