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
            // Handle the payment success or failure
            console.log(response);
        }
    };

    $(document).ready(function () {
        var rzp = new Razorpay(options);
        document.getElementById('razorpay-checkout-button').onclick = function (e) {
            rzp.open();
            e.preventDefault();
        };
    });
</script>
<!--form buttons-->
<div class="text-right">
    <a type="submit" href="javascript:void(0);" id="razorpay-checkout-button"
        class="text-center btn-block btn btn-danger waves-effect text-left disable-on-click">
        @lang('lang.pay_now')</a>
</div>