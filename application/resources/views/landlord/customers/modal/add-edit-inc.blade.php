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


        <!--item-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.plan')</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="plan" name="plan">
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


        <!--subscription_payment_method-->
        <div class="hidden" id="toggle_subscription_payment_method">
            <!--item-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-5 text-left control-label col-form-label required">@lang('lang.billing')</label>
                <div class="col-sm-12 col-lg-7">
                    <select class="select2-basic form-control form-control-sm" id="subscription_payment_method"
                        name="subscription_payment_method">
                        <option value="automatic">@lang('lang.automatic_billing')</option>
                        <option value="offline">@lang('lang.manual_billing')</option>
                    </select>
                </div>
            </div>
        </div>


        <!--free trial-->
        <div class="hidden" id="free_plan_container">
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-5 text-left control-label col-form-label required">@lang('lang.free_trial')</label>
                <div class="col-sm-12 col-lg-7" id="package-type-paid">
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
                        class="col-sm-12 col-lg-5 text-left control-label col-form-label required">@lang('lang.free_trial_duration')</label>
                    <div class="col-sm-12 col-lg-7">
                        <div class="input-group input-group-sm input-group-right">
                            <input type="text" class="form-control" id="free_trial_days" name="free_trial_days"
                                value="{{ config('system.settings_free_trial_days') ?? 0 }}">
                            <span class="input-group-addon">@lang('lang.days')</span>
                        </div>
                    </div>

                </div>
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

        @if(config('visibility.send_welcome_email_checkbox'))
        <div class="form-group form-group-checkbox row">
            <div class="col-12 p-t-5">
                <input type="checkbox" id="send_welcome_email" name="send_welcome_email"
                    class="filled-in chk-col-light-blue" checked>
                <label class="p-l-30" for="send_welcome_email">@lang('lang.send_welcome_email')</label>
            </div>
        </div>
        @endif

        <!--DEMO INFO-->
        @if(config('app.application_demo_mode'))
        <div class="alert alert-danger"><h5 class="text-danger"><i class="sl-icon-danger"></i> Demo Info</h5>
          This customer's account will be created with a demo password <strong>growcrm</strong>  
        </div>
        @endif

    </div>
</div>