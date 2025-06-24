<div class="card count-{{ @count($foos) }}" id="foos-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($foos) > 0)
            <table id="foos-list-table" class="table m-t-0 m-b-0 table-hover no-wrap foo-list" data-page-size="10">
                <thead>
                    <tr>
                        <!--checkboxes-->
                        <th class="list-checkbox-wrapper">
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-foos" name="listcheckbox-foos"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="foos-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-foos">
                                <label for="listcheckbox-foos"></label>
                            </span>
                        </th>
                        <!--foo-->
                        <th class="foos_col_description"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_foo_description" href="javascript:void(0)"
                                data-url="{{ urlResource('/foos?action=sort&orderby=foo_description&sortorder=asc') }}">@lang('lang.description')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--actions-->
                        <th class="foos_col_action"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="foos-td-container">
                    <!--ajax content here-->
                    @include('pages.foos.components.table.ajax')
                    <!--ajax content here-->

                    <!--bulk actions - change category-->
                    <input type="hidden" name="checkbox_actions_foos_category" id="checkbox_actions_foos_category">
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--load more button-->
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif @if (@count($foos) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>