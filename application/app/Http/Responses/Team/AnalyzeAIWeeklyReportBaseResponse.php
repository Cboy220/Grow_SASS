<?php

namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIWeeklyReportBaseResponse implements Responsable
{
    protected $payload;

    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }

    public function toResponse($request)
    {
        $html = view('pages.team.views.modals.tabs.weekly_report_analysis_base', $this->payload)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '#analysis-content',
                'action' => 'replace',
                'value' => $html
            ]]
        ]);
    }
}