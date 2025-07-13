<?php

namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIWeeklyReportAIResponse implements Responsable
{
    protected $payload;

    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }

    public function toResponse($request)
    {
        $html = view('pages.team.views.modals.tabs.weekly_report_analysis_ai', $this->payload)->render();
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
