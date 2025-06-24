@extends('landlord.settings.wrapper')
@section('settings_content')

<!--page heading-->
<div class="row page-titles">
    @include('landlord.misc.crumbs')
</div>

<!--form-->
<div class="card">
    <div class="card-body" id="landlord-settings-form">


        <!--title-->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.title')*</label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm" id="blog_title" name="blog_title"
                    value="{{ $blog->blog_title ?? '' }}">
            </div>
        </div>

        <!--body-->
        <div class="form-group row">
            <div class="col-sm-12">
                <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="blog_text"
                    id="blog_text">{{ $blog->blog_text ?? '' }}</textarea>
            </div>
        </div>

        <!--date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.date')*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm pickadate" name="blog_created" autocomplete="off"
                    value="{{ runtimeDatepickerDate($blog->blog_created ?? now()) }}">
                <input class=" mysql-date" type="hidden" name="blog_created" id="blog_created"
                    value="{{ $blog->blog_created ?? '' }}">
            </div>
        </div>

        <!--(select2-preselected) &  (data-preselected) are optional-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.status')*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm select2-preselected" id="blog_status"
                    name="blog_status" data-preselected="{{ $blog->blog_status ?? 'draft'}}">
                    <option></option>
                    <option value="draft">@lang('lang.draft')</option>
                    <option value="published">@lang('lang.published')</option>
                </select>
            </div>
        </div>

        <!--form buttons-->
        <div class="text-right p-t-30">
            <button type="submit" id="submitButton" class="btn btn-danger waves-effect text-left ajax-request"
                data-url="{{ url('app-admin/blogs') }}" data-type="form" data-form-id="landlord-settings-form" data-ajax-type="POST"
                data-loading-target="main-body">@lang('lang.save_changes')</button>
        </div>

    </div>
</div>
@endsection