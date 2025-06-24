@extends('landlord.settings.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">


        <!--offer_free_trial-->
        <div class="form-group row">
            <label
                class="col-sm-12 text-left control-label col-form-label">@lang('lang.offer_free_trial')</label>
            <div class="col-sm-12">
                <select class="select2-basic form-control form-control-sm select2-preselected" id="settings_free_trial"
                    name="settings_free_trial" data-preselected="{{ $settings->settings_free_trial }}">
                    <option></option>
                    <option value="yes">@lang('lang.yes')</option>
                    <option value="no">@lang('lang.no')</option>
                </select>
            </div>
        </div>


        <!--free_trial_duration-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label">{{ cleanLang(__('lang.free_trial_duration')) }}</label>
            <div class="col-12">
                <input type="number" class="form-control form-control-sm" id="settings_free_trial_days" {{ runtimeTrialDaysDisabled($settings->settings_free_trial) }}
                    name="settings_free_trial_days" value="{{ $settings->settings_free_trial_days ?? '' }}">
            </div>
        </div>

        <div class="alert alert-info">
            @lang('lang.free_trial_info')
        </div>

        <!--submit-->
        <div class="text-right">
            <button type="submit" id="commonModalSubmitButton"
                class="btn btn-rounded-x btn-danger waves-effect text-left ajax-request"
                data-url="{{ url('app-admin/settings/freetrial') }}" data-form-id="landlord-settings-form"
                data-loading-target="" data-ajax-type="post" data-type="form"
                data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
        </div>
    </div>
</div>
@endsection