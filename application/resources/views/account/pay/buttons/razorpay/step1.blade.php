<!--form buttons-->
<div class="text-right">
    <a type="submit" href="javascript:void(0);" id="razorpay-checkout-button"
        data-url="{{ url('settings/account/'.$payload['subscription_uniqueid'].'/pay/razorpay/initiate') }}"
        class="text-center btn-block btn btn-danger waves-effect text-left disable-on-click ajax-request">
        @lang('lang.pay_now')</a>
</div>