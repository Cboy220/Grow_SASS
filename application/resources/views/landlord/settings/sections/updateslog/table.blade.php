<div class="table-responsive">
    @if (@count($logs ?? []) > 0)
    <table id="demo-updates-addrow" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
        <thead>
            <tr>
                <th class="updatess_col_date">@lang('lang.date')</th>
                <th class="updatess_col_customer_domain">@lang('lang.customer')</th>
                <th class="updatess_col_customer_database">@lang('lang.customer_database')</th>
                <th class="updatess_col_current_version">@lang('lang.current_version')</th>
                <th class="updatess_col_target_version">@lang('lang.target_version')</th>
                <th class="updatess_col_update_status">@lang('lang.update_status')</th>
                <th class="updatess_col_action">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody id="logs-td-container">
            <!--ajax content here-->
            @include('landlord.settings.sections.updateslog.ajax')
            <!--ajax content here-->
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
    @endif
    @if (@count($logs ?? []) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
</div>