<!--item-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.title')</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm" id="frontend_data_1" name="frontend_data_1"
            value="{{ $item->frontend_data_1 ?? '' }}">
    </div>
</div>

<!--item-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.description')</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm" id="frontend_data_2" name="frontend_data_2"
            value="{{ $item->frontend_data_2 ?? '' }}">
    </div>
</div>


<!--fileupload-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.image')</label>
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