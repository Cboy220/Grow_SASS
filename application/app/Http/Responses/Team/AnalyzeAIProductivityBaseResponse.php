<?php

namespace App\Http\Responses\Team;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIProductivityBaseResponse implements Responsable
{
    protected $payload;

    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }

    public function toResponse($request)
    {
        $html = view('pages.team.views.modals.tabs.productivity_analysis_base', $this->payload)->render();

        return response()->json([
            'dom_html' => [[
                'selector' => '#analysis-content',
                'action' => 'replace',
                'value' => $html
            ]]
        ]);
    }
}
