<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the KB
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

 namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //render the form
        $html = view('pages/projects/views/list/modals/analyze_ai', [
            'project' => $project ?? null,
            'tasks' => $tasks ?? collect(),
            'team_members' => $team_members ?? collect(),
            'invoices' => $invoices ?? collect(),
            'estimates' => $estimates ?? collect(),
            'contracts' => $contracts ?? collect(),
            'unbilled_timers' => $unbilled_timers ?? collect(),
        ])->render();
        
        $jsondata['dom_html'][] = array(
            'selector' => '#basicModal',
            'action' => 'replace',
            'value' => $html);

        //show modal footer
        $jsondata['dom_visibility'][] = array('selector' => '#basicModal', 'action' => 'show');

        //ajax response
        $jsondata['postrun_functions'][] = [
            'value' => 'NXAddEditFeedback',
        ];
        return response()->json($jsondata);

    }

}
