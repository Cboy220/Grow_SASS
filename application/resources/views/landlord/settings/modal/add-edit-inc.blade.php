<div class="row">
    <div class="col-lg-12">

        <!--description-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">@lang('lang.description')*</label>
            <div class="col-sm-12 col-lg-9">
                <textarea class="w-100" id="foo_description" rows="5" name="foo_description"
                    >{{ $foo->foo_description ?? '' }}</textarea>
            </div>
        </div>
        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>