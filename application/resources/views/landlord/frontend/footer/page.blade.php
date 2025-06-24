@extends('landlord.frontend.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <form class="form" id="settingsForm">



            <!--footer section-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.section_1')</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm tinymce-textarea-footer" rows="5"
                        name="html_frontend_data_1" id="html_frontend_data_1">{{ $section->frontend_data_1 ?? '' }}</textarea>

                </div>
            </div>

            <!--footer section-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.section_2')</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm tinymce-textarea-footer" rows="5"
                        name="html_frontend_data_2" id="html_frontend_data_2">{{ $section->frontend_data_2 ?? '' }}</textarea>

                </div>
            </div>

            <!--footer section-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.section_3')</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm tinymce-textarea-footer" rows="5"
                        name="html_frontend_data_3" id="html_frontend_data_3">{{ $section->frontend_data_3 ?? '' }}</textarea>

                </div>
            </div>

            <!--footer section-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label">@lang('lang.section_4')</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm tinymce-textarea-footer" rows="5"
                        name="html_frontend_data_4" id="html_frontend_data_4">{{ $section->frontend_data_4 ?? '' }}</textarea>

                </div>
            </div>


            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/footer') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection