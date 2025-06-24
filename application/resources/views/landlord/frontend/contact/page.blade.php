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


            <!--introduction_content-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.introduction_content')</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm tinymce-textarea-extended" rows="5"
                        name="html_frontend_data_2" id="html_frontend_data_2">{{ $section->frontend_data_2 ?? '' }}</textarea>

                </div>
            </div>


            <!--delivery_email_address-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.delivery_email_address')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_3" name="frontend_data_3"
                        value="{{ $section->frontend_data_3 ?? '' }}">
                </div>
            </div>


            <!--delivery_subject-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.email_subject')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_6" name="frontend_data_6"
                        value="{{ $section->frontend_data_6 ?? '' }}">
                </div>
            </div>


            <!--introduction_content-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.thank_you_message')</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm tinymce-textarea-extended" rows="5"
                        name="html_frontend_data_5" id="html_frontend_data_2">{{ $section->frontend_data_5 ?? '' }}</textarea>

                </div>
            </div>


            <!--submit_button_text-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.submit_button_text')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_4" name="frontend_data_4"
                        value="{{ $section->frontend_data_4 ?? '' }}">
                </div>
            </div>



            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/contact') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection