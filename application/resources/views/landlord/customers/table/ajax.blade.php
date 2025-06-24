@foreach($customers as $customer)
<!--each row-->
<tr id="customer_{{ $customer->tenant_id }}">
    <!--id-->
    <td class="tenants_col_id" id="tenants_col_rate_{{ $customer->tenant_id }}">
        {{ $customer->tenant_id }}
    </td>

    <!--first_name-->
    <td class="tenants_col_name">
        <a href="{{ url('app-admin/customers/'.$customer->tenant_id) }}">{{ $customer->tenant_name }}</a>
    </td>

    <!--tenant_created-->
    <td class="tenants_col_tenant_created">
        {{ runtimeDate($customer->tenant_created) }}
    </td>

    <!--domain-->
    <td class="tenants_col_domain">
        <a href="https://{{ $customer->domain }}" target="_blank">{{ $customer->domain }}</a>
    </td>

    <!--package_name-->
    <td class="tenants_col_package_name">
        <a href="{{ url('app-admin/packages?filter_package_id='.$customer->package_id) }}"
            target="_blank">{{ $customer->package_name ?? '---' }}</a>
    </td>

    <!--tenant_package_type-->
    <td class="tenants_col_tenant_package_type">
        {{ runtimeLang($customer->subscription_type ?? '---') }}
    </td>

    <!--tenant_status-->
    <td class="tenants_col_rate">
        <span
            class="label {{ runtimeCustomerStatusColors($customer->tenant_status) }}">{{ runtimeCustomerStatusLang($customer->tenant_status) }}</span>
    </td>

    <td class="tenants_col_action actions_column" id="tenants_col_action_{{ $customer->tenant_id }}">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <!--delete-->
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_customer')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                data-url="{{ url('/app-admin') }}/customers/{{ $customer->tenant_id }}">
                <i class="sl-icon-trash"></i>
            </button>
            <!--edit-->
            <span class="list-table-action dropdown" style="font-size: inherit;">
                <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" class="btn btn-outline-default btn-circle btn-sm">
                    <i class="sl-icon-note"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="listTableAction">
                    <!--edit account-->
                    <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="@lang('lang.edit_account')"
                        data-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/edit') }}"
                        data-action-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id) }}"
                        data-loading-target="commonModalBody" data-action-method="PUT">
                        @lang('lang.edit_account')</a>

                    <!--update password-->
                    <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="@lang('lang.update_password')"
                        data-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/update-password?ref=list') }}"
                        data-action-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/update-password?ref=list') }}"
                        data-loading-target="commonModalBody" data-action-method="POST">
                        @lang('lang.update_password')</a>
                </div>
            </span>

            <!--login in as a customer-->
            <a type="button" title="@lang('lang.login_in_as_customer')"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm" target="_blank"
                href="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/login?ref=list') }}">
                <i class="sl-icon-people"></i>
            </a>


            @if(!config('app.application_demo_mode'))
            @if($customer->tenant_email_config_type == 'local' && $customer->tenant_email_config_status == 'pending' && config('customer_defaults.defaults_email_delivery') == 'smtp_and_sendmail')
            <button type="button"
                class="email_settings_pending_{{ $customer->tenant_id }} btn btn-outline-info btn-circle btn-sm data-toggle-action-tooltip edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                title="{{ cleanLang(__('lang.mail_action_required')) }}" data-toggle="modal" data-target="#commonModal"
                data-url="{{ url('app-admin/customers/'.$customer->tenant_id.'/email?source=list') }}"
                data-loading-target="commonModalBody" data-footer-visibility="hidden" data-modal-size="modal-xl"
                data-modal-title="@lang('lang.mail_delivery_server')">
                <i class="ti-email"></i>
                <div class="notify email-blinking-icon-table"> <span class="heartbit"></span> <span
                        class="point"></span> </div>
            </button>
            @endif
            @endif



        </span>
        <!--action button-->
    </td>
</tr>
@endforeach
<!--each row-->