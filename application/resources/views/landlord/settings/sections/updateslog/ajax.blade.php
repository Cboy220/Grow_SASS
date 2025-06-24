@foreach($logs as $log)
<!--each row-->
<tr class="settings-each-log" id="log_{{ $log->updateslog_id }}">
    <td class="updatess_col_date">
        {{ runtimeDate($log->updateslog_created) }}
    </td>
    <td class="updatess_col_customer_domain">
        <a href="https://{{ $log->domain ?? '#' }}" target="_blank">{{ $log->domain ?? '---' }}</a>
    </td>
    <td class="updatess_col_customer_database">
        {{ $log->updateslog_tenant_database ?? '---' }}
    </td>
    <td class="updatess_col_current_version">
        {{ $log->updateslog_current_version }}
    </td>
    <td class="updatess_col_target_version">
        {{ $log->updateslog_target_version }}
    </td>
    <td class="updatess_col_update_status">
        @if($log->updateslog_status == 'completed')
        <span class="label label-outline-success">@lang('lang.completed')</span>
        @else
        <span class="label label-outline-danger">@lang('lang.failed')</span>
        @endif
    </td>
    <td class="logs_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--view log-->
            <button type="button"
                class="btn btn-outline-success btn-circle btn-sm data-toggle-action-tooltip edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                title="{{ cleanLang(__('lang.view')) }}"
                data-toggle="modal" data-target="#commonModal"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.details')"
                data-action-type="" data-action-form-id=""
                data-footer-visibility="hidden"
                data-url="{{ url('app-admin/settings/updateslog/'.$log->updateslog_id) }}">
                <i class="ti-book"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->