@if($subscription->subscription_payment_method == 'automatic')
<!--free trial-->
@if($subscription->subscription_status == 'free-trial')
<div class="x-free-trial-info">
    <div class="alert alert-info m-t-20">@lang('lang.subscription_currently_free_trial')</div>
</div>

<!--awaiting payment-->
@elseif($subscription->subscription_status == 'awaiting-payment')
<div class="x-awaiting-payment-info">
    <div class="alert alert-danger m-t-20">@lang('lang.subscription_currently_awaiting_payment')</div>
</div>

<!--free plan-->
@elseif($subscription->subscription_status == 'awaiting-payment')
<div class="x-free-plan-info">
    <div class="alert alert-danger m-t-20">@lang('lang.subscription_currently_awaiting_payment')</div>
</div>
<!--plan inform from payment gateways-->
@else
<div class="x-gateway-info">
    <!--STRIPE-->
    @if($subscription->subscription_gateway_name == 'stripe')
    @include('landlord.subscriptions.misc.info-stripe')
    @endif

    <!--PAYPAL-->

    <!--MANUAL-->
</div>
@endif
@else
<!--offline payments-->
<div class="x-offline-payments-info">
    <div class="alert alert-info m-t-20">@lang('lang.payments_are_made_offline')</div>
</div>
@endif