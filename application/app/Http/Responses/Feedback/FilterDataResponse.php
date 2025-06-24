<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the KB
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Feedback;
use Illuminate\Contracts\Support\Responsable;

class FilterDataResponse implements Responsable {

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
        // foreach ($this->payload as $key => $value) {
        //     $$key = $value;
        // }

        if (request()->ajax()) {
          return response()->json($this->payload);
      } else {
        return response()->json(['success' => false, 'message'=> __('')]);
      }
    }

}
