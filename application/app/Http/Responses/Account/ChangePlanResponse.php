<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the invoice settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Account;
use Illuminate\Contracts\Support\Responsable;

class ChangePlanResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for invoices
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //page
        $html = view('account/changeplan/start', compact('package'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#packages-container',
            'action' => 'replace',
            'value' => $html,
        ];

        //ajax response
        return response()->json($jsondata);
    }
}
