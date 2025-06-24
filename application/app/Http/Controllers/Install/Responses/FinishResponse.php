<?php

/** --------------------------------------------------------------------------------
 * This controller manages the business logic for the setup wizard
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Install\Responses;
use Illuminate\Contracts\Support\Responsable;

class FinishResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for install
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        $html = view('pages/install/finish', compact('page', 'cron_path_1', 'cron_path_2', 'wildcard_domain'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#setup-content',
            'action' => 'replace',
            'value' => $html);

        //already passed
        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-2',
            'action' => 'add',
            'value' => 'active-passed');

        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-3',
            'action' => 'add',
            'value' => 'active-passed');

        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-4',
            'action' => 'add',
            'value' => 'active-passed');

        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-5',
            'action' => 'add',
            'value' => 'active-passed');

        $jsondata['dom_classes'][] = array(
            'selector' => '#steps-6',
            'action' => 'add',
            'value' => 'active-passed');

        //ajax response
        return response()->json($jsondata);

    }

}
