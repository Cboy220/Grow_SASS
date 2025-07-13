<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [analyzeAITasks] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class AnalyzeAITasksResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for tasks analysis
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //render the tasks analysis view
        $html = view('pages.projects.views.list.modals.tabs.tasks_analysis', compact(
            'project', 'tasks', 'overdueTasks', 'upcomingDeadlines', 'criticalTasks', 'aiPrompt'
        ))->render();
        
        $jsondata['dom_html'][] = array(
            'selector' => '#analysis-content',
            'action' => 'replace',
            'value' => $html);

        //ajax response
        return response()->json($jsondata);

    }

} 