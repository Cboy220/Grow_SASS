@foreach($payments as $payment)
<!--each row-->
<tr id="payment_{{ $payment->payment_id }}">
    <!--payment_id-->
    <td class="col_payment_id">
        {{ $payment->payment_id }}
    </td>
    <!--tenant_name-->
    @if(config('visibility.col_tenant_name'))
    <td class="col_tenant_name">
        <a href="{{ url('app-admin/customers/'.$payment->payment_tenant_id) }}">{{ $payment->tenant_name }}</a>
    </td>
    @endif
    <!--payment_created-->
    <td class="col_payment_created">
        {{ runtimeDate($payment->payment_date) }}
    </td>
    <!--payment_transaction_id-->
    <td class="col_payment_transaction_id">
        {{ $payment->payment_transaction_id ?? '---' }}
    </td>
    <!--payment_amount-->
    <td class="col_payment_amount">
        {{ runtimeMoneyFormat($payment->payment_amount) }}
    </td>
    <!--payment_gateway-->
    @if(config('visibility.col_payment_gateway'))
    <td class="col_payment_gateway text-ucf">
        {{ $payment->payment_gateway ?? '---' }}
    </td>
    @endif
    <td class="payments_col_action actions_column" id="payments_col_action_{{ $payment->payment_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.delete_payment')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('/app-admin/payments/'.$payment->payment_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--edit-->
            <button type="button" title="{{ cleanLang(__('lang.edit')) }}"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="{{ urlResource('/app-admin/payments/'.$payment->payment_id.'/edit') }}"
                data-loading-target="commonModalBody" data-modal-title="@lang('lang.edit_payment')"
                data-action-url="{{ urlResource('/app-admin/payments/'.$payment->payment_id.'?ref=list') }}"
                data-action-method="PUT" data-action-ajax-class=""
                data-action-ajax-loading-target="payments-td-container">
                <i class="sl-icon-note"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->