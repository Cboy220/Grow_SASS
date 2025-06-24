@foreach($subscriptions as $subscription)
<!--each row-->
<tr id="subscription_{{ $subscription->subscription_id }}">


    <!--subscription_gateway_id-->
    <td class="col_subscription_gateway_id">
        @if($subscription->subscription_type == 'free')
        <span>{{ __('lang.free_plan_id') }}_{{ $subscription->subscription_id }}</span>
        @else
        <span title="{{ $subscription->subscription_gateway_id ?? $subscription->subscription_id }}">{{ str_limit($subscription->subscription_gateway_id ?? $subscription->subscription_id, 17) }}</span>
        @endif
        <!--archived-->
        @if($subscription->subscription_archived == 'yes')
        <span class="label label-icons label-icons-default m-t--5" data-toggle="tooltip" data-placement="top"
            title="@lang('lang.archived_subscription_info')"><i class="ti-archive"></i></span>
        @endif
    </td>

    <!--tenant_name-->
    <td class="col_tenant_name">
        <a
            href="{{ url('app-admin/customers/'.$subscription->subscription_customerid) }}">{{ str_limit($subscription->tenant_name ?? '---', 15) }}</a>
    </td>

    <!--subscription_created-->
    <td class="col_subscription_created">
        {{ runtimeDate($subscription->subscription_created) }}
    </td>


    <!--subscription_amount-->
    <td class="col_subscription_amount">
        {{ runtimeMoneyFormat($subscription->subscription_amount) }}
    </td>

    <!--subscription_date_renewed-->
    <td class="col_subscription_date_renewed">
        {{ runtimeDate($subscription->subscription_date_renewed) }}
    </td>

    <!--subscription_gateway_billing_cycle-->
    <td class="col_subscription_gateway_billing_cycle">
        {{ runtimeLang($subscription->subscription_gateway_billing_cycle) }}
    </td>

    <!--subscription_status-->
    <td class="col_subscription_status">
        <span
            class="label {{ runtimeSubscriptionStatusColors($subscription->subscription_status) }}">{{ runtimeLang($subscription->subscription_status) }}</span>
    </td>

    <!--subscription_gateway-->
    <td class="col_subscription_gateway_name ucwords">
        {{ $subscription->subscription_gateway_name ?? '---' }}
    </td>

    <!--actions-->
    <td class="subscriptions_col_action actions_column"
        id="subscriptions_col_action_{{ $subscription->subscription_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--cancel-->
            @if(in_array($subscription->subscription_status, ['active', 'failed']))
            <button type="button" title="{{ cleanLang(__('lang.cancel_subscription')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.cancel_subscription')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="POST"
                data-url="{{ urlResource('/app-admin/subscriptions/'.$subscription->subscription_id.'/cancel?ref=page') }}">
                <i class="ti-na"></i>
            </button>
            @endif

            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_subscription')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/app-admin') }}/subscriptions/{{ $subscription->subscription_id }}">
                <i class="sl-icon-trash"></i>
            </button>

            <!--more info-->
            <button type="button" title="{{ cleanLang(__('lang.subscription_details')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('app-admin/subscriptions/'.$subscription->subscription_id.'/info') }}"
                data-loading-target="commonModalBody"
                data-footer-visibility="hidden"
                data-modal-title="{{ cleanLang(__('lang.subscription_details')) }}">
                <i class="ti-info-alt"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->