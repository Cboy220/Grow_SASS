@extends('landlord.frontend.wrapper')
@section('settings_content')

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

            <!--heading_2-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.heading_2')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_2" name="frontend_data_2"
                        value="{{ $section->frontend_data_2 ?? '' }}">
                </div>
            </div>

            <!--heading_1-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.heading_1')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_3" name="frontend_data_3"
                        value="{{ $section->frontend_data_3 ?? '' }}">
                </div>
            </div>


            <!--button_1_text-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_1_text')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_4" name="frontend_data_4"
                        value="{{ $section->frontend_data_4 ?? '' }}">
                </div>
            </div>


            <!--button_1_link-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_1_link')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_5" name="frontend_data_5"
                        value="{{ $section->frontend_data_5 ?? '' }}">
                </div>
            </div>


            <!--button_2_text-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_2_text')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_6" name="frontend_data_6"
                        value="{{ $section->frontend_data_6 ?? '' }}">
                </div>
            </div>


            <!--button_2_link-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.button_2_link')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="frontend_data_7" name="frontend_data_7"
                        value="{{ $section->frontend_data_7 ?? '' }}">
                </div>
            </div>

            <!--fileupload-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.main_image')</label>
                <div class="col-12">
                    <div class="dropzone dz-clickable" id="fileupload_image">
                        <div class="dz-default dz-message">
                            <i class="icon-Upload-toCloud"></i>
                            <span>@lang('lang.drag_drop_file')</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--#fileupload-->

            <!--fileupload-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.background_image')</label>
                <div class="col-12">
                    <div class="dropzone dz-clickable" id="fileupload_image_2">
                        <div class="dz-default dz-message">
                            <i class="icon-Upload-toCloud"></i>
                            <span>@lang('lang.drag_drop_file')</span>
                        </div>
                    </div>
                </div>
            </div>
            <!--#fileupload-->

            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/hero') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection