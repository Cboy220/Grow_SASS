<?php

namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIGeneralAlertsAIResponse implements Responsable
{
    private $payload;
    public function __construct($payload = []) { $this->payload = $payload; }
    public function toResponse($request)
    {
        $html = view('pages.team.views.modals.tabs.general_alerts_analysis_ai', $this->payload)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '.ai-analysis-result',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => [
                ['value' => 'convertTeamAIMarkdown']
            ]
        ]);
    }
} 