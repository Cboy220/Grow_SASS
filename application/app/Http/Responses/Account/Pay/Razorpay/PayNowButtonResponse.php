<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the 'pay now' button for a subscription
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Account\Pay\Razorpay;
use Illuminate\Contracts\Support\Responsable;

class PayNowButtonResponse implements Responsable {

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
        $html = view('account/pay/buttons/razorpay/step1', compact('payload'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#payment_now_buttons_container",
            'action' => 'replace',
            'value' => $html);

        return response()->json($jsondata);

    }

}
