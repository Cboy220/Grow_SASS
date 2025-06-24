<!--expiry_date-->
<div class="form-group row">
    <label class="col-sm-12 text-left control-label col-form-label required">@lang('lang.expiry_date')</label>
    <div class="col-sm-12">
        <input type="text" class="form-control form-control-sm pickadate" autocomplete="off" name="expiry_date"
            value="">
        <input class="mysql-date" type="hidden" name="expiry_date" id="expiry_date"
            value="">
    </div>
</div>

<div class="alert alert-info">@lang('lang.set_customer_active_info')</div>