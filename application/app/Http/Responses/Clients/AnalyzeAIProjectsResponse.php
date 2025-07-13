<?php
namespace App\Http\Responses\Clients;
use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIProjectsResponse implements Responsable {
    private $payload;
    public function __construct($payload = array()) {
        $this->payload = $payload;
    }
    public function toResponse($request) {
        foreach ($this->payload as $key => $value) { $$key = $value; }
        $html = view('pages.clients.components.table.modals.tabs.projects_analysis', compact('client', 'projectData'))->render();
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