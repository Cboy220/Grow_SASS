<div class="row">
    <!--options panel-->
    <?php echo $__env->make('pages.ticket.components.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <div class="col-sm-12 col-lg-9">

        <!--body-->
        <div class="card-body card x-message p-t-0" id="ticket-body">
            <!--message-->
            <div class="x-body">
                <div class="d-flex m-b-20">
                    <div>
                        <img src="<?php echo e(getUsersAvatar($ticket->avatar_directory, $ticket->avatar_filename)); ?>" alt="user"
                            width="40" class="img-circle" />
                    </div>
                    <div class="p-l-10">
                        <h5 class="m-b-0">
                            <?php if(config('visibility.show_contact_profile')): ?>
                            <a href="javascript:void(0);"
                                class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form user_profile_name_<?php echo e($ticket->id); ?>"
                                data-toggle="modal" data-target="#commonModal"
                                data-url="<?php echo e(url('contacts/'.$ticket->id)); ?>" data-loading-target="commonModalBody"
                                data-modal-title="" data-modal-size="modal-md" data-header-close-icon="hidden"
                                data-header-extra-close-icon="visible" data-footer-visibility="hidden"
                                data-action-ajax-loading-target="commonModalBody"><?php echo e($ticket->first_name ?? runtimeUnkownUser()); ?>

                                <?php echo e($ticket->last_name ?? ''); ?>

                            </a>
                            <?php else: ?>
                            <?php echo e($ticket->first_name ?? runtimeUnkownUser()); ?> <?php echo e($ticket->last_name ?? ''); ?>

                            <?php endif; ?>
                        </h5>
                        <small class="text-muted"><?php echo e(runtimeDateAgo($ticket->ticket_created )); ?></small>
                    </div>
                </div>

                <?php echo clean($ticket->ticket_message); ?>


            </div>
            <!--ticket attachements-->
            <?php if($ticket->attachments_count > 0): ?>
            <div class="x-attachements">
                <!--attachments container-->
                <div class="row">
                    <!--attachments-->
                    <?php $__currentLoopData = $ticket->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('pages.ticket.components.attachments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>


        <!--replies-->
        <div id="ticket-replies-container">
            <?php echo $__env->make('pages.ticket.components.replies', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!--reply notice-->
        <?php if(config('visibility.ticket_replying_on_hold')): ?>
        <div class="p-b-40">
            <div class="alert alert-danger" id="ticket_reply_onhold_notice">
                <?php echo e(cleanLang(__('lang.ticket_is_on_hold'))); ?></div>
        </div>
        <?php endif; ?>

        <!--reply button (popup)-->
        <?php if(config('visibility.ticket_replying') && config('system.settings2_tickets_replying_interface') == 'popup'): ?>
        <div class="p-b-40">
            <div class="x-reply text-center" id="ticket_reply_button">
                <button type="button" class="btn btn-rounded-x btn-info edit-add-modal-button js-ajax-ux-request"
                    data-toggle="modal" data-url="<?php echo e(urlResource('/tickets/'.$ticket->ticket_id.'/reply')); ?>"
                    data-action-url="<?php echo e(urlResource('/tickets/'.$ticket->ticket_id.'/postreply')); ?>"
                    data-target="#commonModal" data-loading-target="commonModalBody" data-action-method="POST"
                    data-modal-title="<?php echo e(cleanLang(__('lang.reply_ticket'))); ?>">
                    <?php echo e(cleanLang(__('lang.reply_ticket'))); ?></button>
            </div>
        </div>
        <?php endif; ?>


        <!--reply button (inline)-->
        <?php if(config('visibility.ticket_replying') && config('system.settings2_tickets_replying_interface') == 'inline'): ?>
        <div class="p-b-40" id="ticket_replay_button_inline_container">
            <div class="x-reply text-center">
                <button type="button" class="btn btn-rounded-x btn-default" id="ticket_replay_button_inline" data-reply-type="note">
                    <?php echo e(cleanLang(__('lang.add_a_note'))); ?></button>
                <button type="button" class="btn btn-rounded-x btn-info" id="ticket_replay_button_inline" data-reply-type="reply">
                    <?php echo e(cleanLang(__('lang.reply_ticket'))); ?></button>
            </div>
        </div>
        <?php endif; ?>

        <!--replying container-->
        <?php if(config('visibility.ticket_replying') && config('system.settings2_tickets_replying_interface') == 'inline'): ?>
        <div id="ticket_reply_inline_form" class="hidden">

            <div class="alert alert-warning hidden" id="ticketreply_type_info"><?php echo app('translator')->get('lang.ticket_notes_info'); ?></div>

            <?php echo $__env->make('pages.ticket.components.modals.reply', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!--form buttons-->
            <div class="text-right p-t-30">
                <button type="button" class="btn btn-danger waves-effect text-left" id="ticket_reply_button_close"
                    data-dismiss="modal"><?php echo app('translator')->get('lang.cancel'); ?></button>
                <button type="submit" id="ticket_reply_button_submit"
                    class="btn btn-info waves-effect text-left js-ajax-ux-request"
                    data-lang-submit="<?php echo app('translator')->get('lang.submit'); ?>"
                    data-lang-save-note="<?php echo app('translator')->get('lang.save_ticket_note'); ?>"
                    data-url="<?php echo e(url('tickets/'.$ticket->ticket_id.'/postreply?view=inline')); ?>" data-type="form"
                    data-form-id="ticket_reply_inline_form" data-ajax-type="post" data-loading-target="main-body"
                    data-on-start-submit-button="disable"><?php echo app('translator')->get('lang.submit'); ?></button>
            </div>
        </div>
        <?php endif; ?>


    </div>

</div><?php /**PATH D:\my_apache\application\resources\views/pages/ticket/components/body.blade.php ENDPATH**/ ?>