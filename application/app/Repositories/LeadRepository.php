<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Log;

class LeadRepository {

    /**
     * The leads repository instance.
     */
    protected $leads;

    /**
     * Inject dependecies
     */
    public function __construct(Lead $leads) {
        $this->leads = $leads;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object lead collection
     */
    public function search($id = '', $data = []) {

        $leads = $this->leads->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        //joins
        $leads->leftJoin('categories', 'categories.category_id', '=', 'leads.lead_categoryid');
        $leads->leftJoin('users', 'users.id', '=', 'leads.lead_creatorid');
        $leads->leftJoin('leads_status', 'leads_status.leadstatus_id', '=', 'leads.lead_status');
        $leads->leftJoin('pinned', function ($join) {
            $join->on('pinned.pinnedresource_id', '=', 'leads.lead_id')
                ->where('pinned.pinnedresource_type', '=', 'lead');
            if (auth()->check()) {
                $join->where('pinned.pinned_userid', auth()->id());
            }
        });

        //join: users reminders - do not do this for cronjobs
        if (auth()->check()) {
            $leads->leftJoin('reminders', function ($join) {
                $join->on('reminders.reminderresource_id', '=', 'leads.lead_id')
                    ->where('reminders.reminderresource_type', '=', 'lead')
                    ->where('reminders.reminder_userid', '=', auth()->id());
            });
        }

        // select all
        $leads->selectRaw('*');

        //count unread notifications
        $leads->selectRaw('(SELECT COUNT(*)
                                      FROM events_tracking
                                      LEFT JOIN events ON events.event_id = events_tracking.eventtracking_eventid
                                      WHERE eventtracking_userid = ' . auth()->id() . '
                                      AND events_tracking.eventtracking_status = "unread"
                                      AND events.event_parent_type = "lead"
                                      AND events.eventresource_id = leads.lead_id
                                      AND events.event_item = "comment")
                                      AS count_unread_comments');

        //count unread notifications
        $leads->selectRaw('(SELECT COUNT(*)
                                      FROM events_tracking
                                      LEFT JOIN events ON events.event_id = events_tracking.eventtracking_eventid
                                      WHERE eventtracking_userid = ' . auth()->id() . '
                                      AND events_tracking.eventtracking_status = "unread"
                                      AND events.event_parent_type = "lead"
                                      AND events.event_parent_id = leads.lead_id
                                      AND events.event_item = "attachment")
                                      AS count_unread_attachments');
        //converted by details
        $leads->selectRaw('(SELECT first_name
                                      FROM users
                                      WHERE users.id = leads.lead_converted_by_userid)
                                      AS converted_by_first_name');
        //converted by details
        $leads->selectRaw('(SELECT last_name
                                      FROM users
                                      WHERE users.id = leads.lead_converted_by_userid)
                                      AS converted_by_last_name');

        $leads->selectRaw('(SELECT CONCAT_WS(" ", first_name, last_name)
                                   FROM users WHERE users.id = leads.lead_converted_by_userid)
                                   AS converted_by_full_name');

        //default where
        $leads->whereRaw("1 = 1");

        //filter for active or archived (default to active) - do not use this when a lead id has been specified
        if (!is_numeric($id)) {
            if (!request()->filled('filter_show_archived_leads') && !request()->filled('filter_lead_state')) {
                $leads->where('lead_active_state', 'active');
            }
        }

        //filters: id
        if (request()->filled('filter_lead_id')) {
            $leads->where('lead_id', request('filter_lead_id'));
        }
        if (is_numeric($id)) {
            $leads->where('lead_id', $id);
        }

        //do not show items that not yet ready (i.e exclude items in the process of being cloned that have status 'invisible')
        $leads->where('lead_visibility', 'visible');

        //apply filters
        if ($data['apply_filters']) {

            //filter archived leads
            if (request()->filled('filter_lead_state') && (request('filter_lead_state') == 'active' || request('filter_lead_state') == 'archived')) {
                $leads->where('lead_active_state', request('filter_lead_state'));
            }

            //filter: added date (start)
            if (request()->filled('filter_lead_created_start')) {
                $leads->whereDate('lead_created', '>=', request('filter_lead_created_start'));
            }

            //filter: added date (end)
            if (request()->filled('filter_lead_created_end')) {
                $leads->whereDate('lead_created', '<=', request('filter_lead_created_end'));
            }

            //filter: last contacted date (start)
            if (request()->filled('filter_lead_last_contacted_start')) {
                $leads->whereDate('lead_last_contacted', '>=', request('filter_lead_last_contacted_start'));
            }

            //filter: last contacted date (end)
            if (request()->filled('filter_lead_last_contacted_end')) {
                $leads->whereDate('lead_last_contacted', '<=', request('filter_lead_last_contacted_end'));
            }

            //filter: value (min)
            if (request()->filled('filter_lead_value_min')) {
                $leads->where('lead_value', '>=', request('filter_lead_value_min'));
            }

            //filter: value (max)
            if (request()->filled('filter_lead_value_max')) {
                $leads->where('lead_value', '<=', request('filter_lead_value_max'));
            }

            //filter: board
            if (request()->filled('filter_single_lead_status')) {
                $leads->where('lead_status', request('filter_single_lead_status'));
            }

            //filter status
            if (is_numeric(request('filter_lead_status'))) {
                $leads->where('lead_status', request('filter_lead_status'));
            }
            if (is_array(request('filter_lead_status')) && !empty(array_filter(request('filter_lead_status')))) {
                $leads->whereIn('lead_status', request('filter_lead_status'));
            }

            //filter assigned
            if (is_array(request('filter_assigned')) && !empty(array_filter(request('filter_assigned')))) {
                $leads->whereHas('assigned', function ($query) {
                    $query->whereIn('leadsassigned_userid', request('filter_assigned'));
                });
            }

            //filter content created by me or assigned to me
            if (request()->filled('filter_created_by_or_assigned_to_me')) {
                $leads->where(function ($query) {
                    //where created by me
                    $query->where('lead_creatorid', auth()->id())
                    //or where assigned to me
                        ->orWhereHas('assigned', function ($subquery) {
                            $subquery->where('leadsassigned_userid', auth()->id());
                        });
                });
            }

            //filter my leads (using the actions button)
            if (request()->filled('filter_my_leads')) {
                //leads assigned to me
                $leads->whereHas('assigned', function ($query) {
                    $query->whereIn('leadsassigned_userid', [auth()->id()]);
                });
            }

            //filter: tags
            if (is_array(request('filter_tags')) && !empty(array_filter(request('filter_tags')))) {
                $leads->whereHas('tags', function ($query) {
                    $query->whereIn('tag_title', request('filter_tags'));
                });
            }

        }
        //custom fields filtering
        if (request('action') == 'search') {
            if ($fields = \App\Models\CustomField::Where('customfields_type', 'leads')->Where('customfields_show_filter_panel', 'yes')->get()) {
                foreach ($fields as $field) {
                    //field name, as posted by the filter panel (e.g. filter_ticket_custom_field_70)
                    $field_name = 'filter_' . $field->customfields_name;
                    if ($field->customfields_name != '' && request()->filled($field_name)) {
                        if (in_array($field->customfields_datatype, ['number', 'decimal', 'dropdown', 'date', 'checkbox'])) {
                            $leads->Where($field->customfields_name, request($field_name));
                        }
                        if (in_array($field->customfields_datatype, ['text', 'paragraph'])) {
                            $leads->Where($field->customfields_name, 'LIKE', '%' . request($field_name) . '%');
                        }
                    }
                }
            }
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $leads->where(function ($query) {
                $query->Where('lead_id', '=', request('search_query'));
                $query->orWhere('lead_created', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                $query->orWhere('lead_last_contacted', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                $query->orWhereRaw("YEAR(lead_created) = ?", [request('search_query')]); //example binding
                $query->orWhereRaw("YEAR(lead_last_contacted) = ?", [request('search_query')]); //example binding
                $query->orWhere('lead_title', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_firstname', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_lastname', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_email', '=', request('search_query'));
                $query->orWhere('lead_company_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_street', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_city', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_state', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_zip', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_country', '=', request('search_query'));
                $query->orWhere('lead_source', '=', request('search_query'));
                $query->orWhere('leadstatus_title', '=', request('search_query'));
                $query->orWhere('lead_phone', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_value', '=', request('search_query'));
                $query->orWhere('lead_website', 'LIKE', '%' . request('search_query') . '%');
                if (is_numeric(request('search_query'))) {
                    $query->orWhere('lead_value', '=', request('search_query'));
                }
                $query->orWhereHas('tags', function ($q) {
                    $q->where('tag_title', 'LIKE', '%' . request('search_query') . '%');
                });
            });

        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('leads', request('orderby'))) {
                $leads->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'status':
                $leads->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('leadstatus_title', request('sortorder'));
                break;
            case 'lead_category_name':
                $leads->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('category_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            if (request('query_type') == 'kanban') {
                $leads->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('lead_position', 'asc');
            } else {
                $leads->orderByRaw('CASE WHEN pinned.pinned_id IS NOT NULL THEN 1 ELSE 0 END DESC')
                    ->orderBy('lead_id', 'desc');
            }
        }

        //eager load
        $leads->with([
            'tags',
            'leadstatus',
            'attachments',
            'assigned',
            'reminders',
        ]);

        //count relationships
        $leads->withCount([
            'tags',
            'comments',
            'attachments',
            'checklists',
        ]);

        // Get the results and return them.
        return $leads->paginate(config('system.settings_system_kanban_pagination_limits'));
    }

    /**
     * Create a new record
     * @param array $position new record position
     * @return mixed object|bool
     */
    public function create($position = '') {

        //validate
        if (!is_numeric($position)) {
            Log::error("validation error - invalid params", ['process' => '[LeadRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
            return false;
        }

        //save new user
        $lead = new $this->leads;

        //data
        $lead->lead_creatorid = auth()->id();
        $lead->lead_uniqueid = str_unique();
        $lead->lead_firstname = request('lead_firstname');
        $lead->lead_lastname = request('lead_lastname');
        $lead->lead_email = request('lead_email');
        $lead->lead_phone = request('lead_phone');
        $lead->lead_job_position = request('lead_job_position');
        $lead->lead_company_name = request('lead_company_name');
        $lead->lead_website = request('lead_website');
        $lead->lead_street = request('lead_street');
        $lead->lead_city = request('lead_city');
        $lead->lead_state = request('lead_state');
        $lead->lead_zip = request('lead_zip');
        $lead->lead_country = request('lead_country');
        $lead->lead_source = request('lead_source');
        $lead->lead_title = request('lead_title');
        $lead->lead_description = request('lead_description');
        $lead->lead_value = request('lead_value');
        $lead->lead_last_contacted = request('lead_last_contacted'); //or \Carbon\Carbon::now()
        $lead->lead_status = request('lead_status');
        $lead->lead_position = $position;
        $lead->lead_categoryid = request('lead_categoryid');

        //save and return id
        if ($lead->save()) {
            //apply custom fields data
            $this->applyCustomFields($lead->lead_id);
            return $lead->lead_id;
        } else {
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$lead = $this->leads->find($id)) {
            Log::error("record could not be found", ['process' => '[LeadAssignedRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead_id' => $id ?? '']);
            return false;
        }

        //last updated
        $lead->lead_updatorid = auth()->id();

        //general
        $lead->lead_firstname = request('lead_firstname');
        $lead->lead_lastname = request('lead_lastname');
        $lead->lead_email = request('lead_email');
        $lead->lead_phone = request('lead_phone');
        $lead->lead_job_position = request('lead_job_position');
        $lead->lead_company_name = request('lead_company_name');
        $lead->lead_website = request('lead_website');
        $lead->lead_street = request('lead_street');
        $lead->lead_city = request('lead_city');
        $lead->lead_state = request('lead_state');
        $lead->lead_zip = request('lead_zip');
        $lead->lead_country = request('lead_country');
        $lead->lead_source = request('lead_source');
        $lead->lead_title = request('lead_title');
        $lead->lead_description = request('lead_description');
        $lead->lead_value = request('lead_value');
        $lead->lead_last_contacted = request('lead_last_contacted');
        $lead->lead_status = request('lead_status');

        //save
        if ($lead->save()) {
            //apply custom fields data
            $this->applyCustomFields($lead->lead_id);
            return $lead->lead_id;
        } else {
            return false;
        }
    }

    /**
     * feed for leads
     * @param string $status lead status
     * @param string $limit assigned|null limit to leads assiged to auth() user
     * @param string $searchterm
     * @return array
     */
    public function autocompleteFeed($status = '', $limit = '', $searchterm = '') {

        //validation
        if ($searchterm == '') {
            return [];
        }

        //start
        $query = $this->leads->newQuery();
        $query->selectRaw("lead_title AS value, lead_id AS id");

        //[filter] lead status
        if ($status != '') {
            if ($status == 'active') {
                $query->where('lead_status', '!=', 'completed');
            } else {
                $query->where('lead_status', '=', $status);
            }
        }

        //[filter] search term
        $query->where('lead_title', 'like', '%' . $searchterm . '%');

        //[filter] assigned
        if ($limit == 'assigned') {
            $leads->whereHas('assigned', function ($query) {
                $query->whereIn('leadsassigned_userid', auth()->user()->id);
            });
        }

        //return
        return $query->get();
    }

    /**
     * feed for leads
     * @param string $status lead status
     * @param string $limit assigned|null limit to leads assiged to auth() user
     * @param string $searchterm
     * @return array
     */
    public function autocompleteFeedNames($status = '', $limit = '', $searchterm = '') {

        //validation
        if ($searchterm == '') {
            return [];
        }

        //start
        $query = $this->leads->newQuery();
        $query->selectRaw("CONCAT(lead_firstname , ' ' , lead_lastname , ' - ' , lead_title) AS value, lead_title, lead_id AS id");

        //[filter] lead status
        if ($status != '') {
            if ($status == 'active') {
                $query->where('lead_status', '!=', 'completed');
            } else {
                $query->where('lead_status', '=', $status);
            }
        }

        //[filter] search term
        $query->where('lead_title', 'like', '%' . $searchterm . '%');

        //[filter] assigned
        if ($limit == 'assigned') {
            $leads->whereHas('assigned', function ($query) {
                $query->whereIn('leadsassigned_userid', auth()->user()->id);
            });
        }

        //return
        return $query->get();
    }

    /**
     * update model wit custom fields data (where enabled)
     */
    public function applyCustomFields($id = '') {

        //custom fields
        $fields = \App\Models\CustomField::Where('customfields_type', 'leads')->get();
        foreach ($fields as $field) {
            if ($field->customfields_standard_form_status == 'enabled') {
                $field_name = $field->customfields_name;
                \App\Models\Lead::where('lead_id', $id)
                    ->update([
                        "$field_name" => request($field_name),
                    ]);
            }
        }
    }

    /**
     * clone a leads
     * @return bool
     */
    public function cloneLead($lead = '', $data = []) {

        //we are copying
        $new_lead = $lead->replicate();
        $new_lead->lead_created = now();
        $new_lead->lead_creatorid = auth()->id();
        $new_lead->lead_uniqueid = str_unique();
        $new_lead->lead_title = $data['lead_title'];
        $new_lead->lead_firstname = $data['lead_firstname'];
        $new_lead->lead_lastname = $data['lead_lastname'];
        $new_lead->lead_status = $data['lead_status'];
        $new_lead->lead_email = $data['lead_email'];
        $new_lead->lead_value = $data['lead_value'];
        $new_lead->lead_phone = $data['lead_phone'];
        $new_lead->lead_company_name = $data['lead_company_name'];
        $new_lead->lead_website = $data['lead_website'];
        $new_lead->lead_last_contacted = null;
        $new_lead->lead_converted = 'no';
        $new_lead->lead_converted_by_userid = null;
        $new_lead->lead_converted_date = null;
        $new_lead->lead_converted_clientid = null;
        $new_lead->lead_active_state = 'active';
        $new_lead->save();

        //copy check list
        if ($data['copy_checklist']) {
            if ($checklists = \App\Models\Checklist::Where('checklistresource_type', 'lead')->Where('checklistresource_id', $lead->lead_id)->get()) {
                foreach ($checklists as $checklist) {
                    $new_checklist = $checklist->replicate();
                    $new_checklist->checklist_created = now();
                    $new_checklist->checklist_creatorid = auth()->id();
                    $new_checklist->checklist_clientid = $new_lead->lead_clientid;
                    $new_checklist->checklist_status = 'pending';
                    $new_checklist->checklistresource_type = 'lead';
                    $new_checklist->checklistresource_id = $new_lead->lead_id;
                    $new_checklist->save();
                }
            }
        }

        //copy attachements
        if ($data['copy_files']) {
            if ($attachments = \App\Models\Attachment::Where('attachmentresource_type', 'lead')->Where('attachmentresource_id', $lead->lead_id)->get()) {
                foreach ($attachments as $attachment) {
                    //unique key
                    $unique_key = Str::random(50);
                    //directory
                    $directory = Str::random(40);
                    //paths
                    $source = BASE_DIR . "/storage/files/" . $attachment->attachment_directory;
                    $destination = BASE_DIR . "/storage/files/$directory";
                    //validate
                    if (is_dir($source)) {
                        //copy the database record
                        $new_attachment = $attachment->replicate();
                        $new_attachment->attachment_creatorid = auth()->id();
                        $new_attachment->attachment_created = now();
                        $new_attachment->attachmentresource_id = $lead->lead_id;
                        $new_attachment->attachment_clientid = $new_lead->lead_clientid;
                        $new_attachment->attachment_uniqiueid = $directory;
                        $new_attachment->attachment_directory = $directory;
                        $new_attachment->attachmentresource_type = 'lead';
                        $new_attachment->attachmentresource_id = $new_lead->lead_id;
                        $new_attachment->save();
                        //copy folder
                        File::copyDirectory($source, $destination);
                    }
                }
            }
        }

        //all done
        return $new_lead;
    }

    /**
     * Get a list or leads which the user is
     * When the $result param is set to 'feed', this can be used in Feed.php
     * @param string $result null | feed | list
     * @return mixed returns collection by default or a feed obj or an array of lead id's
     */
    public function usersAssignedLeads($userid = '', $result = '') {

        //sanity
        if (!is_numeric($userid)) {
            $userid = -1;
        }

        //save userid to usein subquery
        request()->merge([
            'temp_query_userid' => $userid,
        ]);

        $leads = $this->leads->newQuery();

        $leads->whereHas('assigned', function ($q) {
            $q->whereIn('leadsassigned_userid', [request('temp_query_userid')]);
        });

        //sorting
        $leads->orderBy('lead_title', 'asc');

        $collection = $leads->get();

        //array result
        if ($result == 'list') {
            $list = [];
            foreach ($collection as $lead) {
                $list[] = $lead->lead_id;
            }
            return $list;
        }

        //return
        return $collection;

    }

    // Generate prompt for Analysis tab
    public function generateLeadAnalysisPrompt($leadId) {
        $lead = \App\Models\Lead::with(['assigned', 'comments', 'attachments', 'category', 'leadstatus'])->findOrFail($leadId);
        $conversionRate = $this->calculateLeadConversionRate();
        $avgResponseTime = $this->calculateLeadAvgResponseTime($leadId);
        $prompt = "You are a sales analyst AI. Analyze the following lead and provide data-driven insights.\n";
        $prompt .= "Lead Name: {$lead->getFullNameAttribute()}\n";
        $prompt .= "Title: {$lead->lead_title}\n";
        $prompt .= "Company: {$lead->lead_company_name}\n";
        $prompt .= "Status: {$lead->leadstatus->leadstatus_title}\n";
        $prompt .= "Category: {$lead->category->category_name}\n";
        $prompt .= "Value: {$lead->lead_value}\n";
        $prompt .= "Source: {$lead->lead_source}\n";
        $prompt .= "Assigned: " . $lead->assigned->pluck('first_name')->implode(', ') . "\n";
        $prompt .= "Conversion Rate: {$conversionRate}%\n";
        $prompt .= "Average Response Time: {$avgResponseTime} hours\n";
        $prompt .= "Description: {$lead->lead_description}\n";
        $prompt .= "Recent Comments: ";
        foreach ($lead->comments->take(3) as $comment) {
            $prompt .= "- {$comment->comment_text}\n";
        }
        $prompt .= "\nProvide insights on conversion likelihood, bottlenecks, and actionable suggestions.";
        return $prompt;
    }

    // Generate prompt for Scoring & Suggestions tab
    public function generateLeadScoringPrompt($leadId) {
        $lead = \App\Models\Lead::with(['assigned', 'comments', 'attachments', 'category', 'leadstatus'])->findOrFail($leadId);
        $prompt = "You are a sales coach AI. Score the following lead and provide recommendations to win the lead.\n";
        $prompt .= "Lead Name: {$lead->getFullNameAttribute()}\n";
        $prompt .= "Title: {$lead->lead_title}\n";
        $prompt .= "Company: {$lead->lead_company_name}\n";
        $prompt .= "Status: {$lead->leadstatus->leadstatus_title}\n";
        $prompt .= "Category: {$lead->category->category_name}\n";
        $prompt .= "Value: {$lead->lead_value}\n";
        $prompt .= "Source: {$lead->lead_source}\n";
        $prompt .= "Assigned: " . $lead->assigned->pluck('first_name')->implode(', ') . "\n";
        $prompt .= "Description: {$lead->lead_description}\n";
        $prompt .= "Recent Comments: ";
        foreach ($lead->comments->take(3) as $comment) {
            $prompt .= "- {$comment->comment_text}\n";
        }
        $prompt .= "\nScore the lead (0-100), describe the ideal profile, and list next best actions to win the lead.";
        return $prompt;
    }

    // Calculate conversion rate (dummy example)
    private function calculateLeadConversionRate() {
        $total = \App\Models\Lead::count();
        $converted = \App\Models\Lead::where('lead_converted', 'yes')->count();
        return $total > 0 ? round(($converted / $total) * 100, 1) : 0;
    }

    // Calculate average response time (dummy example)
    private function calculateLeadAvgResponseTime($leadId) {
        // Placeholder: In real app, calculate from events/comments timestamps
        return 12; // hours
    }

    // Call OpenAI for Analysis tab
    public function getLeadAIAnalysis($leadId) {
        $prompt = $this->generateLeadAnalysisPrompt($leadId);
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful sales analyst AI.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            return $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    // Call OpenAI for Scoring & Suggestions tab
    public function getLeadAIScoring($leadId) {
        $prompt = $this->generateLeadScoringPrompt($leadId);
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful sales coach AI.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            return $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}