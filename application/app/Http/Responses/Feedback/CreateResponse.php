<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the KB
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Feedback;
use Illuminate\Contracts\Support\Responsable;

class CreateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view
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
        $html = view('pages/feedback/components/modals/add-edit-inc', compact('page', 'feedbackQueries'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#basicModal',
            'action' => 'replace',
            'value' => $html);

        //show modal footer
        $jsondata['dom_visibility'][] = array('selector' => '#basicModal', 'action' => 'show');

        //ajax response
        $jsondata['postrun_functions'][] = [
            'value' => 'NXAddEditFeedback',
        ];
        return response()->json($jsondata);

    }

}
