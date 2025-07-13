<?php if(config('visibility.viewing') == 'public'): ?>
<?php echo $__env->make('pages.documents.wrapper-public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
<?php echo $__env->make('pages.documents.wrapper-auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/documents/wrapper.blade.php ENDPATH**/ ?>