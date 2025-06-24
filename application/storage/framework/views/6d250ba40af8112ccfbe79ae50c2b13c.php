<!--change category-->
<?php if($project->permission_edit_project): ?>
<a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form" href="javascript:void(0)"
    data-toggle="modal" data-target="#actionsModal" data-modal-title="<?php echo e(cleanLang(__('lang.change_category'))); ?>"
    data-url="<?php echo e(url('/projects/change-category')); ?>"
    data-action-url="<?php echo e(urlResource('/projects/change-category?id='.$project->project_id.'&filter_category='.request('filter_category'))); ?>"
    data-loading-target="actionsModalBody" data-action-method="POST">
    <?php echo e(cleanLang(__('lang.change_category'))); ?></a>
<!--change status-->
<a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form" href="javascript:void(0)"
    data-toggle="modal" data-target="#actionsModal" data-modal-title="<?php echo e(cleanLang(__('lang.change_status'))); ?>"
    data-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/change-status')); ?>"
    data-action-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/change-status?filter_category='.request('filter_category'))); ?>"
    data-loading-target="actionsModalBody" data-action-method="POST">
    <?php echo e(cleanLang(__('lang.change_status'))); ?></a>

<!--update progress-->
<a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form" href="javascript:void(0)"
    data-toggle="modal" data-target="#actionsModal" data-modal-title="<?php echo e(cleanLang(__('lang.update_progress'))); ?>"
    data-url="<?php echo e(url('/projects/'.$project->project_id.'/progress?ref=list')); ?>"
    data-action-url="<?php echo e(url('/projects/'.$project->project_id.'/progress?ref=list&filter_category='.request('filter_category'))); ?>"
    data-loading-target="actionsModalBody" data-action-method="POST">
    <?php echo e(cleanLang(__('lang.update_progress'))); ?></a>

<!--stop all timers-->
<a class="dropdown-item confirm-action-danger" data-confirm-title="<?php echo e(cleanLang(__('lang.stop_all_timers'))); ?>"
    data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>" data-ajax-type="PUT"
    data-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/stop-all-timers')); ?>">
    <?php echo e(cleanLang(__('lang.stop_all_timers'))); ?>

</a>

<!--clone project-->
<?php if(auth()->user()->role->role_projects > 1): ?>
<a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button"
    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
    data-modal-title="<?php echo e(cleanLang(__('lang.clone_project'))); ?>"
    data-url="<?php echo e(url('/projects/'.$project->project_id.'/clone')); ?>"
    data-action-url="<?php echo e(url('/projects/'.$project->project_id.'/clone?filter_category='.request('filter_category'))); ?>"
    data-loading-target="actionsModalBody" data-action-method="POST">
    <?php echo e(cleanLang(__('lang.clone_project'))); ?></a>
<?php endif; ?>

<!--archive-->
<?php if($project->project_active_state == 'active' && runtimeArchivingOptions()): ?>
<a class="dropdown-item confirm-action-info" data-confirm-title="<?php echo e(cleanLang(__('lang.archive_project'))); ?>"
    data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>" data-ajax-type="PUT"
    data-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/archive')); ?>">
    <?php echo e(cleanLang(__('lang.archive'))); ?>

</a>
<?php endif; ?>

<!--activate-->
<?php if($project->project_active_state == 'archived' && runtimeArchivingOptions()): ?>
<a class="dropdown-item confirm-action-info" data-confirm-title="<?php echo e(cleanLang(__('lang.restore_project'))); ?>"
    data-confirm-text="<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>" data-ajax-type="PUT"
    data-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/activate')); ?>">
    <?php echo e(cleanLang(__('lang.restore'))); ?>

</a>
<?php endif; ?>


<!--automation-->
<a href="javascript:void(0)" class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
    data-toggle="modal" data-target="#commonModal"
    data-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/edit-automation?ref=list')); ?>"
    data-loading-target="commonModalBody" data-modal-title="<?php echo app('translator')->get('lang.project_automation'); ?>"
    data-action-url="<?php echo e(urlResource('/projects/'.$project->project_id.'/edit-automation?ref=list')); ?>"
    data-action-method="POST" data-action-ajax-loading-target="commonModalBody"><?php echo app('translator')->get('lang.automation'); ?>
</a>

<?php else: ?>
<span class="small">--- no options avaiable</span>
<?php endif; ?><?php /**PATH D:\my_apache\application\resources\views/pages/projects/views/common/dropdown-menu-team.blade.php ENDPATH**/ ?>