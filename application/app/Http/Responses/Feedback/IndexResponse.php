<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the client settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Feedback;
use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable
{

  private $payload;

  public function __construct($payload = array())
  {
    $this->payload = $payload;
  }

  /**
   * render the view for clients
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function toResponse($request)
  {

    //set all data to arrays
    foreach ($this->payload as $key => $value) {
      $$key = $value;
    }

    if (request()->ajax() && request('action') == 'search') {
      $html = view('pages/feedback/components/table/table', compact('page', 'feedback'))->render();
      $jsondata['dom_html'][] = [
        'selector' => '#feedback-table-wrapper',
        'action' => 'replace',
        'value' => $html,
      ];
      return response()->json($jsondata);
    }
    return view('pages/feedback/wrapper', compact('page', 'feedbacks'))->render();
  }
}
