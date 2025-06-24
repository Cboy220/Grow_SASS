<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the event
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Landlord\Events;
use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for event members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //was this call made from an embedded page/ajax or directly on event page
        if (request('source') == 'ext' || request('action') == 'search' || request()->ajax()) {

            //template and dom - for additional ajax loading
            $template = 'landlord/events/event';
            $dom_container = '#dynamic-content-container';
            $dom_action = 'append';

            //from client page
            if (request('action') == 'load') {
                $dom_action = 'replace';
            }

            //load more button - change the page number and determine buttons visibility
            if ($events->currentPage() < $events->lastPage()) {
                $url = loadMoreButtonUrl($events->currentPage() + 1, request('source'));
                $jsondata['dom_attributes'][] = array(
                    'selector' => '#load-more-button',
                    'attr' => 'data-url',
                    'value' => $url);
                //load more - visible
                $jsondata['dom_visibility'][] = array('selector' => '.loadmore-button-container', 'action' => 'show');
                //load more: (intial load - sanity)
                $page['visibility_show_load_more'] = true;
                $page['url'] = $url;
            } else {
                $jsondata['dom_visibility'][] = array('selector' => '.loadmore-button-container', 'action' => 'hide');
            }

            //render the view and save to json
            $html = view($template, compact('page', 'events'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => $dom_container,
                'action' => $dom_action,
                'value' => $html);

            //replace list page actions
            if (request('ref') == 'customer') {
                $jsondata['dom_visibility'][] = [
                    'selector' => '.list-page-actions-containers',
                    'action' => 'hide',
                ];
                $jsondata['dom_visibility'][] = [
                    'selector' => '#list-page-actions-container-customer',
                    'action' => 'show',
                ];
            }

            //ajax response
            return response()->json($jsondata);

        } else {
            //standard view
            $page['url'] = loadMoreButtonUrl($events->currentPage() + 1, request('source'));
            $page['loading_target'] = 'event-td-container';
            $page['visibility_show_load_more'] = ($events->currentPage() < $events->lastPage()) ? true : false;
            return view('landlord/events/wrapper', compact('page', 'events'))->render();
        }

    }

}