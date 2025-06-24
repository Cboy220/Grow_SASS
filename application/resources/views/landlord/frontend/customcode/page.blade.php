@extends('landlord.frontend.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <form class="form" id="settingsForm">


            <!--header-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">&#x3C;head&#x3E;..........&#x3C;/head&#x3E;</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm" rows="10" name="settings_code_head"
                        id="settings_code_head">{{ $settings->settings_code_head ?? '' }}</textarea>
                </div>
            </div>


            <!--footer-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">&#x3C;body&#x3E;..........&#x3C;/body&#x3E;</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm" rows="10" name="settings_code_body"
                        id="settings_code_body">{{ $settings->settings_code_body ?? '' }}</textarea>
                </div>
            </div>

            <div class="alert alert-info">@lang('lang.custom_code_info')
            </div>

            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/customcode') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection