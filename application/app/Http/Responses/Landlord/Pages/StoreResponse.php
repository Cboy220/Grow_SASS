<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the page
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Landlord\Pages;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

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

        //change save button to an [edit] button
        $jsondata['dom_attributes'][] = [
            'selector' => '#pages-buttons-save-changes',
            'attr' => 'data-url',
            'value' => url('/app-admin/frontend/pages/' . $content->page_id),
        ];

        $jsondata['dom_attributes'][] = [
            'selector' => '#pages-buttons-save-changes',
            'attr' => 'data-ajax-type',
            'value' => 'PUT',
        ];

        //show preview link
        $jsondata['dom_visibility'][] = [
            'selector' => '#pages-buttons-save-preview',
            'action' => 'show',
        ];
        $jsondata['dom_attributes'][] = [
            'selector' => '#pages-buttons-save-preview',
            'attr' => 'href',
            'value' => 'https://' . config('system.settings_frontend_domain') . '/page/' . $content->page_permanent_link . '?preview=' . $content->page_uniqueid,
        ];

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        //response
        return response()->json($jsondata);

    }

}
