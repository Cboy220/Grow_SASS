<table class="table no-border">
    <tbody>
        <!--subscription id-->
        <tr>
            <td>@lang('lang.subscription_id')</td>
            <td class="font-medium w-30">{{ $subscription->subscription_gateway_id ?? '---'}}</td>
        </tr>
        <!--stripe plan-->
        <tr>
            <td>@lang('lang.stripe_plan_id')</td>
            <td class="font-medium w-30">{{ $subscription->subscription_gateway_plan_id ?? '---'}}</td>
        </tr>
        <!--stripe price-->
        <tr>
            <td>@lang('lang.stripe_price_id')</td>
            <td class="font-medium">{{ $subscription->subscription_gateway_price_id ?? '---' }}
            </td>
        </tr>
    </tbody>
</table>