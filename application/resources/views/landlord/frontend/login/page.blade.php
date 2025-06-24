@extends('landlord.frontend.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <form class="form" id="settingsForm">


            <!--heading-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.heading')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_1" name="frontend_data_1"
                        value="{{ $section->frontend_data_1 ?? '' }}">
                </div>
            </div>

            <!--sub_heading-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.subheading')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_2" name="frontend_data_2"
                        value="{{ $section->frontend_data_2 ?? '' }}">
                </div>
            </div>

            <!--account_name-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.account_name') (URL)</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_4" name="frontend_data_4"
                        value="{{ $section->frontend_data_4 ?? '' }}">
                </div>
            </div>

            <!--submit_button_text-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.submit_button_text')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_3" name="frontend_data_3"
                        value="{{ $section->frontend_data_3 ?? '' }}">
                </div>
            </div>


            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/login') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection