<div class="card count-{{ @count($payments) }}" id="payments-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($payments) > 0)
            <table id="payments-list-table" class="table m-t-0 m-b-0 table-hover no-wrap payment-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <!--proof_date-->
                        <th class="col_proof_date"><a class="js-ajax-ux-request js-list-sorting" id="sort_proof_date"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/offline-payments?action=sort&orderby=proof_date&sortorder=asc') }}">@lang('lang.date')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--tenant_name-->
                        <th class="col_tenant_name"><a class="js-ajax-ux-request js-list-sorting" id="sort_tenant_name"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/offline-payments?action=sort&orderby=tenant_name&sortorder=asc') }}">@lang('lang.customer')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--tenant_domain-->
                        <th class="col_tenant_domain"><a href="javascript:void(0)">@lang('lang.account')</a></th>

                        <!--proof_amount-->
                        <th class="col_proof_amount"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_proof_amount" href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/offline-payments?action=sort&orderby=proof_amount&sortorder=asc') }}">@lang('lang.amount')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--proof_filename-->
                        <th class="col_proof_filename"><a href="javascript:void(0)">@lang('lang.attachment')</a></th>

                        <!--actions-->
                        <th class="col_action"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="payments-td-container">
                    <!--ajax content here-->
                    @include('landlord.offlinepayments.table.ajax')
                    <!--ajax content here-->
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