<?php
namespace App\Http\Responses\Clients;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIFeedbackModalResponse implements Responsable
{
    private $payload;
    public function __construct($payload = array())
    {
        $this->payload = $payload;
    }
    public function toResponse($request)
    {
        foreach ($this->payload as $key => $value) { $$key = $value; }
        $modalHtml = view('pages.clients.views.list.modals.analyze_ai', compact('client'))->render();
        return response()->json([
            'dom_html' => [
                [
                    'selector' => '#basicModal',
                    'action' => 'replace-with',
                    'value' => $modalHtml
                ]
            ],
            'modal' => [
                'selector' => '#basicModal',
                'action' => 'show'
            ]
        ]);
    }
} 