<div class="card count-{{ @count($customers) }}" id="customer-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            @if (@count($customers) > 0)
            <table id="customer-list-table" class="table m-t-0 m-b-0 table-hover no-wrap tenant-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <!--tenant_id-->
                        <th class="tenants_col_tenant_id"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_id" href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/customers?action=sort&orderby=tenant_id&sortorder=asc') }}">@lang('lang.id')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_name-->
                        <th class="tenants_col_tenant_name"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_name" href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/customers?action=sort&orderby=tenant_name&sortorder=asc') }}">@lang('lang.name')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_created-->
                        <th class="tenants_col_tenant_created"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_created" href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/customers?action=sort&orderby=tenant_created&sortorder=asc') }}">@lang('lang.created')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--domain-->
                        <th class="tenants_col_domain"><a class="js-ajax-ux-request js-list-sorting" id="sort_domain"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/customers?action=sort&orderby=domain&sortorder=asc') }}">@lang('lang.account_url')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--package_name-->
                        <th class="tenants_col_package_name"><a class="js-ajax-ux-request js-list-sorting" id="sort_domain"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/customers?action=sort&orderby=package_name&sortorder=asc') }}">@lang('lang.package')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_package_type-->
                        <th class="tenants_col_tenant_package_type"><a class="js-ajax-ux-request js-list-sorting" id="sort_domain"
                                href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/customers?action=sort&orderby=tenant_package_type&sortorder=asc') }}">@lang('lang.type')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_status-->
                        <th class="tenants_col_tenant_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_status" href="javascript:void(0)"
                                data-url="{{ urlResource('/app-admin/customers?action=sort&orderby=tenant_status&sortorder=asc') }}">@lang('lang.status')<span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--actions-->
                        <th class="tenants_col_action"><a href="javascript:void(0)">@lang('lang.action')</a></th>
                    </tr>
                </thead>
                <tbody id="customer-td-container">
                    <!--ajax content here-->
                    @include('landlord.customers.table.ajax')
                    <!--ajax content here-->
                </tbody>
                <tbody class="border-0">
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            @include('misc.load-more-button')
                            <!--load more button-->
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif @if (@count($customers) == 0)
            <!--nothing found-->
            @include('notifications.no-results-found')
            <!--nothing found-->
            @endif
        </div>
    </div>
</div>