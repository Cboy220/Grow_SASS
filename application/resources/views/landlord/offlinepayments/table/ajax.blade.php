@foreach($payments as $payment)
<!--each row-->
<tr id="payment_{{ $payment->proof_id }}">
    <!--proof_date-->
    <td class="col_proof_date">
        {{ runtimeDate($payment->proof_date) }}
    </td>

    <!--tenant_name-->
    <td class="col_tenant_name">
        <a href="{{ url('app-admin/customers/'.$payment->proof_tenant_id) }}">{{ $payment->tenant_name }}</a>
    </td>

    <!--tenant_domain-->
    <td class="col_tenant_domain">
        <a href="https://{{ $payment->domain }}">{{ $payment->domain }}</a>
    </td>

    <!--proof_amount-->
    <td class="col_proof_amount">
        {{ runtimeMoneyFormat($payment->proof_amount) }}
    </td>
    <!--proof_filename-->
    <td class="col_proof_filename">
        <a href="storage/files/{{ $payment->proof_directory }}/{{ $payment->proof_filename  }}" target="_blank"
            download>{{ str_limit($payment->proof_filename ?? '---', 20) }}</a>
    </td>
    <!--actions-->
    <td class="payments_col_action actions_column">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="@lang('lang.delete_payment')" data-confirm-text="@lang('lang.are_you_sure')"
                data-ajax-type="DELETE" data-url="{{ url('/app-admin/offline-payments/'.$payment->proof_id) }}">
                <i class="sl-icon-trash"></i>
            </button>
        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->