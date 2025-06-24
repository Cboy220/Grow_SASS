<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Landlord\Settings;

use App\Http\Controllers\Controller;
use App\Http\Responses\Landlord\Settings\Updateslog\LogResponse;

class Updateslog extends Controller {

    public function __construct(
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

    }

    /**
     * Show log log
     * @return blade view | ajax view
     */
    public function logShow() {

        //get all logs
        $logs = \App\Models\Landlord\Updatelog::query()
            ->leftJoin('tenants', 'tenants.tenant_id', '=', 'updateslog.updateslog_tenant_id')
            ->orderBy('updateslog_id', 'DESC')
            ->paginate(100);

        //payload
        $payload = [
            'page' => $this->pageSettings('log'),
            'logs' => $logs,
        ];

        //show the view
        return new LogResponse($payload);
    }

    /**
     * Show the log
     * @return blade view | ajax view
     */
    public function logRead($id) {

        if (!$log = \App\Models\Landlord\Updatelog::Where('updateslog_id', $id)->first()) {
            abort(404);
        }

        //page
        $html = view('landlord/settings/sections/updateslog/read', compact('log'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //fix logfooer
        $jsondata['dom_classes'][] = [
            'selector' => 'style',
            'action' => 'remove',
            'value' => 'footer',
        ];

        //remove <style> tags
        $jsondata['dom_visibility'][] = [
            'selector' => '.settings-log-view-wrapper > style',
            'action' => 'hide-remove',
        ];

        //render
        return response()->json($jsondata);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.log_settings'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'page' => 'landlord-settings',
            'mainmenu_settings' => 'active',
            'inner_group_menu_debugging' => 'active',
            'inner_menu_updating_log' => 'active',
        ];

        //show
        config(['visibility.left_inner_menu' => 'settings']);

        //return
        return $page;
    }
}