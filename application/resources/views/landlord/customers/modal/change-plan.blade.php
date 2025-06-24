<div class="row">
    <div class="col-lg-12">

        <!--plans-->
        <div class="modal-selector">
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.plan')</label>
                <div class="col-sm-12">
                    <select class="select2-basic form-control form-control-sm" id="changed_package_id" name="changed_package_id">
                        <option></option>
                        @foreach($packages as $package)
                        @if($package->package_subscription_options == 'paid')
                        <option data-option="monthly" value="{{  $package->package_id }}">{{  $package->package_name }}
                            ({{ runtimeMoneyFormat($package->package_amount_monthly) }} / @lang('lang.month'))
                        </option>
                        <option data-option="yearly" value="{{  $package->package_id }}">{{  $package->package_name }}
                            ({{ runtimeMoneyFormat($package->package_amount_yearly) }} / @lang('lang.year'))
                        </option>
                        @else
                        <option data-option="free" value="{{  $package->package_id }}">{{  $package->package_name }}
                            (@lang('lang.free'))</option>
                        @endif
                        @endforeach
                    </select>
                    <input type="hidden" name="billing_cycle" id="billing_cycle" value="monthly">
                </div>
            </div>
        </div>

        
        <!--subscription_payment_method-->
        <div class="hidden" id="toggle_subscription_payment_method">
            <!--item-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.billing')</label>
                <div class="col-sm-12">
                    <select class="select2-basic form-control form-control-sm" id="subscription_payment_method"
                        name="subscription_payment_method">
                        <option value="automatic">@lang('lang.automatic_billing')</option>
                        <option value="offline">@lang('lang.manual_billing')</option>
                    </select>
                </div>
            </div>
        </div>

        <!--free trial-->
        <div class="form-group row hidden" id="free_plan_container">
            <label
                class="col-sm-12 text-left control-label col-form-label required">@lang('lang.free_trial')</label>
            <div class="col-sm-12" id="package-type-paid">
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
                    class="col-sm-12 text-left control-label col-form-label required">@lang('lang.free_trial_duration')</label>
                <div class="col-sm-12">
                    <div class="input-group input-group-sm input-group-right">
                        <input type="text" class="form-control" id="free_trial_days" name="free_trial_days"
                            value="{{ config('system.settings_free_trial_days') ?? 0 }}">
                        <span class="input-group-addon">@lang('lang.days')</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>