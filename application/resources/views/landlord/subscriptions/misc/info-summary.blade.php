<table class="table no-border">
    <tbody>
        @if($subscription->subscription_type =='paid')
        <!--amount-->
        <tr>
            <td>@lang('lang.amount')</td>
            <td class="font-medium w-30">{{ runtimeMoneyFormat($subscription->subscription_amount) }}</td>
        </tr>
        <!--billing cycle-->
        <tr>
            <td>@lang('lang.billing_cycle')</td>
            <td class="font-medium">{{ runtimeLang($subscription->subscription_gateway_billing_cycle ?? '---') }}
            </td>
        </tr>
        <!--status-->
        <tr>
            <td>@lang('lang.status')</td>
            <td class="font-medium"><span
                    class="label {{ runtimeSubscriptionStatusColors($subscription->subscription_status) }}">{{ runtimeSubscriptionStatusLang($subscription->subscription_status) }}</span>
            </td>
        </tr>
        @if($subscription->subscription_status =='free-trial')
        <!--trial_end_date-->
        <tr>
            <td>@lang('lang.trial_end_date')</td>
            <td class="font-medium">{{ runtimeDate($subscription->subscription_trial_end) }}</td>
        </tr>
        @else
        <!--payment gateway-->
        <tr>
            <td>@lang('lang.payment_gateway')</td>
            <td class="font-medium">{{ runtimeLang($subscription->subscription_gateway_name ?? '---') }}</td>
        </tr>
        <!--payment gateway-->
        <tr>
            <td>@lang('lang.start_date')</td>
            <td class="font-medium">{{ runtimeDate($subscription->subscription_date_started) }}</td>
        </tr>
        <!--last payment-->
        <tr>
            <td>@lang('lang.last_payment')</td>
            <td class="font-medium">{{ runtimeDate($subscription->subscription_date_renewed) }}</td>
        </tr>
        <!--next payment-->
        <tr>
            <td>@lang('lang.next_payment')</td>
            <td class="font-medium">{{ runtimeDate($subscription->subscription_date_next_renewal) }}</td>
        </tr>
        @endif
        @else
        <tr>
            <td colspan="2" class="font-medium">
                <div class="alert alert-success">@lang('lang.free_subscription_info')</div>
            </td>
        </tr>
        @endif
    </tbody>
</table>