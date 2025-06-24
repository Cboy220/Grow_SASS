<div class="card count-{{ @count($subscriptions) }}" id="subscriptions-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($subscriptions) > 0)
            <table id="subscriptions-list-table" class="table m-t-0 m-b-0 table-hover no-wrap subscription-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <!--subscription_gateway_id-->
                        <th class="col_subscription_gateway_id"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_subscription_gateway_id" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_gateway_id&sortorder=asc') }}">@lang('lang.id')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_name-->
                        <th class="col_tenant_name"><a class="js-ajax-ux-request js-list-sorting" id="sort_tenant_name"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=tenant_name&sortorder=asc') }}">@lang('lang.customer')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--subscription_amount-->
                        <th class="col_subscription_created"><a class="js-ajax-ux-request js-list-sorting"
                            id="sort_subscription_created" href="javascript:void(0)"
                            data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_created&sortorder=asc') }}">@lang('lang.created')<span
                                class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--subscription_amount-->
                        <th class="col_subscription_amount"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_subscription_amount" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_amount&sortorder=asc') }}">@lang('lang.amount')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--subscription_date_renewed-->
                        <th class="col_subscription_date_renewed"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_subscription_date_renewed" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_date_renewed&sortorder=asc') }}">@lang('lang.renewed')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--subscription_gateway_billing_cycle-->
                        <th class="col_subscription_gateway_billing_cycle"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_subscription_gateway_billing_cycle" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_gateway_billing_cycle&sortorder=asc') }}">@lang('lang.cycle')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--subscription_status-->
                        <th class="col_subscription_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_subscription_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_status&sortorder=asc') }}">@lang('lang.status')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--subscription_gateway_name-->
                        <th class="col_subscription_gateway_name"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_subscription_gateway_name" href="javascript:void(0)"
                                data-url="{{ urlResource('/subscriptions?action=sort&orderby=subscription_gateway_name&sortorder=asc') }}">@lang('lang.gateway')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--actions-->
                        <th class="col_action"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="subscriptions-td-container">
                    <!--ajax content here-->
                    @include('landlord.subscriptions.table.ajax')
                    <!--ajax content here-->

                    <!--bulk actions - change category-->
                    <input type="hidden" name="checkbox_actions_subscriptions_category"
                        id="checkbox_actions_subscriptions_category">
                </tbody>
                <tsubscriptiont>
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--load more button-->
                        </td>
                    </tr>
                </tsubscriptiont>
            </table>
            @endif @if (@count($subscriptions) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>