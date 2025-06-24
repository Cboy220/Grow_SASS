<div class="row">
    <div class="col-lg-12">

        <!--package-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-4 text-left control-label col-form-label required">@lang('lang.package')</label>
            <div class="col-sm-12 col-lg-8">
                <select class="select2-basic form-control form-control-sm" id="package_id" name="package_id">
                    @foreach($packages as $package)
                    <option value="{{ $package->package_id }}" title="{{ $package->package_subscription_options }}">
                        {{ $package->package_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>


        <!--billing cycle-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-4 text-left control-label col-form-label required">@lang('lang.billing_cycle')</label>
            <div class="col-sm-12 col-lg-8" id="package-type-paid">
                <select class="select2-basic form-control form-control-sm" id="billing_cycle" name="billing_cycle">
                    <option value="monthly">@lang('lang.monthly')</option>
                    <option value="yearly">@lang('lang.yearly')</option>
                </select>
            </div>
        </div>

        <!--free trial-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-4 text-left control-label col-form-label required">@lang('lang.free_trial')</label>
            <div class="col-sm-12 col-lg-8" id="package-type-paid">
                <select class="select2-basic form-control form-control-sm" id="free_trial" name="free_trial">
                    <option value="no">@lang('lang.no')</option>
                    <option value="yes">@lang('lang.yes')</option>
                </select>
            </div>
        </div>

        <!--free_trial_days-->
        <div class="hidden" id="toggle_trial_date">
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-4 text-left control-label col-form-label required">@lang('lang.free_trial_duration')</label>
                <div class="col-sm-12 col-lg-8">
                    <input type="number" class="form-control form-control-sm" id="free_trial_days" name="free_trial_days">
                </div>
            </div>
        </div>
        

        <div class="modal-selector m-t-12 m-b-12">
            <!--payment type-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-4 text-left control-label col-form-label required">@lang('lang.payment_method')</label>
                <div class="col-sm-12 col-lg-8" id="package-type-paid">
                    <select class="select2-basic form-control form-control-sm" id="subscription_payment_method"
                        name="subscription_payment_method">
                        <option value="automatic">@lang('lang.automatic_billing')</option>
                        <option value="offline">@lang('lang.manual_billing')</option>
                    </select>
                </div>
            </div>

            <!--automatic payment info-->
            <div class="alert alert-info" id="automatic_payments_toggle">
                @lang('lang.automatic_payments_info')
            </div>
            <!--automatic payment info-->
            <div class="alert alert-info hidden" id="offline_payments_toggle">
                @lang('lang.offline_payment_info')
            </div>
        </div>
    </div>
</div>