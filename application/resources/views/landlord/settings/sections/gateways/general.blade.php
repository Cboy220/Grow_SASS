@extends('landlord.settings.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <form class="form" id="settingsFormGeneral">
            <!--settings_gateways_default_product_name-->
            <div class="form-group row">
                <label class="col-12 control-label col-form-label">@lang('lang.default_product_name')</label>
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm" id="settings_gateways_default_product_name"
                        name="settings_gateways_default_product_name"
                        value="{{ $settings->settings_gateways_default_product_name ?? '' }}">
                </div>
            </div>

            <!--settings_gateways_default_product_description-->
            <div class="form-group row">
                <label class="col-12 control-label col-form-label">@lang('lang.default_product_description')</label>
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm"
                        id="settings_gateways_default_product_description"
                        name="settings_gateways_default_product_description"
                        value="{{ $settings->settings_gateways_default_product_description ?? '' }}">
                </div>
            </div>

            <div class="alert alert-info">@lang('lang.default_product_name_info')</div>
            <div class="line"></div>
            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger waves-effect text-left ajax-request"
                    data-url="{{ url('app-admin/settings/gateways') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection