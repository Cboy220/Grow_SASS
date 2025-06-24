<div class="card count-{{ @count($expectations) }}" id="expectations-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($expectations) > 0)
            <table id="expectations-list-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list"
                data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.projects_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-expectations" name="listcheckbox-expectations"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="expectations-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-expectations">
                                <label for="listcheckbox-expectations"></label>
                            </span>
                        </th>
                        @endif
                        <th class="projects_col_id">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_project_id" href="javascript:void(0)"
                                data-url="{{ urlResource('/expectations?action=sort&orderby=project_id&sortorder=asc') }}">{{ cleanLang(__('lang.id')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="projects_col_project">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_project_title"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expectations?action=sort&orderby=project_title&sortorder=asc') }}">{{ cleanLang(__('lang.title')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.projects_col_client'))
                        <th class="projects_col_client">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_project_client"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expectations?action=sort&orderby=project_client&sortorder=asc') }}">{{ cleanLang(__('lang.client')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @endif
                        <th class="projects_col_start_date hidden">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_project_date_start"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expectations?action=sort&orderby=project_date_start&sortorder=asc') }}">{{ cleanLang(__('lang.start_date')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="projects_col_end_date">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_project_date_due"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expectations?action=sort&orderby=project_date_due&sortorder=asc') }}">{{ cleanLang(__('lang.due_date')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.projects_col_tags'))
                        <th class="projects_col_tags">{{ cleanLang(__('lang.tags')) }}</th>
                        @endif
                        <th class="projects_col_progress"><a class="js-ajax-ux-request js-list-sorting" 
                                id="sort_project_progress" href="javascript:void(0)"
                                data-url="{{ urlResource('/expectations?action=sort&orderby=project_progress&sortorder=asc') }}">{{ cleanLang(__('lang.progress')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        @if(config('visibility.projects_col_team'))
                        <th class="projects_col_team"><a href="javascript:void(0)">{{ cleanLang(__('lang.team')) }}</a></th>
                        @endif
                        <th class="projects_col_status">
                            <a class="js-ajax-ux-request js-list-sorting" id="sort_project_status"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/expectations?action=sort&orderby=project_status&sortorder=asc') }}">{{ cleanLang(__('lang.status')) }}<span class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a>
                        </th>
                        <th class="projects_col_action"><a href="javascript:void(0)">{{ cleanLang(__('lang.action')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="expectations-td-container">
                    <!--ajax content here-->
                    @include('pages.expectations.components.table.ajax')
                    <!--/ajax content here-->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--/load more button-->
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif @if (@count($expectations) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>