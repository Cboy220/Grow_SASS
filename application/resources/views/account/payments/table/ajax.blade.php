@foreach($payments as $payment)
<!--each row-->
<tr id="payment_{{ $payment->payment_id }}">
    <!--payment_id-->
    <td class="col_payment_id">
        {{ $payment->payment_id }}
    </td>
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
    <td class="col_payment_amount text-ucf">
        {{ $payment->payment_gateway ?? '---' }}
    </td>
</tr>
@endforeach
<!--each row-->