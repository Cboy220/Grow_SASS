<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-7 p-b-9 align-self-center text-right list-page-actions-containers {{ $page['list_page_actions_size'] ?? '' }} {{ $page['list_page_container_class'] ?? '' }}"
    id="list-page-actions-container-customer">
    <div id="list-page-actions">
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                Edit Customer
            </button>
            <!--edit account-->
            <div class="dropdown-menu" x-placement="top-start"
                style="position: absolute; transform: translate3d(0px, -197px, 0px); top: 0px; left: 0px; will-change: transform;">

                <!--edit account-->
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0);" data-toggle="modal" data-target="#commonModal"
                    data-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/edit') }}"
                    data-loading-target="commonModalBody" data-modal-title="{{ cleanLang(__('lang.edit_account')) }}"
                    data-action-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'?ref=page') }}"
                    data-action-method="PUT" data-action-ajax-class=""
                    data-action-ajax-loading-target="customer-td-container">@lang('lang.edit_account')</a>

                <!--cancel subscription-->
                @if(config('visibility.has_subscription'))
                <a class="dropdown-item confirm-action-danger" data-confirm-title="@lang('lang.delete_subscription')"
                    href="javascript:void(0);" data-confirm-text="@lang('lang.delete_subscription_info')" data-ajax-type="POST"
                    data-url="{{ urlResource('/app-admin/subscriptions/'.$subscription->subscription_id.'/cancel?ref=page') }}">
                    @lang('lang.cancel_subscription')
                </a>
                @endif

                <!--delete subscription (same as cancel - needed - do not remove)-->
                @if(config('subscription_status') == 'awaiting-payment')
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-modal-title="@lang('lang.set_to_active')"
                    data-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/set-active?ref=list') }}"
                    data-action-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/set-active?ref=list') }}"
                    data-loading-target="commonModalBody" data-action-method="POST">
                    @lang('lang.set_to_active')</a>
                @endif


                <!--change subscription plan-->
                @if(in_array(config('subscription_status'), ['active', 'free-trial', 'awaiting-payment']))
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0);" data-toggle="modal" data-target="#commonModal"
                    data-url="{{ urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan') }}"
                    data-loading-target="commonModalBody" data-modal-title="@lang('lang.change_plan')"
                    data-action-url="{{ urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan?ref=page') }}"
                    data-action-method="POST" data-action-ajax-class=""
                    data-action-ajax-loading-target="customer-td-container">@lang('lang.change_plan')</a>
                @endif

                <!--create subscription-->
                @if(!config('visibility.has_subscription'))
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0);" data-toggle="modal" data-target="#commonModal"
                    data-url="{{ urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan') }}"
                    data-loading-target="commonModalBody" data-modal-title="@lang('lang.create_subscription')"
                    data-action-url="{{ urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan?ref=page') }}"
                    data-action-method="POST" data-action-ajax-class=""
                    data-action-ajax-loading-target="customer-td-container">@lang('lang.create_subscription')</a>
                @endif


                <!--update password-->
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-modal-title="@lang('lang.update_password')"
                    data-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/update-password?ref=list') }}"
                    data-action-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/update-password?ref=list') }}"
                    data-loading-target="commonModalBody" data-action-method="POST">
                    @lang('lang.update_password')</a>

                <!--sync customers crm with subscription account details/settings (needed is there is a mismatch)-->
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-modal-title="@lang('lang.sync_account')"
                    data-action-ajax-class="ajax-request"
                    data-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/sync-account?ref=list') }}"
                    data-action-url="{{ urlResource('/app-admin/customers/'.$customer->tenant_id.'/sync-account?ref=list') }}"
                    data-loading-target="commonModalBody" data-action-method="POST">
                    @lang('lang.sync_account')</a>

                <!--cancel subscription-->
                <a class="dropdown-item confirm-action-danger" data-confirm-title="@lang('lang.delete_account')"
                    data-confirm-text="@lang('lang.are_you_sure')" data-ajax-type="DELETE"
                    data-url="{{ url('/app-admin/customers/'.$customer->tenant_id.'?source=page') }}">
                    @lang('lang.delete_account')
                </a>

            </div>
        </div>
    </div>
</div>

<!--hidden actions-->
@php $page['list_page_container_class'] = 'hidden' ; @endphp

<!--payments-->
@include('landlord.payments.actions.page-actions')