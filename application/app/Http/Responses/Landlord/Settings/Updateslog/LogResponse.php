<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the email settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Landlord\Settings\Updateslog;
use Illuminate\Contracts\Support\Responsable;

class LogResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for projects
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //was this call made from an embedded page/ajax or directly on temp page
        if (request('source') == 'ext' || request('action') == 'search' || request()->ajax()) {

            //template and dom - for additional ajax loading
            switch (request('action')) {

            //typically from the loadmore button
            case 'load':
                $template = 'landlord/settings/sections/updateslog/ajax';
                $dom_container = '#logs-td-container';
                $dom_action = 'append';
                break;

            //from the sorting links
            case 'sort':
                $template = 'landlord/settings/sections/updateslog/ajax';
                $dom_container = '#logs-td-container';
                $dom_action = 'replace';
                break;

            //template and dom - for ajax initial loading
            default:
                $template = 'landlord/settings/sections/updateslog/table';
                $dom_container = '#embed-content-container';
                $dom_action = 'replace';
                break;
            }

            //load more button - change the page number and determine buttons visibility
            if ($logs->currentPage() < $logs->lastPage()) {
                $url = loadMoreButtonUrl($logs->currentPage() + 1, request('source'));
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

            //page
            $html = view($template, compact('logs', 'page'))->render();
            $jsondata['dom_html'][] = [
                'selector' => $dom_container,
                'action' => $dom_action,
                'value' => $html,
            ];

            return response()->json($jsondata);
            
        } else {
            //standard view
            $page['url'] = loadMoreButtonUrl($logs->currentPage() + 1, request('source'));
            $page['loading_target'] = 'logs-td-container';
            $page['visibility_show_load_more'] = ($logs->currentPage() < $logs->lastPage()) ? true : false;
            return view('landlord/settings/sections/updateslog/page', compact('page', 'logs'))->render();
        }

    }
}
