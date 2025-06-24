<div id="paypal-button-container"></div>



<script>
    $(document).ready(function () {
        paypal.Buttons({
            createSubscription: function (data, actions) {
                return actions.subscription.create({
                    'plan_id': '{{ $payload["paypal_plan_id"] }}'
                });
            },
            onApprove: function (data, actions) {

                window.location.replace(
                    "{{ url('app/settings/account/thankyou/paypal?checkout_session_id='.$payload['checkout_session_id']) }}&subscription_id=" +
                    data
                    .subscriptionID);

            }
        }).render('#paypal-button-container');
    });
</script>