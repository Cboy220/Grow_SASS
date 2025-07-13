<?php

namespace App\Http\Responses\Leads;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIScoringBaseResponse implements Responsable
{
    protected $payload;
    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }
    public function toResponse($request)
    {
        $html = view('pages.leads.components.modals.tabs.scoring_base', $this->payload)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '#analysis-content',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => []
        ]);
    }
} 