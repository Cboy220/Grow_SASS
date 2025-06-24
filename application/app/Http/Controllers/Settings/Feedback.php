<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Feedback\IndexResponse;
use App\Http\Responses\Settings\Feedback\UpdateResponse;
use App\Http\Responses\Settings\Feedback\CreateResponse;
use App\Http\Responses\Settings\Feedback\DeleteResponse;
use App\Models\FeedbackQuery;
use App\Repositories\SettingsRepository;

class Feedback extends Controller
{
    protected $settingsrepo;

    public function __construct(SettingsRepository $settingsrepo)
    {
        parent::__construct();
        $this->middleware("auth");
        $this->middleware("settingsMiddlewareIndex");
        $this->settingsrepo = $settingsrepo;

    }

    /**
     * this is an index for feedback setting page
     * @return IndexResponse
     */
    public function index()
    {
        //crumbs, page data & stats
        $page = $this->pageSettings();

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        return new IndexResponse($payload);
    }

    public function getTbody() {
        $queries = FeedbackQuery::all();

        $tbodyHtml = view("pages/settings/sections/feedback/components/table/tbody-ajax", compact("queries"))->render();

        return response()->json(['success' => true, 'tbodyHtml'=> $tbodyHtml]);
    }

    /**
     * this is an update method to edit or add the feedback queries.
     * @return UpdateResponse
     */
    public function update($id)
    {
        $payload = [];

        $feedbackQuery = FeedbackQuery::find($id);
        $feedbackQuery->title = request()->get('title');
        $feedbackQuery->content = request()->get('content');
        $feedbackQuery->type = (int) request()->get('type');
        $feedbackQuery->range = (int) request()->get('range');
        $feedbackQuery->weight = (int) request()->get('weight');
        $feedbackQuery->note = request()->get('note');

        if ($feedbackQuery->save()) {
            $payload['success'] = true;
            $payload['newId'] = $feedbackQuery->feedback_query_id;
        } else {
            $payload['success'] = false;
        }
        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * this function is to create new feedback query
     * @return UpdateResponse
     */
    public function create()
    {
        $payload = [];
       
        $feedbackQuery = new FeedbackQuery();
        $feedbackQuery->title = request()->get('title');
        $feedbackQuery->content = request()->get('content');
        $feedbackQuery->type = (int) request()->get('type');
        $feedbackQuery->range = (int) request()->get('range');
        $feedbackQuery->weight = (int) request()->get('weight');
        $feedbackQuery->note = request()->get('note');

        if ($feedbackQuery->save()) {
            $payload['success'] = true;
            $payload['newId'] = $feedbackQuery->feedback_query_id;
        } else {
            $payload['success'] = false;
        }
        //generate a response
        // var_dump($payload);
        return new CreateResponse($payload);
    }

    /**
     * this function is to create new feedback query
     * @return DeleteResponse
     */
    public function delete($id)
    {
        $payload = [];
        FeedbackQuery::destroy($id);
        return new DeleteResponse($payload);
    }

    private function pageSettings($section = '', $data = [])
    {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.feedback'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'settingsmenu_feedback' => 'active',
        ];
        return $page;
    }


    //
}
