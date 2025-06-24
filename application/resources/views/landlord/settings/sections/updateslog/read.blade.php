
@if($log->updateslog_status == 'failed')
<div class="alert alert-danger m-b-40"><h5 class="text-danger">@lang('lang.updates_error_database')</h5></div>
@endif

<div class="settings-email-view-wrapper p-t-30 p-b-30 p-l-10 p-r-10">

    @if($log->updateslog_notes != '')
    {!! $log->updateslog_notes !!}
    @else
    <div class="text-center"><h5>@lang('lang.no_details_available')</h5></div>
    @endif

</div>