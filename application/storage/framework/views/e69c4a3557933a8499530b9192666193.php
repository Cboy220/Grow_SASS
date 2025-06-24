<!--each reply-->
<?php $__currentLoopData = $replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!--do not show [notes] to client users-->
<?php if((auth()->user()->is_client && $reply->ticketreply_type == 'reply') || auth()->user()->is_team): ?>
<div class="comment-widgets ticket_reply_type_<?php echo e($reply->ticketreply_type); ?>"
    id="ticket_reply_<?php echo e($reply->ticketreply_id); ?>">
    <div class="d-flex flex-row comment-rowp-b-0">
        <div class="p-2"><span class="round"><img
                    src="<?php echo e(getUsersAvatar($reply->avatar_directory, $reply->avatar_filename)); ?>" width="50"></span>
        </div>
        <div class="comment-text w-100">

            <!--note icon-->
            <span class="label label-default label-sm hidden ticket_reply_note_icon">ticket note</span>

            <h5 class="m-b-0">

                <?php if(config('visibility.show_contact_profile')): ?>
                <a href="javascript:void(0);"
                    class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form user_profile_name_<?php echo e($reply->id); ?>"
                    data-toggle="modal" data-target="#commonModal" data-url="<?php echo e(url('contacts/'.$reply->id)); ?>"
                    data-loading-target="commonModalBody" data-modal-title="" data-modal-size="modal-md"
                    data-header-close-icon="hidden" data-header-extra-close-icon="visible"
                    data-footer-visibility="hidden"
                    data-action-ajax-loading-target="commonModalBody"><?php echo e($reply->first_name ?? runtimeUnkownUser()); ?>

                    <?php echo e($reply->last_name ?? ''); ?>

                </a>
                <?php else: ?>
                <?php echo e($reply->first_name ?? runtimeUnkownUser()); ?> <?php echo e($reply->last_name ?? ''); ?>

                <?php endif; ?>

            </h5>

            <div class="text-muted m-b-5"><small class="text-muted">
                    <?php echo e(runtimeDate($reply->ticketreply_created)); ?> -
                    (<?php echo e(runtimeDateAgo($reply->ticketreply_created)); ?>)</small>

            </div>

            <div id="ticket_reply_text_<?php echo e($reply->ticketreply_id); ?>">
                <?php echo $reply->ticketreply_text; ?>

            </div>

            <div id="ticket_edit_reply_container_<?php echo e($reply->ticketreply_id); ?>">
                <!--dynamic content-->
            </div>

            <!--action buttons [edit & delete]-->
            <?php if(permissionEditTicketReply($reply)): ?>
            <div class="text-right">
                <!--edit reply-->
                <small><a class="text-muted ajax-request"
                        data-loading-target="ticket_reply_text_<?php echo e($reply->ticketreply_id); ?>" href="javascript:void(0);"
                        data-url="<?php echo e(url('tickets/'.$reply->ticketreply_id.'/edit-reply')); ?>"><?php echo app('translator')->get('lang.edit'); ?></a></small>
                |
                <!--delete reply-->
                <small><a class="text-muted confirm-action-danger" href="javascript:void(0);"
                        data-confirm-title="<?php echo app('translator')->get('lang.delete_reply'); ?>" data-confirm-text="<?php echo app('translator')->get('lang.are_you_sure'); ?>"
                        data-ajax-type="DELETE"
                        data-url="<?php echo e(url('tickets/'.$reply->ticketreply_id.'/delete-reply')); ?>"><?php echo app('translator')->get('lang.delete'); ?></a></small>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!--ticket attachements-->
    <?php if($reply->attachments_count > 0): ?>
    <div class="x-attachements">
        <!--attachments container-->
        <div class="row">
            <!--attachments-->
            <?php $__currentLoopData = $reply->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('pages.ticket.components.attachments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/ticket/components/replies.blade.php ENDPATH**/ ?>