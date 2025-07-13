<?php
namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIGeneralAlertsResponse implements Responsable
{
    private $payload;
    public function __construct($payload = array())
    {
        $this->payload = $payload;
    }
    public function toResponse($request)
    {
        foreach ($this->payload as $key => $value) { $$key = $value; }
        $html = view('pages.team.views.modals.tabs.general_alerts_analysis', compact('aiPrompt', 'aiAnalysis'))->render();
        return response()->json([
            'dom_html' => [
                [
                    'selector' => '#analysis-content',
                    'action' => 'replace',
                    'value' => $html
                ]
            ]
        ]);
    }
}
