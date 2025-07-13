<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [analyzeAITeam] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class AnalyzeAITeamResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team analysis
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //render the team analysis view
        $html = view('pages.projects.views.list.modals.tabs.team_analysis', compact(
            'project', 'teamMembers', 'overloadedMembers', 'unassignedMembers', 'aiPrompt'
        ))->render();
        
        $jsondata['dom_html'][] = array(
            'selector' => '#analysis-content',
            'action' => 'replace',
            'value' => $html);

        //ajax response
        return response()->json($jsondata);

    }

} 