<!--each row-->
<tr id="ticket_<?php echo e($ticket->ticket_id); ?>" class="<?php echo e($ticket->pinned_status ?? ''); ?>">
    <?php if(config('visibility.tickets_col_checkboxes')): ?>
    <td class="tickets_col_checkbox checkitem" id="tickets_col_checkbox_<?php echo e($ticket->ticket_id); ?>">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-tickets-<?php echo e($ticket->ticket_id); ?>"
                name="ids[<?php echo e($ticket->ticket_id); ?>]"
                class="listcheckbox listcheckbox-tickets filled-in chk-col-light-blue"
                data-actions-container-class="tickets-checkbox-actions-container">
            <label for="listcheckbox-tickets-<?php echo e($ticket->ticket_id); ?>"></label>
        </span>
    </td>
    <?php endif; ?>
    <?php if(config('visibility.tickets_col_id')): ?>
    <td class="tickets_col_id"><a href="/tickets/<?php echo e($ticket->ticket_id); ?>"><?php echo e($ticket->ticket_id); ?></a></td>
    <?php endif; ?>
    <td class="tickets_col_subject">
        <a href="/tickets/<?php echo e($ticket->ticket_id); ?>"><?php echo e(str_limit($ticket->ticket_subject ?? '---', 35)); ?></a>
    </td>
    <td class="tickets_col_user">
        <?php if(config('visibility.show_contact_profile')): ?>
        <a href="javascript:void(0);" class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form user_profile_name_<?php echo e($ticket->id); ?>"
            data-toggle="modal" data-target="#commonModal" data-url="<?php echo e(url('contacts/'.$ticket->id)); ?>"
            data-loading-target="commonModalBody" data-modal-title="" data-modal-size="modal-md"
            data-header-close-icon="hidden" data-header-extra-close-icon="visible" data-footer-visibility="hidden"
            data-action-ajax-loading-target="commonModalBody"><?php echo e($ticket->first_name ?? '---'); ?>

            <?php echo e($ticket->last_name ?? ''); ?>

        </a>
        <?php else: ?>
        <span><?php echo e($ticket->first_name ?? '---'); ?> <?php echo e($ticket->last_name ?? ''); ?></span>
        <?php endif; ?>

    </td>
    <?php if(config('visibility.tickets_col_client')): ?>
    <td class="tickets_col_client">
        <?php echo e(str_limit($ticket->client_company_name ?? '---', 15)); ?>

    </td>
    <?php endif; ?>
    <?php if(config('visibility.tickets_col_department')): ?>
    <td class="tickets_col_department">
        <?php echo e(str_limit($ticket->category_name ?? '---', 30)); ?>

    </td>
    <?php endif; ?>
    <td class="tickets_col_date">
        <?php echo e(runtimeDate($ticket->ticket_created)); ?>

    </td>
    <td class="tickets_col_priority">
        <span class="label <?php echo e(runtimeTicketPriorityColors($ticket->ticket_priority, 'label')); ?>"><?php echo e(runtimeLang($ticket->ticket_priority)); ?></span>
    </td>
    <?php if(config('visibility.tickets_col_activity')): ?>
    <td class="tickets_col_activity">
        <?php echo e(runtimeDateAgo($ticket->ticket_last_updated)); ?>

    </td>
    <?php endif; ?>
    <td class="tickets_col_status">
        <span class="label label-outline-<?php echo e($ticket->ticketstatus_color); ?>"><?php echo e(runtimeLang($ticket->ticketstatus_title)); ?></span>

        <!--archived-->
        <?php if($ticket->ticket_active_state == 'archived' && runtimeArchivingOptions()): ?>
        <span class="label label-icons label-icons-default" data-toggle="tooltip" data-placement="top"
            title="<?php echo app('translator')->get('lang.archived'); ?>"><i class="ti-archive"></i></span>
        <?php endif; ?>
    </td>
    <?php if(config('visibility.tickets_col_action')): ?>
    <td class="tickets_col_action actions_column">
        <!--action button-->
        <span class="list-table-action font-size-inherit">
            <!--delete-->
            <?php if(config('visibility.action_buttons_delete')): ?>
            <button type="button" title="<?php echo e(cleanLang(__('lang.delete'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="<?php echo e(cleanLang(__('lang.delete_item'))); ?>"
                data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>" data-ajax-type="DELETE"
                data-url="<?php echo e(url('/')); ?>/tickets/<?php echo e($ticket->ticket_id); ?>">
                <i class="sl-icon-trash"></i>
            </button>
            <?php endif; ?>
            <!--edit-->
            <?php if(config('visibility.action_buttons_edit')): ?>
            <button type="button" title="<?php echo e(cleanLang(__('lang.edit'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="<?php echo e(urlResource('/tickets/'.$ticket->ticket_id.'/edit?edit_type=all&edit_source=list')); ?>"
                data-loading-target="commonModalBody" data-modal-title="<?php echo e(cleanLang(__('lang.edit_ticket'))); ?>"
                data-action-url="<?php echo e(urlResource('/tickets/'.$ticket->ticket_id)); ?>" data-action-method="PUT"
                data-action-ajax-class="js-ajax-ux-request" data-action-ajax-loading-target="tickets-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <?php endif; ?>
            <a href="/tickets/<?php echo e($ticket->ticket_id); ?>" title="<?php echo e(cleanLang(__('lang.view'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm">
                <i class="ti-new-window"></i>
            </a>
            <!--pin-->
            <span class="list-table-action">
                <a href="javascript:void(0);" title="<?php echo e(cleanLang(__('lang.pinning'))); ?>"
                    data-parent="ticket_<?php echo e($ticket->ticket_id); ?>"
                    data-url="<?php echo e(url('/tickets/'.$ticket->ticket_id.'/pinning')); ?>"
                    class="data-toggle-action-tooltip btn btn-outline-default-light btn-circle btn-sm opacity-4 js-toggle-pinning">
                    <i class="ti-pin2"></i>
                </a>
            </span>
        </span>
        <!--action button-->
    </td>
    <?php endif; ?>
</tr><?php /**PATH D:\my_apache\application\resources\views/pages/tickets/components/table/ajax-inc.blade.php ENDPATH**/ ?>