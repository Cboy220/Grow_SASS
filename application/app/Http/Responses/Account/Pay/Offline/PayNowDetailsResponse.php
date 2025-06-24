<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the 'pay now' button for a subscription
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Account\Pay\Offline;
use Illuminate\Contracts\Support\Responsable;

class PayNowDetailsResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for subscription members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }
        $payload = $this->payload;

        //show stripe pay now button
        $html = view('account/pay/buttons/offline', compact('landlord_settings'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#online-payment-form",
            'action' => 'replace',
            'value' => $html);

        return response()->json($jsondata);

    }

}
