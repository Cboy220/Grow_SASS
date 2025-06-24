
<?php $__env->startSection('settings-page'); ?>
<span id="dynamic-settings-content" data-loading-target="embed-content-container"
    data-url="<?php echo e($page['dynamic_url'] ?? ''); ?>"></span>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pages.settings.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/settings/sections/dynamic/page.blade.php ENDPATH**/ ?>