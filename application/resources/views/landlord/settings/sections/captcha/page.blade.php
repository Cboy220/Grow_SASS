@extends('landlord.settings.wrapper')
@section('settings_content')
<!--settings-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">
        <form class="form">

            <!--form text tem-->
            <div class="form-group row">
                <label class="col-3 control-label col-form-label">@lang('lang.site_key')</label>
                <div class="col-9">
                    <input type="text" class="form-control form-control-sm" id="settings_captcha_api_site_key"
                        name="settings_captcha_api_site_key"
                        value="{{ $settings->settings_captcha_api_site_key ?? '' }}">
                </div>
            </div>

            <!--form text tem-->
            <div class="form-group row">
                <label class="col-3 control-label col-form-label">@lang('lang.secret_key')</label>
                <div class="col-9">
                    <input type="text" class="form-control form-control-sm" id="settings_captcha_api_secret_key"
                        name="settings_captcha_api_secret_key"
                        value="{{ $settings->settings_captcha_api_secret_key ?? '' }}">
                </div>
            </div>


            <!--Enabled-->
            <div class="form-group form-group-checkbox row">
                <label class="col-3 col-form-label">@lang('lang.enable_feature') <span
                        class="align-middle text-info font-16" data-toggle="tooltip"
                        title="@lang('lang.recaptcha_signup_info')" data-placement="top"><i
                            class="ti-info-alt"></i></span></label>
                <div class="col-9 p-t-5">
                    <input type="checkbox" id="settings_captcha_status" name="settings_captcha_status"
                        class="filled-in chk-col-light-blue"
                        {{ runtimePrechecked($settings->settings_captcha_status) }}>
                    <label for="settings_captcha_status"></label>
                </div>
            </div>

            <div class="alert alert-info">
                @lang('lang.recaptcha_info') - <a href="https://www.google.com/recaptcha/admin/" target="_blank">Google
                    reCAPTCHA</a>
            </div>

            <!--buttons-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
                    data-url="{{ url('app-admin/settings/captcha') }}" data-loading-target="" data-ajax-type="POST"
                    data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection