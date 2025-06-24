@extends('landlord.frontend.wrapper')
@section('settings_content')

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">

        <form class="form" id="settingsForm">


            <!--title-->
            <div class="form-group row">
                <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.title')</label>
                <div class="col-sm-12">
                    <input type="text" class="form-control form-control-sm" id="settings_code_meta_title"
                        name="settings_code_meta_title" value="{{ $settings->settings_code_meta_title ?? '' }}">
                </div>
            </div>


            <!--description-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.description')</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm" rows="5" name="settings_code_meta_description"
                        id="settings_code_meta_description">{{ $settings->settings_code_meta_description ?? '' }}</textarea>
                </div>
            </div>

            <div class="alert alert-info">@lang('lang.meta_tags_info')
            </div>

            <!--submit-->
            <div class="text-right">
                <button type="submit" id="commonModalSubmitButton"
                    class="btn btn-rounded-x btn-danger btn-sm waves-effect text-left ajax-request"
                    data-url="{{ url('/app-admin/frontend/metatags') }}" data-form-id="landlord-settings-form"
                    data-loading-target="" data-ajax-type="post" data-type="form"
                    data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
            </div>
        </form>
    </div>
</div>
@endsection