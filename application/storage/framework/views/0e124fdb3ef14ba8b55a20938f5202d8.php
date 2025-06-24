<?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!--each row-->
<tr id="invoice_<?php echo e($invoice->bill_invoiceid); ?>" class="<?php echo e($invoice->pinned_status ?? ''); ?>">
    <?php if(config('visibility.invoices_col_checkboxes')): ?>
    <td class="invoices_col_checkbox checkitem" id="invoices_col_checkbox_<?php echo e($invoice->bill_invoiceid); ?>">
        <!--list checkbo-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-invoices-<?php echo e($invoice->bill_invoiceid); ?>"
                name="ids[<?php echo e($invoice->bill_invoiceid); ?>]"
                class="listcheckbox listcheckbox-invoices filled-in chk-col-light-blue"
                data-actions-container-class="invoices-checkbox-actions-container">
            <label for="listcheckbox-invoices-<?php echo e($invoice->bill_invoiceid); ?>"></label>
        </span>
    </td>
    <?php endif; ?>

    <!--tableconfig_column_1 [id]-->
    <td class="invoices_col_tableconfig_column_1 <?php echo e(config('table.tableconfig_column_1')); ?> tableconfig_column_1"
        id="invoices_col_id_<?php echo e($invoice->bill_invoiceid); ?>">
        <a href="/invoices/<?php echo e($invoice->bill_invoiceid); ?>">
            <?php echo e($invoice->formatted_bill_invoiceid); ?> </a>
        <!--recurring-->
        <?php if(auth()->user()->is_team && $invoice->bill_recurring == 'yes'): ?>
        <span class="sl-icon-refresh text-danger p-l-5" data-toggle="tooltip"
            title="<?php echo app('translator')->get('lang.recurring_invoice'); ?>"></span>
        <?php endif; ?>
        <!--child invoice-->
        <?php if(auth()->user()->is_team && $invoice->bill_recurring_child == 'yes'): ?>
        <a href="<?php echo e(url('invoices/'.$invoice->bill_recurring_parent_id)); ?>">
            <i class="ti-back-right text-success p-l-5" data-toggle="tooltip" data-html="true"
                title="<?php echo e(cleanLang(__('lang.invoice_automatically_created_from_recurring'))); ?> <br>(#<?php echo e(runtimeInvoiceIdFormat($invoice->bill_recurring_parent_id)); ?>)"></i>
        </a>
        <?php endif; ?>
    </td>


    <!--tableconfig_column_2 [parent id] -->
    <td class="invoices_col_tableconfig_column_2 <?php echo e(config('table.tableconfig_column_2')); ?> tableconfig_column_2"
        id="invoices_col_tableconfig_column_2<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e($invoice->bill_recurring_parent_id ?? '---'); ?>

    </td>


    <!--tableconfig_column_3 [date]-->
    <td class="invoices_col_tableconfig_column_3 <?php echo e(config('table.tableconfig_column_3')); ?> tableconfig_column_3"
        id="invoices_col_date_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e(runtimeDate($invoice->bill_date)); ?>

    </td>

    <!--tableconfig_column_4 [due]-->
    <td class="invoices_col_tableconfig_column_4 <?php echo e(config('table.tableconfig_column_4')); ?> tableconfig_column_4"
        id="invoices_col_tableconfig_column_4_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e(runtimeDate($invoice->bill_due_date ?? '')); ?>

    </td>

    <!--tableconfig_column_5 [company name]-->
    <td class="invoices_col_tableconfig_column_5 <?php echo e(config('table.tableconfig_column_5')); ?> tableconfig_column_5"
        id="invoices_col_company_<?php echo e($invoice->bill_invoiceid); ?>">
        <a href="/clients/<?php echo e($invoice->bill_clientid); ?>"><?php echo e(str_limit($invoice->client_company_name ?? '---', 22)); ?></a>
    </td>

    <!--tableconfig_column_6 [client contact] -->
    <td class="invoices_col_tableconfig_column_6 <?php echo e(config('table.tableconfig_column_6')); ?> tableconfig_column_6"
        id="invoices_col_tableconfig_column_6_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php if(isset($invoice->contact_name) && $invoice->contact_name != ''): ?>
        <a href="javascript:void(0);" class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
            data-toggle="modal" data-target="#commonModal" data-url="<?php echo e(url('contacts/'.$invoice->contact_id)); ?>"
            data-loading-target="commonModalBody" data-modal-title="" data-modal-size="modal-md"
            data-header-close-icon="hidden" data-header-extra-close-icon="visible" data-footer-visibility="hidden"
            data-action-ajax-loading-target="commonModalBody"><?php echo e($invoice->contact_name); ?>

        </a>
        <?php else: ?>
        <span>---</span>
        <?php endif; ?>
    </td>

    <!--tableconfig_column_7 [created by] -->
    <td class="invoices_col_tableconfig_column_7 <?php echo e(config('table.tableconfig_column_7')); ?> tableconfig_column_7"
        id="invoices_col_tableconfig_column_7_<?php echo e($invoice->bill_invoiceid); ?>">
        <span class="printing_hidden">
            <img src="<?php echo e(getUsersAvatar($invoice->avatar_directory, $invoice->avatar_filename, $invoice->bill_creatorid)); ?>"
                alt="user" class="img-circle avatar-xsmall printing_hidden">
            <span
                class="user-profile-first-name"><?php echo e(checkUsersName($invoice->first_name, $invoice->bill_creatorid)); ?></span>
        </span>

        <!--print view-->
        <span class="hidden printing_visible">
            <?php echo e($invoice->first_name ?? runtimeUnkownUser()); ?> <?php echo e($invoice->last_name ?? ''); ?>

        </span>
    </td>

    <!--tableconfig_column_8 [project] -->
    <td class="invoices_col_tableconfig_column_8 <?php echo e(config('table.tableconfig_column_8')); ?> tableconfig_column_8"
        id="invoices_col_tableconfig_column_8_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e($invoice->bill_projectid ?? '---'); ?>

    </td>

    <!--tableconfig_column_9 [project_title] -->
    <td class="invoices_col_tableconfig_column_9 <?php echo e(config('table.tableconfig_column_9')); ?> tableconfig_column_9"
        id="invoices_col_project_<?php echo e($invoice->bill_invoiceid); ?>">
        <a href="/projects/<?php echo e($invoice->bill_projectid); ?>"><?php echo e(str_limit($invoice->project_title ?? '---', 20)); ?></a>
    </td>

    <!--tableconfig_column_10 [tax] -->
    <td class="invoices_col_tableconfig_column_10 <?php echo e(config('table.tableconfig_column_10')); ?> tableconfig_column_10"
        id="invoices_col_tableconfig_column_10_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e(runtimeMoneyFormat($invoice->bill_tax_total_amount)); ?>

    </td>

    <!--tableconfig_column_11 [discount type] -->
    <td class="invoices_col_tableconfig_column_11 <?php echo e(config('table.tableconfig_column_11')); ?> tableconfig_column_11"
        id="invoices_col_tableconfig_column_11_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php if($invoice->bill_discount_type == 'fixed'): ?>
        <?php echo app('translator')->get('lang.fixed_amount'); ?>
        <?php endif; ?>
        <?php if($invoice->bill_discount_type == 'none'): ?>
        ---
        <?php endif; ?>
        <?php if($invoice->bill_discount_type == 'percentage'): ?>
        <?php echo e($invoice->bill_discount_percentage); ?>%
        <?php endif; ?>
    </td>

    <!--tableconfig_column_12 [discount amount] -->
    <td class="invoices_col_tableconfig_column_12 <?php echo e(config('table.tableconfig_column_12')); ?> tableconfig_column_12"
        id="invoices_col_tableconfig_column_12_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php if($invoice->bill_discount_amount == '0.00'): ?>
        ---
        <?php else: ?>
        <?php echo e(runtimeMoneyFormat($invoice->bill_discount_amount)); ?>

        <?php endif; ?>
    </td>

    <!--tableconfig_column_13 [last payment - date] -->
    <td class="invoices_col_tableconfig_column_13 <?php echo e(config('table.tableconfig_column_13')); ?> tableconfig_column_13"
        id="tableconfig_column_13_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e(runtimeDate($invoice->last_payment_date)); ?>

    </td>

    <!--tableconfig_column_14 [last payment - amount] -->
    <td class="invoices_col_tableconfig_column_14 <?php echo e(config('table.tableconfig_column_14')); ?> tableconfig_column_14"
        id="invoices_col_tableconfig_column_14_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php if($invoice->last_payment_amount == '0.00'): ?>
        ---
        <?php else: ?>
        <?php echo e(runtimeMoneyFormat($invoice->last_payment_amount)); ?>

        <?php endif; ?>
    </td>



    <!--tableconfig_column_15 [last payment - method] -->
    <td class="invoices_col_tableconfig_column_15 <?php echo e(config('table.tableconfig_column_15')); ?> tableconfig_column_15 ucw"
        id="invoices_col_tableconfig_column_15_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e($invoice->last_payment_method ?? '---'); ?>

    </td>

    <!--tableconfig_column_16 [last payment - transaction id] -->
    <td class="invoices_col_tableconfig_column_16 <?php echo e(config('table.tableconfig_column_16')); ?> tableconfig_column_16"
        id="invoices_col_tableconfig_column_16_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e($invoice->last_payment_transaction_id ?? '---'); ?>

    </td>

    <!--tableconfig_column_17 [attachments] -->
    <td class="invoices_col_tableconfig_column_17 <?php echo e(config('table.tableconfig_column_17')); ?> tableconfig_column_17"
        id="invoices_col_tableconfig_column_17_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php if($invoice->count_attachments == 0): ?>
        ---
        <?php else: ?>
        <?php echo e($invoice->count_attachments); ?>

        <?php endif; ?>
    </td>

    <!--tableconfig_column_19 [scheduled publishing date] -->
    <td class="invoices_col_tableconfig_column_19 <?php echo e(config('table.tableconfig_column_19')); ?> tableconfig_column_19"
        id="invoices_col_tableconfig_column_19_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e(runtimeDate($invoice->bill_publishing_scheduled_date ?? '')); ?>

    </td>


    <!--tableconfig_column_20 [payments]-->
    <td class="invoices_col_tableconfig_column_20 <?php echo e(config('table.tableconfig_column_20')); ?> tableconfig_column_20"
        id="invoices_col_tableconfig_column_20_<?php echo e($invoice->bill_invoiceid); ?>">
        <a
            href="<?php echo e(url('payments?filter_payment_invoiceid='.$invoice->bill_invoiceid)); ?>"><?php echo e(runtimeMoneyFormat($invoice->sum_payments)); ?></a>
    </td>

    <!--tableconfig_column_21 [amount]-->
    <td class="invoices_col_tableconfig_column_21 <?php echo e(config('table.tableconfig_column_21')); ?> tableconfig_column_21"
        id="invoices_col_amount_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e(runtimeMoneyFormat($invoice->bill_final_amount)); ?></td>


    <!--tableconfig_column_22 [balance]-->
    <td class="invoices_col_tableconfig_column_22 <?php echo e(config('table.tableconfig_column_22')); ?> tableconfig_column_22"
        id="invoices_col_balance_<?php echo e($invoice->bill_invoiceid); ?>">
        <?php echo e(runtimeMoneyFormat($invoice->invoice_balance)); ?>

    </td>

    <!--tableconfig_column_23 [status]-->
    <td class="invoices_col_tableconfig_column_23 <?php echo e(config('table.tableconfig_column_23')); ?> tableconfig_column_23"
        id="invoices_col_status_<?php echo e($invoice->bill_invoiceid); ?>">

        <span class="label <?php echo e(runtimeInvoiceStatusColors($invoice->bill_status, 'label')); ?>"><?php echo e(runtimeLang($invoice->bill_status)); ?></span>

        <!--invoice is scheduled-->
        <?php if($invoice->bill_publishing_type == 'scheduled' && $invoice->bill_publishing_scheduled_status == 'pending'): ?>
        <span class="label label-icons label-icons-warning" data-toggle="tooltip" data-placement="top"
            title="<?php echo app('translator')->get('lang.scheduled_publishing_info'); ?>: <?php echo e(runtimeDate($invoice->bill_publishing_scheduled_date)); ?>"><i
                class="sl-icon-clock"></i></span>
        <?php endif; ?>

        <?php if(config('system.settings_estimates_show_view_status') == 'yes' && auth()->user()->is_team &&
        $invoice->bill_status != 'draft' && $invoice->bill_status != 'paid'): ?>
        <!--estimate not viewed-->
        <?php if($invoice->bill_viewed_by_client == 'no'): ?>
        <span class="label label-icons label-icons-default" data-toggle="tooltip" data-placement="top"
            title="<?php echo app('translator')->get('lang.client_has_not_opened'); ?>"><i class="sl-icon-eye"></i></span>
        <?php endif; ?>
        <!--estimate viewed-->
        <?php if($invoice->bill_viewed_by_client == 'yes'): ?>
        <span class="label label-icons label-icons-info" data-toggle="tooltip" data-placement="top"
            title="<?php echo app('translator')->get('lang.client_has_opened'); ?>"><i class="sl-icon-eye"></i></span>
        <?php endif; ?>
        <?php endif; ?>

    </td>

    <!--actions-->
    <td class="invoices_col_action actions_column" id="invoices_col_action_<?php echo e($invoice->bill_invoiceid); ?>">
        <!--action button-->
        <span class="list-table-action font-size-inherit">

            <!--client options-->
            <?php if(auth()->user()->is_client): ?>
            <a title="<?php echo e(cleanLang(__('lang.download'))); ?>" title="<?php echo e(cleanLang(__('lang.download'))); ?>"
                class="data-toggle-tooltip data-toggle-tooltip btn btn-outline-warning btn-circle btn-sm"
                href="<?php echo e(url('/invoices/'.$invoice->bill_invoiceid.'/pdf')); ?>" download>
                <i class="ti-import"></i></a>
            <?php endif; ?>
            <!--delete-->
            <?php if(config('visibility.action_buttons_delete')): ?>
            <button type="button" title="<?php echo e(cleanLang(__('lang.delete'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="<?php echo e(cleanLang(__('lang.delete_invoice'))); ?>"
                data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>" data-ajax-type="DELETE"
                data-url="<?php echo e(url('/')); ?>/invoices/<?php echo e($invoice->bill_invoiceid); ?>">
                <i class="sl-icon-trash"></i>
            </button>
            <?php endif; ?>
            <!--edit-->
            <?php if(config('visibility.action_buttons_edit')): ?>
            <a href="/invoices/<?php echo e($invoice->bill_invoiceid); ?>/edit-invoice" title="<?php echo e(cleanLang(__('lang.edit'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="sl-icon-note"></i>
            </a>
            <?php endif; ?>
            <a href="/invoices/<?php echo e($invoice->bill_invoiceid); ?>" title="<?php echo e(cleanLang(__('lang.view'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>

            <!--more button (team)-->
            <?php if(auth()->user()->is_team): ?>
            <span class="list-table-action dropdown font-size-inherit">
                <button type="button" id="listTableAction" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" title="<?php echo e(cleanLang(__('lang.more'))); ?>"
                    class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm">
                    <i class="ti-more"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="listTableAction">
                    <?php if(config('visibility.action_buttons_edit')): ?>
                    <!--quick edit-->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                        data-toggle="modal" data-target="#commonModal"
                        data-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'/edit')); ?>"
                        data-loading-target="commonModalBody"
                        data-modal-title="<?php echo e(cleanLang(__('lang.edit_invoice'))); ?>"
                        data-action-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'?ref=list')); ?>"
                        data-action-method="PUT" data-action-ajax-class=""
                        data-action-ajax-loading-target="invoices-td-container">
                        <?php echo e(cleanLang(__('lang.quick_edit'))); ?>

                    </a>
                    <!--email to client-->
                    <?php if(auth()->user()->role->role_invoices > 1): ?>
                    <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                        data-confirm-title="<?php echo e(cleanLang(__('lang.email_to_client'))); ?>"
                        data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>"
                        data-url="<?php echo e(url('/invoices')); ?>/<?php echo e($invoice->bill_invoiceid); ?>/resend?ref=list">
                        <?php echo e(cleanLang(__('lang.email_to_client'))); ?></a>
                    <?php endif; ?>
                    <!--overdue invoice reminder-->
                    <?php if(auth()->user()->role->role_invoices > 1 && $invoice->bill_status == 'overdue'): ?>
                    <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                        data-confirm-title="<?php echo e(cleanLang(__('lang.send_overdue_reminder_info'))); ?>"
                        data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>"
                        data-url="<?php echo e(url('/invoices')); ?>/<?php echo e($invoice->bill_invoiceid); ?>/overdue-reminder?ref=list">
                        <?php echo e(cleanLang(__('lang.send_overdue_reminder'))); ?></a>
                    <?php endif; ?>
                    <!--add payment-->
                    <?php if(auth()->user()->role->role_invoices > 1): ?>
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="<?php echo e(cleanLang(__('lang.add_new_payment'))); ?>"
                        data-url="<?php echo e(url('/payments/create?bill_invoiceid='.$invoice->bill_invoiceid)); ?>"
                        data-action-url="<?php echo e(urlResource('/payments?ref=invoice&source=list&bill_invoiceid='.$invoice->bill_invoiceid)); ?>"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        <?php echo e(cleanLang(__('lang.add_new_payment'))); ?></a>
                    <?php endif; ?>
                    <!--clone invoice-->
                    <?php if(auth()->user()->role->role_invoices > 1): ?>
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-modal-title="<?php echo e(cleanLang(__('lang.clone_invoice'))); ?>"
                        data-url="<?php echo e(url('/invoices/'.$invoice->bill_invoiceid.'/clone')); ?>"
                        data-action-url="<?php echo e(url('/invoices/'.$invoice->bill_invoiceid.'/clone')); ?>"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        <?php echo e(cleanLang(__('lang.clone_invoice'))); ?></a>
                    <?php endif; ?>
                    <!--change category-->
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title="<?php echo e(cleanLang(__('lang.change_category'))); ?>"
                        data-url="<?php echo e(url('/invoices/change-category')); ?>"
                        data-action-url="<?php echo e(urlResource('/invoices/change-category?id='.$invoice->bill_invoiceid)); ?>"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        <?php echo e(cleanLang(__('lang.change_category'))); ?></a>
                    <!--attach project -->
                    <?php if(!is_numeric($invoice->bill_projectid)): ?>
                    <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#actionsModal"
                        data-modal-title=" <?php echo e(cleanLang(__('lang.attach_to_project'))); ?>"
                        data-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'/attach-project?client_id='.$invoice->bill_clientid)); ?>"
                        data-action-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'/attach-project')); ?>"
                        data-loading-target="actionsModalBody" data-action-method="POST">
                        <?php echo e(cleanLang(__('lang.attach_to_project'))); ?></a>
                    <?php endif; ?>
                    <!--dettach project -->
                    <?php if(is_numeric($invoice->bill_projectid)): ?>
                    <a class="dropdown-item confirm-action-danger" href="javascript:void(0)"
                        data-confirm-title="<?php echo e(cleanLang(__('lang.detach_from_project'))); ?>"
                        data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>"
                        data-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'/detach-project')); ?>">
                        <?php echo e(cleanLang(__('lang.detach_from_project'))); ?></a>
                    <?php endif; ?>
                    <!--recurring settings-->
                    <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                        href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                        data-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'/recurring-settings?source=page')); ?>"
                        data-loading-target="commonModalBody"
                        data-modal-title="<?php echo e(cleanLang(__('lang.recurring_settings'))); ?>"
                        data-action-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'/recurring-settings?source=page')); ?>"
                        data-action-method="POST"
                        data-action-ajax-loading-target="invoices-td-container"><?php echo e(cleanLang(__('lang.recurring_settings'))); ?></a>
                    <!--stop recurring -->
                    <?php if($invoice->bill_recurring == 'yes'): ?>
                    <a class="dropdown-item confirm-action-info" href="javascript:void(0)"
                        data-confirm-title="<?php echo e(cleanLang(__('lang.stop_recurring'))); ?>"
                        data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>"
                        data-url="<?php echo e(urlResource('/invoices/'.$invoice->bill_invoiceid.'/stop-recurring')); ?>">
                        <?php echo e(cleanLang(__('lang.stop_recurring'))); ?></a>
                    <?php endif; ?>
                    <?php endif; ?>
                    <a class="dropdown-item"
                        href="<?php echo e(url('payments?filter_payment_invoiceid='.$invoice->bill_invoiceid)); ?>">
                        <?php echo e(cleanLang(__('lang.view_payments'))); ?></a>
                    <a class="dropdown-item" href="<?php echo e(url('/invoices/'.$invoice->bill_invoiceid.'/pdf')); ?>" download>
                        <?php echo e(cleanLang(__('lang.download'))); ?></a>
                </div>
            </span>
            <?php endif; ?>
            <!--more button-->

            <!--pin-->
            <span class="list-table-action">
                <a href="javascript:void(0);" title="<?php echo e(cleanLang(__('lang.pinning'))); ?>"
                    data-parent="invoice_<?php echo e($invoice->bill_invoiceid); ?>"
                    data-url="<?php echo e(url('/invoices/'.$invoice->bill_invoiceid.'/pinning')); ?>"
                    class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm opacity-4 js-toggle-pinning">
                    <i class="ti-pin2"></i>
                </a>
            </span>
        </span>
        <!--action button-->

    </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<!--each row--><?php /**PATH D:\my_apache\application\resources\views/pages/invoices/components/table/ajax.blade.php ENDPATH**/ ?>