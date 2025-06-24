<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-3 align-self-center text-right parent-page-actions p-b-9"
        id="list-page-actions-container">
        <div id="list-page-actions">
                <!--edit (nb: the second condition is needed for timeline [right actions nav] replacement-->
                <?php if(config('visibility.action_buttons_edit')): ?>
                <!--reminder-->
                <?php if(config('visibility.modules.reminders')): ?>
                <button type="button" data-toggle="tooltip" title="<?php echo e(cleanLang(__('lang.reminder'))); ?>"
                        id="reminders-panel-toggle-button"
                        class="reminder-toggle-panel-button list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-reminder-panel ajax-request <?php echo e($ticket->reminder_status); ?>"
                        data-url="<?php echo e(url('reminders/start?resource_type=ticket&resource_id='.$ticket->ticket_id)); ?>"
                        data-loading-target="reminders-side-panel-body" data-progress-bar='hidden'
                        data-target="reminders-side-panel" data-title="<?php echo app('translator')->get('lang.my_reminder'); ?>">
                        <i class="ti-alarm-clock"></i>
                </button>
                <?php endif; ?>
                <span class="dropdown">
                        <button type="button"
                                class="data-toggle-tooltip list-actions-button btn btn-page-actions waves-effect waves-dark edit-add-modal-button js-ajax-ux-request"
                                data-toggle="modal"
                                data-url="/tickets/<?php echo e($ticket->ticket_id); ?>/edit?edit_type=all&edit_source=leftpanel"
                                data-action-url="/tickets/<?php echo e($ticket->ticket_id); ?>" data-target="#commonModal"
                                data-loading-target="commonModalBody" data-action-method="PUT"
                                data-modal-title="<?php echo e(cleanLang(__('lang.edit_ticket'))); ?>">
                                <i class="sl-icon-note"></i>
                        </button>
                </span>
                <?php endif; ?>

                <div class="btn-group" id="list_actions_sort_kanban">
                        <button type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                class="list-actions-button btn waves-effect waves-dark">
                                <i class="sl-icon-settings"></i></button>
                        <div class="dropdown-menu dropdown-menu-right fx-kaban-sorting-dropdown">
                                <!--archive-->
                                <?php if($ticket->ticket_active_state == 'active'): ?>
                                <a class="dropdown-item js-ajax-ux-request" href="javascript:void(0)"
                                        data-url="<?php echo e(url('/tickets/archive?ref=page&id='.$ticket->ticket_id)); ?>"><?php echo app('translator')->get('lang.archive'); ?></a>
                                <?php endif; ?>

                                <!--restore-->
                                <?php if($ticket->ticket_active_state == 'archived'): ?>
                                <a class="dropdown-item js-ajax-ux-request" href="javascript:void(0)"
                                        data-url="<?php echo e(url('/tickets/restore?ref=page&id='.$ticket->ticket_id)); ?>"><?php echo app('translator')->get('lang.restore'); ?></a>
                                <?php endif; ?>

                                <?php if(config('visibility.action_buttons_edit')): ?>
                                <a type="button" class="dropdown-item js-ajax-ux-request edit-add-modal-button "
                                        data-toggle="modal"
                                        data-url="/tickets/<?php echo e($ticket->ticket_id); ?>/edit-tags"
                                        data-action-url="/tickets/<?php echo e($ticket->ticket_id.'/edit-tags?action=redirect'); ?>"
                                        data-target="#commonModal" data-loading-target="commonModalBody"
                                        data-close-button-visibility="hidden" data-action-method="post"
                                        data-action-ajax-class="ajax-request"
                                        data-modal-title="<?php echo app('translator')->get('lang.edit_tags'); ?>">
                                        <?php echo app('translator')->get('lang.edit_tags'); ?>
                                </a>
                                <?php endif; ?>

                        </div>
                </div>


                <?php if(auth()->user()->role->role_tickets >= 3): ?>
                <!--delete-->
                <button type="button" data-toggle="tooltip" title="<?php echo e(cleanLang(__('lang.delete_ticket'))); ?>"
                        class="list-actions-button btn btn-page-actions waves-effect waves-dark confirm-action-danger"
                        data-confirm-title="<?php echo e(cleanLang(__('lang.delete_ticket'))); ?>"
                        data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>" data-ajax-type="DELETE"
                        data-url="<?php echo e(url('/tickets/'.$ticket->ticket_id.'?source=page')); ?>"><i
                                class="sl-icon-trash"></i></button>
                <?php endif; ?>

                <?php if(auth()->user()->is_client): ?>
                <!--reminder-->
                <?php if(config('visibility.modules.reminders')): ?>
                <button type="button" data-toggle="tooltip" title="<?php echo e(cleanLang(__('lang.reminder'))); ?>"
                        id="reminders-panel-toggle-button"
                        class="reminder-toggle-panel-button list-actions-button btn btn-page-actions waves-effect waves-dark js-toggle-reminder-panel ajax-request <?php echo e($ticket->reminder_status); ?>"
                        data-url="<?php echo e(url('reminders/start?resource_type=ticket&resource_id='.$ticket->ticket_id)); ?>"
                        data-loading-target="reminders-side-panel-body" data-progress-bar='hidden'
                        data-target="reminders-side-panel" data-title="<?php echo app('translator')->get('lang.my_reminder'); ?>">
                        <i class="ti-alarm-clock"></i>
                </button>
                <?php endif; ?>
                <?php endif; ?>
        </div>
</div><?php /**PATH D:\my_apache\application\resources\views/pages/ticket/components/misc/actions.blade.php ENDPATH**/ ?>