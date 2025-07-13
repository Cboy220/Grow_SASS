<?php

namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIGeneralAlertsBaseResponse implements Responsable
{
    private $payload;
    public function __construct($payload = []) { $this->payload = $payload; }
    public function toResponse($request)
    {
        $html = view('pages.team.views.modals.tabs.general_alerts_analysis_base', $this->payload)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '#analysis-content',
                'action' => 'replace',
                'value' => $html
            ]]
        ]);
    }
} 