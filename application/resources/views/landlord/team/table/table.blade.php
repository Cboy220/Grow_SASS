<div class="card count-{{ @count($users) }}" id="users-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($users) > 0)
            <table id="users-list-table" class="table m-t-0 m-b-0 table-hover no-wrap foo-list" data-page-size="10">
                <thead>
                    <tr>
                        <!--name-->
                        <th class="col_name"><a href="javascript:void(0)">@lang('lang.name')</a></th>
                        <!--email-->
                        <th class="col_email"><a href="javascript:void(0)">@lang('lang.email')</a></th>
                        <!--created-->
                        <th class="col_created"><a href="javascript:void(0)">@lang('lang.date_created')</a></th>
                        <!--actions-->
                        <th class="col_action"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="users-td-container">
                    <!--ajax content here-->
                    @include('landlord.team.table.ajax')
                    <!--ajax content here-->
                    <!--bulk actions - change category-->
                    <input type="hidden" name="checkbox_actions_users_category" id="checkbox_actions_users_category">
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
            @endif @if (@count($users) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>