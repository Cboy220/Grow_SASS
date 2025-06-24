<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-7 p-b-9 align-self-center text-right <?php echo e($page['list_page_actions_size'] ?? ''); ?> <?php echo e($page['list_page_container_class'] ?? ''); ?>"
    id="list-page-actions-container">
    <div id="list-page-actions">

        <!--ADD NEW ITEM-->
        <?php if(auth()->user()->role->role_feedback >= 2): ?>
        <button type="button"
            class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form <?php echo e($page['add_button_classes'] ?? ''); ?>"
            data-toggle="modal" data-target="#basicModal" data-url="<?php echo e($page['add_modal_create_url'] ?? ''); ?>"
            data-loading-target="basicModal" data-action-url="<?php echo e($page['add_modal_action_url'] ?? ''); ?>"
            data-action-ajax-class="<?php echo e($page['add_modal_action_ajax_class'] ?? ''); ?>"
            data-action-ajax-loading-target="<?php echo e($page['add_modal_action_ajax_loading_target'] ?? ''); ?>"
            data-save-button-class="<?php echo e($page['add_modal_save_button_class'] ?? ''); ?>" data-project-progress="0">
            <i class="ti-plus"></i>
        </button>
        <?php endif; ?>

        <!--add new button (link)-->
        <?php if( config('visibility.list_page_actions_add_button_link')): ?>
        <a id="fx-page-actions-add-button" type="button" class="btn btn-danger btn-add-circle edit-add-modal-button"
            href="<?php echo e($page['add_button_link_url'] ?? ''); ?>">
            <i class="ti-plus"></i>
        </a>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\my_apache\application\resources\views/pages/feedback/components/misc/list-page-actions.blade.php ENDPATH**/ ?>