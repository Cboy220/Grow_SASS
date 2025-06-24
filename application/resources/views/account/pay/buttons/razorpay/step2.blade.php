<!--form buttons-->
<script>
    var options = {
        key: "{{ $payload['public_key'] }}",
        amount: "{{ $checkout_session['amount'] }}",
        currency: "{{ $checkout_session['currency'] }}",
        name: "{{ config('system.settings_company_name') }}",
        description: "{{ $payload['plan_name'] }}",
        image: "{{ $payload['logo_url'] }}",
        subscription_id: "{{ $checkout_session['gateway_subscription_id'] }}",
        handler: function (response) {
            if (response.razorpay_payment_id) {
                // Payment successful - redirect to thank you page
                var razorpay_subscription_id = response.razorpay_subscription_id;
                var razorpay_payment_id = response.razorpay_payment_id;
                var redirect_url = "{{ url('app/settings/account/thankyou/razorpay?checkout_session_id=') }}" +
                    razorpay_subscription_id + "&payment_id=" + razorpay_payment_id;
                window.location.href = redirect_url;
            } else {
                //show error page
                var redirect_url = "{{ url('app/settings/account/thankyou/razorpay?payment_status=error') }}";
                window.location.href = redirect_url;
            }
        }
    };

    //dynamically trigger a paynow popup window
    $(document).ready(function (e) {
        var rzp = new Razorpay(options);
        rzp.open();
        e.preventDefault();
    });

    //pay buttun clicked again
    $(document).on('click', '#razorpay-checkout-button-try-again', function (e) {
        var rzp = new Razorpay(options);
        rzp.open();
        e.preventDefault();
    });
</script>



<!--form buttons-->
<div class="text-right">
    <a type="submit" href="javascript:void(0);" id="razorpay-checkout-button-try-again"
        class="text-center btn-block btn btn-danger waves-effect text-left">
        @lang('lang.pay_now')</a>
</div>