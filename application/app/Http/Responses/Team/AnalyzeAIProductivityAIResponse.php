<?php

namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIProductivityAIResponse implements Responsable
{
    protected $payload;

    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }

    public function toResponse($request)
    {
        $html = view('pages.team.views.modals.tabs.productivity_analysis_ai', $this->payload)->render();

        return response()->json([
            'dom_html' => [[
                'selector' => '.ai-analysis-result',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => ['convertTeamAIMarkdown']
        ]);
    }
}
