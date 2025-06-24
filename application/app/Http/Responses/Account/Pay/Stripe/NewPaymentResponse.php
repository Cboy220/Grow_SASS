<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the subscription
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Account\Pay\Stripe\NewPaymentResponse;
use Illuminate\Contracts\Support\Responsable;

class NewPaymentResponse implements Responsable {

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

        $html = view('account/pay/buttons/stripe', compact('checkout'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#payment_now_buttons_container',
            'action' => 'replace',
            'value' => $html,
        ];

        //hide placeholder button
        $jsondata['dom_visibility'][] = [
            'selector' => '#payment_now_placeholder_button',
            'action' => 'hide',
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXStripePaymentButton',
        ];

        return response()->json($jsondata);

    }

}
