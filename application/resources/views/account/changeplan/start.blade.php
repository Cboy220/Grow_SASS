<div class="w-100 text-center" id="change-subscription-plan-form">


    <img class="m-t-30 w-px-300" src="{{ url('public/images/saas/change-plans.png') }}"
        alt="@lang('lang.change_plan')" />

    <div class="m-l-30 m-r-30 m-t-20 m-b-40">
        <h6 class="text-uc font-weight-600 m-b-15">@lang('lang.change_plan')</h6>
        <h4>{{ $package->package_name }}</h4>
        @if($package->package_subscription_options == 'paid')
        <!--item-->
        <div class="form-group row w-px-300 m-l-auto m-r-auto m-b-o m-t-20">
            <div class="col-sm-12">
                <select class="select2-basic form-control form-control-sm" id="billing_cycle"
                    name="billing_cycle">
                    @if($package->package_amount_monthly != '')
                    <option value="monthly">{{ runtimeMoneyFormatSaaS($package->package_amount_monthly) }} / @lang('lang.month')</option>
                    @endif
                    @if($package->package_amount_yearly != '')
                    <option value="yearly">{{ runtimeMoneyFormatSaaS($package->package_amount_yearly) }} / @lang('lang.year')</option>
                    @endif
                </select>
            </div>
        </div>
        @else
        <div class="form-group row w-px-300 m-l-auto m-r-auto m-b-o m-t-20">
            <div class="col-sm-12">
                <select class="select2-basic form-control form-control-sm" id="project_category"
                    name="project_category">
                    <option value="free">{{ runtimeMoneyFormatSaaS(0) }} - @lang('lang.free')</option>
                </select>
            </div>
        </div>
        @endif      

        <!--form buttons-->
        <div class="p-t-0">
            <button type="button" class="btn btn-info confirm-action-info"
                title="{{ cleanLang(__('lang.edit')) }}" data-confirm-title="@lang('lang.change_plan')"
                data-type="form"
                data-form-id="change-subscription-plan-form"
                data-ajax-type="POST" 
                data-confirm-text="@lang('lang.are_you_sure')" data-url="{{ url('settings/account/'.$package->package_id.'/change-plan') }}">
                @lang('lang.select_plan')
            </button>

        </div>
    </div>
</div>