<!--main table view-->
<?php echo $__env->make('pages.clients.components.table.table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--filter-->
<?php if(auth()->user()->is_team): ?>
<?php echo $__env->make('pages.clients.components.misc.filter-clients', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<!--custom table view-->
<?php echo $__env->make('pages.clients.components.misc.table-config', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--export-->
<?php if(config('visibility.list_page_actions_exporting')): ?>
<?php echo $__env->make('pages.export.clients.export', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?><?php /**PATH D:\my_apache\application\resources\views/pages/clients/components/table/wrapper.blade.php ENDPATH**/ ?>