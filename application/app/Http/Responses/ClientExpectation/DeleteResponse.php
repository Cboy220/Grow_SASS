<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the clients
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\ClientExpectation;
use Illuminate\Contracts\Support\Responsable;

class DeleteResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }
        //render the form
        $html = view($page['path'], compact('page', 'id'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#basicModal',
            'action' => 'replace',
            'value' => $html);

        //show modal footer
        $jsondata['dom_visibility'][] = array('selector' => '#basicModal', 'action' => 'show');

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXDeleteExpectationEventInit',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
