<div class="row">
    <div class="col-lg-12">


        <!--tenant_name-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.name')</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="full_name" name="full_name"
                    value="{{ $customer->tenant_name ?? '' }}">
            </div>
        </div>

        <!--tenant_email-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.email')</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="email_address" name="email_address"
                    value="{{ $customer->tenant_email ?? ''  }}">
            </div>
        </div>

        <!--domain-->
        <div class="modal-selector m-t-30">
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.account_name')</label>
                <div class="col-sm-12 col-lg-9">
                    <div class="input-group input-group-sm input-group-both">
                        <span class="input-group-addon">https://</span>
                        <input type="text" class="form-control" id="account_name" name="account_name"
                            value="{{ $customer->subdomain ?? ''  }}">
                        <span class="input-group-addon"> . {{ config('system.settings_base_domain') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>