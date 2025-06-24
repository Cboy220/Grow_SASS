<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the fooo
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Landlord\Team;
use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for fooo members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //was this call made from an embedded page/ajax or directly on fooo page
        if (request()->ajax()) {

            $html = view('landlord/team/wrapper', compact('page', 'users'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#commonModalBody',
                'action' => 'replace',
                'value' => $html,
            ];

            //ajax response
            return response()->json($jsondata);

        } else {
            //standard view
            return view('landlord/team/wrapper', compact('page', 'users'))->render();
        }

    }

}
