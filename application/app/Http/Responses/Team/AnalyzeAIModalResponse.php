<?php
namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIModalResponse implements Responsable
{
    private $payload;
    public function __construct($payload = array())
    {
        $this->payload = $payload;
    }
    public function toResponse($request)
    {
        foreach ($this->payload as $key => $value) { $$key = $value; }
        $modalHtml = view('pages.team.views.modals.team_analysis_modal', compact('team'))->render();
        $jsondata = [];
        $jsondata['dom_html'][] = [
            'selector' => '#basicModal',
            'action' => 'replace',
            'value' => $modalHtml
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#basicModal',
            'action' => 'show'
        ];
        $jsondata['postrun_functions'][] = [
            'value' => 'initTeamAIModalEvents'
        ];
        return response()->json($jsondata);
    }
} 