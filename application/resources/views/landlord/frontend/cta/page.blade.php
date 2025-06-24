@extends('landlord.frontend.wrapper')
@section('settings_content')

<!--tabs menu-->
@include('landlord.frontend.components.home-menu')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <form class="form" id="settingsForm">


            <!--heading_1-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.heading_1')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_1" name="frontend_data_1"
                        value="{{ $section->frontend_data_1 ?? '' }}">
                </div>
            </div>

            <!--button_1_text-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_1_text')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_3" name="frontend_data_3"
                        value="{{ $section->frontend_data_3 ?? '' }}">
                </div>
            </div>


            <!--button_1_link-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_1_link')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_4" name="frontend_data_4"
                        value="{{ $section->frontend_data_4 ?? '' }}">
                </div>
            </div>


            <!--button_2_text-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_2_text')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_5" name="frontend_data_5"
                        value="{{ $section->frontend_data_5 ?? '' }}">
                </div>
            </div>


            <!--button_2_link-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_2_link')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_6" name="frontend_data_6"
                        value="{{ $section->frontend_data_6 ?? '' }}">
                </div>
            </div>

            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/section/'.request()->segment(4).'/cta') }}"
                    data-form-id="landlord-settings-form" data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection