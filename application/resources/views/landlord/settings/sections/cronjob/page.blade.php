@extends('landlord.settings.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <!--settings-->
        <form class="form">
            <!--form text tem-->
            <div class="form-group row">
                <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.cron_job_command')) }}
                    &nbsp; ( 1 )</label>
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm" id="settings_company_name"
                        name="settings_company_name" value="{{ config('cronjob_path') }}">
                </div>
            </div>

            <!--form text tem-->
            <div class="form-group row">
                <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.cron_job_command')) }}
                    &nbsp; ( 2 )</label>
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm" id="settings_company_name"
                        name="settings_company_name" value="{{ config('cronjob_path_2') }}">
                </div>
            </div>

            <!--instructions-->
            @if(config('system.settings_cronjob_has_run') == 'no' ||
            config('system.settings_cronjob_has_run_tenants') == 'no')
            <div id="fx-settings-cronjob-instructions">
                {{ cleanLang(__('lang.cronjob_instructions_saas')) }}
            </div>
            <div id="fx-settings-cronjob-instructions">
                {{ cleanLang(__('lang.cron_jobs_info')) }}
            </div>
            @endif

            <!--cronjob (1) has run-->
            @if(config('system.settings_cronjob_has_run') == 'yes')
            <div class="alert alert-info">
                <h4 class="text-info">@lang('lang.cronjob') ( 1 ) - @lang('lang.active')</h4>
                {{ cleanLang(__('lang.cronjob_last_executed')) }}:
                ({{ runtimeDateAgo(config('system.settings_cronjob_last_run')) }})
            </div>
            @endif



            <!--cronjob (1) has not run-->
            @if(config('system.settings_cronjob_has_run') == 'no')
            <div class="alert alert-danger">
                <h4 class="text-danger">@lang('lang.cronjob') ( 1 ) - @lang('lang.status')</h4>
                {{ cleanLang(__('lang.cronjob_inactive_saas')) }}
            </div>
            @endif



            <!--cronjob (2) has not run-->
            @if(config('system.settings_cronjob_has_run_tenants') == 'no')
            <div class="alert alert-danger">
                <h4 class="text-danger">@lang('lang.cronjob') ( 2 ) - @lang('lang.status')</h4>
                {{ cleanLang(__('lang.cronjob_inactive_saas')) }}
            </div>
            @endif

            <!--cronjob (2) has run-->
            @if(config('system.settings_cronjob_has_run_tenants') == 'yes')
            <div class="alert alert-info">
                <h4 class="text-info">@lang('lang.cronjob') ( 2 ) - @lang('lang.active')</h4>
                {{ cleanLang(__('lang.cronjob_last_executed')) }}:
                ({{ runtimeDateAgo(config('system.settings_cronjob_last_run_tenants')) }})
            </div>
            @endif
        </form>
    </div>
</div>
@endsection