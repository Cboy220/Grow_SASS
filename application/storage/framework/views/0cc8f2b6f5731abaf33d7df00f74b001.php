<!--bulk actions-->
<?php echo $__env->make('pages.contracts.components.actions.checkbox-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--main table view-->
<?php echo $__env->make('pages.contracts.components.table.table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--filter-->
<?php if(auth()->user()->is_team): ?>
<?php echo $__env->make('pages.contracts.components.misc.filter-contracts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<!--filter--><?php /**PATH D:\my_apache\application\resources\views/pages/contracts/components/table/wrapper.blade.php ENDPATH**/ ?>