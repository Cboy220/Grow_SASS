<?php

namespace App\Http\Responses\Leads;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIScoringAIResponse implements Responsable
{
    protected $payload;
    public function __construct($payload = [])
    {
        $this->payload = $payload;
    }
    public function toResponse($request)
    {
        $html = view('pages.leads.components.modals.tabs.scoring_ai', $this->payload)->render();
        return response()->json([
            'dom_html' => [[
                'selector' => '.ai-analysis-result',
                'action' => 'replace',
                'value' => $html
            ]],
            'postrun_functions' => ['convertLeadAIMarkdown']
        ]);
    }
} 