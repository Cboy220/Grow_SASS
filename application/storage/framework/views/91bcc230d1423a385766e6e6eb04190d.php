<!-- dynamic load more button-->
<div class="kanban-board-element autoload loadmore-button-container <?php echo e($board['load_more']); ?>"
    id="leads-loadmore-container-<?php echo e($board['id']); ?>">
    <a data-url="<?php echo e($board['load_more_url']); ?>" href="javascript:void(0)"
        class="btn btn-rounded-x btn-secondary js-ajax-ux-request"
        id="load-more-button-<?php echo e($board['id']); ?>"><?php echo e(cleanLang(__('lang.show_more'))); ?></a>
</div>
<!-- /#dynamic load more button--><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/leads/components/kanban/loadmore-button.blade.php ENDPATH**/ ?>