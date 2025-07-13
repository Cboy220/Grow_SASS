<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for team
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Common\CommonResponse;
use App\Http\Responses\Team\CreateResponse;
use App\Http\Responses\Team\EditResponse;
use App\Http\Responses\Team\IndexResponse;
use App\Http\Responses\Team\StoreResponse;
use App\Http\Responses\Team\UpdateResponse;
use App\Repositories\ProjectRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\TeamRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Validator;

class Team extends Controller {

    /**
     * The roles repository instance.
     */
    protected $roles;

    /**
     * The users repository instance.
     */
    protected $userrepo;

    /**
     * The projectrepo repository instance.
     */
    protected $projectrepo;

    /**
     * The team repository instance.
     */
    protected $teamrepo;

    public function __construct(
        RoleRepository $roles,
        UserRepository $userrepo,
        ProjectRepository $projectrepo,
        TeamRepository $teamrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('teamMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);

        $this->middleware('teamMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('teamMiddlewareEdit')->only([
            'edit',
            'update',
            'destroy',
        ]);

        //dependencies
        $this->roles = $roles;
        $this->userrepo = $userrepo;
        $this->projectrepo = $projectrepo;
        $this->teamrepo = $teamrepo;
    }

    /**
     * Display a listing of team
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //get team members
        request()->merge([
            'type' => 'team',
            'status' => 'active',
        ]);
        $users = $this->userrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('team'),
            'users' => $users,
        ];

        //show views
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new team member
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //get all team level roles
        $roles = $this->roles->allTeamRoles();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'roles' => $roles,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created team member in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,role_id',
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

        //if this is creating an admin user - check permissions
        if (!runtimeTeamCreateAdminPermissions(request('role_id'))) {
            abort(403);
        }

        //set other data
        request()->merge(['type' => 'team']);

        //save
        $password = str_random(9);
        if (!$userid = $this->userrepo->create(bcrypt($password))) {
            abort(409);
        }

        //get the user
        $users = $this->userrepo->search($userid);
        $user = $users->first();

        //update team user specific - default notification settings (defaults are set in config/settings.php)
        $user->notifications_projects_activity = 'yes_email';
        $user->notifications_billing_activity = 'yes_email';
        $user->notifications_new_assignement = 'yes_email';
        $user->notifications_leads_activity = 'yes_email';
        $user->notifications_tasks_activity = 'yes_email';
        $user->notifications_tickets_activity = 'yes_email';
        $user->notifications_system = 'yes_email';
        $user->force_password_change = config('settings.force_password_change');
        $user->pref_language = config('system.settings_system_language_default');
        $user->save();

        //create users space
        $space_uniqueid = $this->projectrepo->createUserSpace();
        $user->space_uniqueid = $space_uniqueid;
        $user->save();

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        //send to users
        $data = [
            'password' => $password,
        ];
        $mail = new \App\Mail\UserWelcome($user, $data);
        $mail->build();

        //reponse payload
        $payload = [
            'users' => $users,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified team member
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get all team level roles
        $roles = $this->roles->allTeamRoles();

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionEdit($user)) {
            abort(403);
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'roles' => $roles,
            'user' => $user,
        ];

        //process reponse
        return new EditResponse($payload);

    }

    /**
     * Update profile
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionEdit($user)) {
            abort(403);
        }

        //custom error messages
        $messages = [
            'role_id.exists' => __('lang.user_role_not_found'),
        ];

        //validate the form
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required',
                Rule::unique('users', 'email')->ignore($id, 'id'),
            ],
            'role_id' => 'nullable|exists:roles,role_id',
            'password' => 'nullable|confirmed|min:5',
        ], $messages);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update the user
        if (!$this->userrepo->update($id)) {
            abort(409);
        }

        //get user
        $users = $this->userrepo->search($id);

        //reponse payload
        $payload = [
            'users' => $users,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Update preferences of logged in user
     * @return null silent
     */
    public function updatePreferences() {

        $this->userrepo->updatePreferences(auth()->id());

    }

