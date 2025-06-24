@extends('landlord.frontend.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body landlord-settings-start" id="landlord-settings-form">

        <img src="{{ url('public/images/landlord-settings-start.svg') }}" alt="@lang('lang.frontend')" />

    </div>
</div>

<form class="form" id="settingsFormStart">

    <!--settings_frontend_domain-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.front_end_domain') <span
                class="align-middle text-info font-16" data-toggle="tooltip" title="@lang('lang.frontend_domain_info')"
                data-placement="top"><i class="ti-info-alt"></i></span></label>
        <div class="col-sm-12">
            <input type="text" class="form-control form-control-sm" id="settings_frontend_domain"
                name="settings_frontend_domain" value="{{ $settings->settings_frontend_domain ?? '' }}">
        </div>
    </div>

    <!--frontend-->
    <div class="form-group row">
        <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.frontend_status')</label>
        <div class="col-sm-12">
            <select class="select2-basic form-control form-control-sm select2-preselected" id="settings_frontend_status"
                name="settings_frontend_status" data-preselected="{{ $settings->settings_frontend_status ?? ''}}">
                <option></option>
                <option value="enabled">@lang('lang.enabled')</option>
                <option value="disabled">@lang('lang.disabled')</option>
            </select>
        </div>
    </div>

    <!--submit-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left ajax-request"
            data-url="{{ url('app-admin/frontend/start') }}" data-form-id="settingsFormStart" data-loading-target=""
            data-ajax-type="post" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>

</form>
@endsection