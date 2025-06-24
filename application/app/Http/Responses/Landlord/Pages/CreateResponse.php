<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the page
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Landlord\Pages;
use Illuminate\Contracts\Support\Responsable;

class CreateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for page members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        
        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }
        
        return view('landlord/frontend/pages/editing/page', compact('page', 'content'))->render();

    }

}
