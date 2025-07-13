<?php
namespace App\Http\Responses\Clients;

use Illuminate\Contracts\Support\Responsable;

class AnalyzeAIModalResponse implements Responsable
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
        $jsondata = [];
        $jsondata['dom_html'][] = [
            'selector' => '#basicModal',
            'action' => 'replace',
            'value' => $modalHtml
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '#basicModal',
            'action' => 'show'
        ];
        $jsondata['postrun_functions'][] = [
            'value' => 'NXAddEditFeedback',
        ];
        return response()->json($jsondata);
    }
} 