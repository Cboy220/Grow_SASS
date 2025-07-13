<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [analyzeAIBilling] process for the projects
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Projects;
use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIBillingResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for billing analysis
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //render the billing analysis view
        $html = view('pages.projects.views.list.modals.tabs.billing_analysis', compact(
            'project', 'invoices', 'estimates', 'contracts', 'timers', 'unbilledHours', 'pendingEstimates', 'pendingContracts', 'aiPrompt'
        ))->render();
        
        $jsondata['dom_html'][] = array(
            'selector' => '#analysis-content',
            'action' => 'replace',
            'value' => $html);

        //ajax response
        return response()->json($jsondata);

    }

} 