    /**
     * Remove the specified team member from storage.
     * @param int $id team member id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the user
        $user = $this->userrepo->get($id);

        //check permissions
        if (!runtimeTeamPermissionDelete($user)) {
            abort(403);
        }

        //delete project assignments
        \App\Models\ProjectAssigned::Where('projectsassigned_userid', $id)->delete();

        //delete task assignments
        \App\Models\TaskAssigned::Where('tasksassigned_userid', $id)->delete();

        //delete lead assignments
        \App\Models\LeadAssigned::Where('leadsassigned_userid', $id)->delete();

        //delete project manager
        \App\Models\ProjectManager::Where('projectsmanager_userid', $id)->delete();

        //delete calendar events
        \App\Models\CalendarEvent::Where('calendar_event_creatorid', $id)->delete();


        //make account as deleted
        $user->status = 'deleted';

        //remove user role
        $user->role_id = 0;

        //delete email
        $user->email = '';

        //delete password
        $user->password = '';

        //remove avater
        $user->avatar_filename = '';

        //update delete date
        $user->deleted = now();

        //save user
        $user->save();

        //reponse payload
        $payload = [
            'type' => 'remove-basic',
            'element' => "#team_$id",
        ];

        //generate a response
        return new CommonResponse($payload);
    }

    /**
     * AI Analysis - Team Weekly Report Tab
     * @return \Illuminate\Http\Response
     */
    public function analyzeAIWeeklyReport()
    {
        try {
            $teamId = request('team_id');
            if ($teamId) {
                $prompt = $this->teamrepo->generateMemberWeeklyReportPrompt($teamId);
            } else {
                $prompt = $this->userrepo->generateTeamWeeklyReportPrompt();
            }
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'You are an expert team performance analyst AI. Analyze the following weekly report and general alerts, and provide actionable insights in a clear, professional format.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];
            $aiResponse = $this->callOpenAI($messages);
            $payload = [
                'aiPrompt' => $prompt,
                'aiAnalysis' => $aiResponse,
            ];
            return new \App\Http\Responses\Team\AnalyzeAIWeeklyReportAIResponse($payload);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the Team AI Analysis modal (AJAX)
     */
    public function analyzeAIModal()
    {
        $teamId = request('team_id');
        $team = null;
        if ($teamId) {
            $team = \App\Models\User::where('type', 'team')->where('id', $teamId)->first();
        }
        $payload = [
            'team' => $team,
        ];
        return new \App\Http\Responses\Team\AnalyzeAIModalResponse($payload);
    }

    /**
     * AI Analysis - Team General Alerts Tab
     * @return \Illuminate\Http\Response
     */
    public function analyzeAIGeneralAlerts()
    {
        try {
            $teamId = request('team_id');
            if ($teamId) {
                // You can customize the prompt logic for alerts here
                $prompt = $this->teamrepo->generateMemberGeneralAlertsPrompt($teamId);
            } else {
                $prompt = $this->userrepo->generateTeamGeneralAlertsPrompt();
            }
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'You are an expert team performance analyst AI. Analyze the following general alerts and bottlenecks, and provide actionable insights in a clear, professional format.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];
            $aiResponse = $this->callOpenAI($messages);
            $payload = [
                'aiPrompt' => $prompt,
                'aiAnalysis' => $aiResponse,
            ];
            return new \App\Http\Responses\Team\AnalyzeAIGeneralAlertsAIResponse($payload);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Base Data - Team Weekly Report Tab (non-AI)
     */
    public function baseWeeklyReport()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getMemberWeeklyReportData($teamId);
        if (!$data) {
            $html = '<div class="alert alert-danger">Team member not found.</div>';
            return new \App\Http\Responses\Team\AnalyzeAIWeeklyReportBaseResponse(['html' => $html]);
        }
        return new \App\Http\Responses\Team\AnalyzeAIWeeklyReportBaseResponse($data);
    }

    /**
     * AI Analysis - Team Weekly Report Tab
     */
    public function aiWeeklyReport()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getWeeklyReportAIAnalysis($teamId);
        $html = view('pages.team.views.modals.tabs.weekly_report_analysis_ai', $data)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '.ai-analysis-result',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => ['convertTeamAIMarkdown']
        ]);
    }

    /**
     * Base Data - Team General Alerts Tab (non-AI)
     */
    public function baseGeneralAlerts()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getMemberGeneralAlertsData($teamId);
        if (!$data) {
            return new \App\Http\Responses\Team\AnalyzeAIGeneralAlertsBaseResponse([ 'html' => '<div class="alert alert-danger">Team member not found.</div>' ]);
        }
        return new \App\Http\Responses\Team\AnalyzeAIGeneralAlertsBaseResponse($data);
    }

    /**
     * AI Analysis - Team General Alerts Tab
     */
    public function aiGeneralAlerts()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getGeneralAlertsAIAnalysis($teamId);
        $html = view('pages.team.views.modals.tabs.general_alerts_analysis_ai', $data)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '.ai-analysis-result',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => ['convertTeamAIMarkdown']
        ]);
    }

    /**
     * Base Data - Team Productivity Tab (non-AI)
     */
    public function baseProductivity()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getMemberProductivityData($teamId);
        if (!$data) {
            $html = '<div class="alert alert-danger">Team member not found.</div>';
            return new \App\Http\Responses\Team\AnalyzeAIProductivityBaseResponse(['html' => $html]);
        }
        return new \App\Http\Responses\Team\AnalyzeAIProductivityBaseResponse($data);
    }

    /**
     * AI Analysis - Team Productivity Tab
     */
    public function aiProductivity()
    {
        $teamId = request('team_id');
        $data = $this->teamrepo->getProductivityAIAnalysis($teamId);
        return new \App\Http\Responses\Team\AnalyzeAIProductivityAIResponse($data);
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI($messages)
    {
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7
            ]);

            return $response['choices'][0]['message']['content'];

        } catch (\OpenAI\Exceptions\RateLimitException $e) {
            throw new \Exception('Rate limit exceeded. Please try again later.');
        } catch (\OpenAI\Exceptions\AuthenticationException $e) {
            throw new \Exception('AI service authentication failed.');
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            throw new \Exception('AI service error: ' . $e->getMessage());
        } catch (\OpenAI\Exceptions\TransporterException $e) {
            throw new \Exception('Connection error. Please check your internet connection.');
        } catch (\Exception $e) {
            throw new \Exception('AI analysis failed: ' . $e->getMessage());
        }
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.team_members'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'team',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_settings' => 'active',
            'submenu_team' => 'active',
            'sidepanel_id' => 'sidepanel-filter-team',
            'dynamic_search_url' => 'team/search?source=' . request('source') . '&action=search',
            'add_button_classes' => '',
            'load_more_button_route' => 'team',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_user'),
            'add_modal_create_url' => url('team/create'),
            'add_modal_action_url' => url('team'),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //contracts list page
        if ($section == 'team') {
            $page += [
                'meta_title' => __('lang.team_members'),
                'heading' => __('lang.team_members'),
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
                'create_type' => 'team',
            ];
            return $page;
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
            return $page;
        }

        //ext page settings
        if ($section == 'ext') {

            $page += [
                'list_page_actions_size' => 'col-lg-12',

            ];

            return $page;
        }

        //return
        return $page;
    }
}