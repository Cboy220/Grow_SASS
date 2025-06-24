<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for knowledgebase
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Feedback\IndexResponse;
use App\Http\Responses\Feedback\CreateResponse;
use App\Http\Responses\Feedback\FilterDataResponse;
use App\Http\Responses\Feedback\StoreResponse;
use App\Http\Responses\Feedback\DestroyResponse;
use App\Http\Responses\Feedback\EditResponse;
use App\Repositories\FeedbackRepository;
use App\Repositories\FeedbackQueryRepository;
use App\Repositories\FeedbackDetailRepository;
use App\Rules\NoTags;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Feedback extends Controller
{

    protected $feedbackRepository;
    protected $feedbackDetailRepository;
    protected $feedbackQueryRepository;

    public function __construct(
        FeedbackRepository $feedbackRepository,
        FeedbackDetailRepository $feedbackDetailRepository,
        FeedbackQueryRepository $feedbackQueryRepository

    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->feedbackRepository = $feedbackRepository;
        $this->feedbackDetailRepository = $feedbackDetailRepository;
        $this->feedbackQueryRepository = $feedbackQueryRepository;
    }

    /**
     * Display a listing of kb
     * @return IndexResponse view | ajax view
     */
    public function index()
    {
        $feedbacks = $this->feedbackRepository->getAllWithDetailsAndQueries();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'feedbacks' => $feedbacks
        ];

        //show the view
        return new IndexResponse($payload);
    }

    public function filterData(Request $request) {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 5); // default to 5
        $feedbacks = $this->feedbackRepository->getFeedbackSummariesForClient(null, $search, $perPage);
        \Carbon\Carbon::setLocale(currentLangCode());
        $feedbacks->getCollection()->transform(function ($item) {
            $item->feedback_date_human = \Carbon\Carbon::parse($item->feedback_date)->diffForHumans();
            return $item;
        });
        $payload = [
            'feedbacks' => $feedbacks->items(),
            'pagination' => [
                'current_page' => $feedbacks->currentPage(),
                'last_page' => $feedbacks->lastPage(),
                'per_page' => $feedbacks->perPage(),
                'total' => $feedbacks->total(),
            ]];
            if (count($feedbacks->items()) === 0) {
                $payload['message'] = __('lang.no_data');
            }
        return new FilterDataResponse($payload);
    }

    public function create() {

        $feedbackQueries = $this->feedbackQueryRepository->all();
        //page settings
        $page = $this->pageSettings('create');

        //reponse payload
        $payload = [
            'page' => $page,
            'feedbackQueries' => $feedbackQueries
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created client in storage.
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        //custom field validation
        // if ($request->all()) {
        //     abort(409, $messages);
        // }
        $payload = [];
        //save the client first [API]
        if ($newFeedback = $this->feedbackRepository->create([
            'client_id'=> auth()->user()->clientid,
            'feedback_date'=> now()->format('Y-m-d H:i:s'),
            'comment'=> $request->get('comment'),
            'feedback_created'=> now()->format('Y-m-d H:i:s'),
        ])) {
            $feedbackAllQueries = $this->feedbackQueryRepository->all();
            // var_dump($feedbackAllQueries);
            $feedbackDetails = [];
            foreach( $feedbackAllQueries as $feedbackQuery ) {
                // var_dump(['result:'=>$request->get('_')]);
                $this->feedbackDetailRepository->create([
                    'feedback_id'=> $newFeedback->feedback_id,
                    'feedback_query_id' => $feedbackQuery->feedback_query_id,
                    'value' => (int) $request->get($feedbackQuery->feedback_query_id),
                    'feedback_detail_created' => now()->format('Y-m-d H:i:s'),
                ]);
            }
            $payload = [
                'success' => true,
                'feedbacks' => $this->feedbackRepository->getFeedbackSummariesForClient(),
                'newFeedback'=> $newFeedback->feedback_id,
            ];
        } else {
            $payload = [
                'success'=> false,
            ];
        }
        //process reponse
        return new StoreResponse($payload);

    }

    public function update(Request $request, $id) {
        $payload = [];
        
        $feedback = $this->feedbackRepository->updateFeedbackWithDetails($id, $request->all());

        return new EditResponse($payload);

    }

    public function delete(Request $request) {
        $html = view('pages/feedback/components/modals/delete-confirm')->render();
        return \response()->json(['success' => true, 'html' => $html]);
    }

    public function destroy(Request $request, $id) {
        $result = $this->feedbackRepository->deleteFeedbackWithDetails($id);
        if( $result ) 
            return response()->json(['success'=> true,'message'=> __('lang.request_has_been_completed')]);
        else
            return response()->json(['success'=> false,'message'=> __('lang.request_is_invalid')]);
    }

    public function updateFeedback(Request $request, $id) {
        
    }

    private function validationFeedbackData($request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'email' => 'required|email',
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
    
        // Access validated data
        $validatedData = $validator->validated();

        return $validatedData;
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = [])
    {
        $page = [
            'crumbs' => [
                __('lang.feedback'),
                __('lang.feedback'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'feedback',
            'meta_title' => __('lang.feedback'),
            'heading' => __('lang.feedback'),
            'mainmenu_feedback' => 'active',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.feedback'),
            'add_modal_create_url' => url('feedback/create'),
            'add_modal_action_url' => url('feedback'),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'basicModal',
            'add_modal_action_method' => 'POST',
        ];

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
            ];
            return $page;
        }


        return $page;
    }
}