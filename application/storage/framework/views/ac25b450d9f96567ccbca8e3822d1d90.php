<?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<!--each row-->
<tr id="note_<?php echo e($note->note_id); ?>">
    <?php if(config('visibility.notes_col_checkboxes')): ?>
    <td class="notes_col_checkbox checkitem" id="notes_col_checkbox_<?php echo e($note->note_id); ?>">
        <!--list checkbox-->
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-notes-<?php echo e($note->note_id); ?>" name="ids[<?php echo e($note->note_id); ?>]"
                class="listcheckbox listcheckbox-notes filled-in chk-col-light-blue"
                data-actions-container-class="notes-checkbox-actions-container">
            <label for="listcheckbox-notes-<?php echo e($note->note_id); ?>"></label>
        </span>
    </td>
    <?php endif; ?>
    <td class="notes_col_added">
        <img src="<?php echo e(getUsersAvatar($note->avatar_directory, $note->avatar_filename)); ?>" alt="user"
            class="img-circle avatar-xsmall">
        <?php echo e($note->first_name ?? runtimeUnkownUser()); ?>

    </td>
    <td class="notes_col_title">
        <a href="javascript:void(0)" class="show-modal-button js-ajax-ux-request" data-toggle="modal"
            data-url="<?php echo e(url('/')); ?>/notes/<?php echo e($note->note_id); ?>" data-target="#plainModal"
            data-loading-target="plainModalBody" data-modal-title=" ">
            <?php echo e(str_limit($note->note_title, 65)); ?>

        </a>
    </td>
    <td class="notes_col_tags">
        <!--tag-->
        <?php if(count($note->tags ?? []) > 0): ?>
        <?php $__currentLoopData = $note->tags->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="label label-outline-default"><?php echo e(str_limit($tag->tag_title, 15)); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
        <span>---</span>
        <?php endif; ?>
        <!--/#tag-->

        <!--more tags (greater than tags->take(x) number above -->
        <?php if(count($note->tags ?? []) > 1): ?>
        <?php $tags = $note->tags; ?>
        <?php echo $__env->make('misc.more-tags', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
        <!--more tags-->
    </td>

    <td class="notes_col_date <?php echo e($page[ 'visibility_col_date'] ?? ''); ?> "><?php echo e(runtimeDate($note->note_created)); ?>

    </td>
    </td>
    <td class="notes_col_action  actions_column <?php echo e($page[ 'visibility_col_action'] ?? ''); ?> ">
        <!--action button-->
        <span class="list-table-action dropdown font-size-inherit">
            <?php if($note->permission_edit_delete_note): ?>
            <button type="button" title="<?php echo e(cleanLang(__('lang.delete'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="<?php echo e(cleanLang(__('lang.delete_note'))); ?>" data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>"
                data-ajax-type="DELETE" data-url="<?php echo e(url( '/')); ?>/notes/<?php echo e($note->note_id); ?> ">
                <i class="sl-icon-trash"></i>
            </button>
            <button type="button" title="<?php echo e(cleanLang(__('lang.edit'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-success btn-circle btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                data-toggle="modal" data-target="#commonModal"
                data-url="<?php echo e(urlResource('/notes/'.$note->note_id.'/edit')); ?>" data-loading-target="commonModalBody"
                data-modal-title="<?php echo e(cleanLang(__('lang.edit_note'))); ?>"
                data-action-url="<?php echo e(urlResource('/notes/'.$note->note_id.'?ref=list')); ?>" data-action-method="PUT"
                data-action-ajax-class="" data-action-ajax-loading-target="notes-td-container">
                <i class="sl-icon-note"></i>
            </button>
            <?php else: ?>
            <span class="btn btn-outline-default btn-circle btn-sm disabled  <?php echo e(runtimePlaceholdeActionsButtons()); ?>"
                data-toggle="tooltip" title="<?php echo e(cleanLang(__('lang.actions_not_available'))); ?>"><i class="sl-icon-trash"></i></span>
            <span class="btn btn-outline-default btn-circle btn-sm disabled  <?php echo e(runtimePlaceholdeActionsButtons()); ?>"
                data-toggle="tooltip" title="<?php echo e(cleanLang(__('lang.actions_not_available'))); ?>"><i class="sl-icon-note"></i></span>
            <?php endif; ?>
            <a href="javascript:void(0)" title="<?php echo e(cleanLang(__('lang.view'))); ?>"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm show-modal-button js-ajax-ux-request"
                data-toggle="modal" data-url="<?php echo e(url( '/')); ?>/notes/<?php echo e($note->note_id); ?> " data-target="#plainModal"
                data-loading-target="plainModalBody" data-modal-title="">
                <i class="ti-new-window"></i>
            </a>
        </span>
        <!--action button-->
    </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<!--each row--><?php /**PATH D:\my_apache\application\resources\views/pages/notes/components/table/ajax.blade.php ENDPATH**/ ?>