<div class="card count-{{ @count($payments) }}" id="payments-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($payments) > 0)
            <table id="payments-list-table" class="table m-t-0 m-b-0 table-hover no-wrap payment-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <!--payment_id-->
                        <th class="col_payment_id"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_payment_id" href="javascript:void(0)"
                                data-url="{{ urlResource('/settings/account/payments?action=sort&orderby=payment_id&sortorder=asc') }}">@lang('lang.id')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--payment_created-->
                        <th class="col_payment_created"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_payment_created" href="javascript:void(0)"
                                data-url="{{ urlResource('/settings/account/payments?action=sort&orderby=payment_created&sortorder=asc') }}">@lang('lang.date')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--payment_transaction_id-->
                        <th class="col_payment_transaction_id"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_payment_transaction_id" href="javascript:void(0)"
                                data-url="{{ urlResource('/settings/account/payments?action=sort&orderby=payment_transaction_id&sortorder=asc') }}">@lang('lang.transaction_id')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--payment_amount-->
                        <th class="col_payment_amount"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_payment_amount" href="javascript:void(0)"
                                data-url="{{ urlResource('/settings/account/payments?action=sort&orderby=payment_amount&sortorder=asc') }}">@lang('lang.amount')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--payment_gateway-->
                        <th class="col_payment_amount"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_payment_gateway" href="javascript:void(0)"
                                data-url="{{ urlResource('/settings/account/payments?action=sort&orderby=payment_gateway&sortorder=asc') }}">@lang('lang.description')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                    </tr>
                </thead>
                <tbody id="payments-td-container">
                    <!--ajax content here-->
                    @include('account.payments.table.ajax')
                    <!--ajax content here-->

                    <!--bulk actions - change category-->
                    <input type="hidden" name="checkbox_actions_payments_category"
                        id="checkbox_actions_payments_category">
                </tbody>
                <tpaymentt>
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--load more button-->
                        </td>
                    </tr>
                </tpaymentt>
            </table>
            @endif @if (@count($payments) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>