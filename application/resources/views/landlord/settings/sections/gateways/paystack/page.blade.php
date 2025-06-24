@extends('landlord.settings.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <!--settings_paystack_secret_key-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.secret_key')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_paystack_secret_key"
                    name="settings_paystack_secret_key"
                    value="{{ $settings->settings_paystack_secret_key ?? '' }}">
            </div>
        </div>

        <!--settings_paystack_public_key-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.public_key')) }}</label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_paystack_public_key"
                    name="settings_paystack_public_key"
                    value="{{ $settings->settings_paystack_public_key ?? '' }}">
            </div>
        </div>

        <!--display name-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.display_name')) }}*
                <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                    title="{{ cleanLang(__('lang.display_name_info')) }}" data-placement="top"><i
                        class="ti-info-alt"></i></span>
            </label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_paystack_display_name"
                    name="settings_paystack_display_name" value="{{ $settings->settings_paystack_display_name ?? '' }}">
            </div>
        </div>


        <!--ipn url-->
        <div class="form-group row">
            <label class="col-12 control-label col-form-label required">{{ cleanLang(__('lang.paypal_ipn_url')) }}*
                <span class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                    title="{{ cleanLang(__('lang.paypal_api_instructions')) }}" data-placement="top"><i
                        class="ti-info-alt"></i></span>
            </label>
            <div class="col-12">
                <input type="text" class="form-control form-control-sm" id="settings_stripe_ipn_url"
                    name="settings_stripe_ipn_url" value="{{ url('/app-admin/webhooks/paystack') }}" disabled>
            </div>
        </div>

        <div class="line"></div>

        <!--Enabled-->
        <div class="form-group form-group-checkbox row">
            <label class="col-3 col-form-label">{{ cleanLang(__('lang.enable_payment_method')) }}</label>
            <div class="col-9 p-t-5">
                <input type="checkbox" id="settings_paystack_status" name="settings_paystack_status"
                    class="filled-in chk-col-light-blue" {{ runtimePrechecked($settings->settings_paystack_status) }}>
                <label for="settings_paystack_status"></label>
            </div>
        </div>


        <div class="line"></div>

        <!--advanced settings-->
        <div class="spacer row m-b-20">
            <div class="col-sm-12 col-lg-8">
                <span class="title">@lang('lang.advanced_settings')</span>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="more_information" id="more_information"
                            class="js-switch-toggle-hidden-content" data-target="toogle_more_information">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <!--advanced settings-->
        <div class="hidden p-t-10" id="toogle_more_information">
            <div class="alert alert-warning">
                <h5 class="text-danger"><i class="sl-icon-info"></i> @lang('lang.warning')</h5>
                @lang('lang.warning_make_changes_if_sure')
            </div>
            <div class="form-group form-group-checkbox row">
                <label class="col-3 col-form-label">{{ cleanLang(__('lang.reset_plan_settings')) }} <span
                        class="align-middle text-themecontrast font-16" data-toggle="tooltip"
                        title="@lang('lang.reset_plan_settings_info')" data-placement="top"><i
                            class="ti-info-alt"></i></span></label>
                <div class="col-9 p-t-5">
                    <input type="checkbox" id="settings_paystack_reset_plans" name="settings_paystack_reset_plans"
                        class="filled-in chk-col-red">
                    <label for="settings_paystack_reset_plans"></label>
                </div>
            </div>
        </div>

        <!--submit-->
        <div class="text-right">
            <button type="submit" id="commonModalSubmitButton"
                class="btn btn-rounded-x btn-danger waves-effect text-left ajax-request"
                data-url="{{ url('app-admin/settings/paystack') }}" data-form-id="landlord-settings-form"
                data-loading-target="" data-ajax-type="post" data-type="form"
                data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
        </div>
    </div>
</div>
@endsection