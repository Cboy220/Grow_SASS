<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the projects
 * controller
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Landlord\Payments;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

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

        //prepend content on top of list or show full table
        if ($count == 1) {
            $html = view('landlord/payments/table/table', compact('payments'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#payments-table-wrapper',
                'action' => 'replace',
                'value' => $html);
        } else {
            //prepend content on top of list
            $html = view('landlord/payments/table/ajax', compact('payments'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#payments-td-container',
                'action' => 'prepend',
                'value' => $html);
        }

        //update customer account status (customer page)
        if (request('subscription_renewal_options') == 'on') {

            switch (request('subscription_status')) {
            case 'active':
            case 'free-trial':
                $text = __('lang.active');
                $colour = 'label-outline-success';
                break;
            case 'awaiting-payment':
                $text = __('lang.awaiting_payment');
                $colour = 'label-outline-warning';
                break;
            case 'cancelled':
            case 'failed':
                $text = __('lang.cancelled');
                $colour = 'label-outline-danger';
                break;
            }

            //reset
            $jsondata['dom_classes'][] = [
                'selector' => '#customer_account_status',
                'action' => 'remove',
                'value' => 'label-outline-warning',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#customer_account_status',
                'action' => 'remove',
                'value' => 'label-outline-success',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#customer_account_status',
                'action' => 'remove',
                'value' => 'label-outline-danger',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#customer_account_status',
                'action' => 'remove',
                'value' => 'label-outline-default',
            ];

            //add
            $jsondata['dom_classes'][] = [
                'selector' => '#customer_account_status',
                'action' => 'add',
                'value' => $colour,
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#customer_account_status',
                'action' => 'replace',
                'value' => $text,
            ];

        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}