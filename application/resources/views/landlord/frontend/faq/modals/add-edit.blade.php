<!--faq_title-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.title')</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm" id="faq_title" name="faq_title"
            value="{{ $faq->faq_title ?? '' }}">
    </div>
</div>


<!--item-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.content')</label>
    <div class="col-sm-12">
        <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="faq_content"
            id="faq_content">{{ $faq->faq_content ?? '' }}</textarea>
    </div>
</div>