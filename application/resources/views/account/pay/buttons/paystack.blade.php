<!--form buttons-->
<div class="text-right">
    <a type="submit" id="submitButton" class="text-center btn-block btn btn-danger waves-effect text-left disable-on-click"
        href="https://checkout.paystack.com/{{ $payload['checkout_session_id'] }}">
        @lang('lang.pay_now')</a>
</div>