<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the email settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Landlord\Settings\Emailtemplates;
use Illuminate\Contracts\Support\Responsable;

class ShowTemplateResponse implements Responsable {

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

        //full payload array
        $payload = $this->payload;

        //render the form
        $html = view('landlord/settings/sections/emailtemplates/edit', compact('template', 'variables'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#landlord-settings-form",
            'action' => 'replace',
            'value' => $html);

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXSaaSEmailTemplates',
        ];

        //ajax response
        return response()->json($jsondata);
    }

}
