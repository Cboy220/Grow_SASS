<!--bulk actions-->
<?php echo $__env->make('pages.invoices.components.actions.checkbox-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--main table view-->
<?php echo $__env->make('pages.invoices.components.table.table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--filter-->
<?php if(auth()->user()->is_team): ?>
<?php echo $__env->make('pages.invoices.components.misc.filter-invoices', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<!--filter-->

<!--custom table view-->
<?php echo $__env->make('pages.invoices.components.misc.table-config', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--export-->
<?php if(config('visibility.list_page_actions_exporting')): ?>
<?php echo $__env->make('pages.export.invoices.export', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?><?php /**PATH D:\my_apache\application\resources\views/pages/invoices/components/table/wrapper.blade.php ENDPATH**/ ?>