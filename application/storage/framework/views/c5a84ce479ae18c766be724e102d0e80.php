<!-- action buttons -->
<?php echo $__env->make('pages.invoices.components.misc.list-page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!-- action buttons -->

<!--stats panel-->
<?php if(auth()->user()->is_team): ?>
<div id="invoices-stats-wrapper" class="stats-wrapper card-embed-fix">
<?php if(@count($invoices ?? []) > 0): ?> <?php echo $__env->make('misc.list-pages-stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
</div>
<?php endif; ?>
<!--stats panel-->

<!--invoices table-->
<div class="card-embed-fix">
<?php echo $__env->make('pages.invoices.components.table.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<!--invoices table--><?php /**PATH D:\my_apache\application\resources\views/pages/invoices/tabswrapper.blade.php ENDPATH**/ ?>