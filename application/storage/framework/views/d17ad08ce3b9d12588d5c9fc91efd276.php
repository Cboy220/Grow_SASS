<div class="row">
    <!--PROJECTS PENDING-->
    <?php echo $__env->make('pages.home.client.widgets.first-row.projects-pending', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!--PROJECTS COMPLETED-->
    <?php echo $__env->make('pages.home.client.widgets.first-row.projects-completed', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!--INVOICES DUE-->
    <?php echo $__env->make('pages.home.client.widgets.first-row.invoices-due', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!--INVOICES OVERDUE-->
    <?php echo $__env->make('pages.home.client.widgets.first-row.invoices-overdue', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div><?php /**PATH D:\my_apache\application\resources\views/pages/home/client/widgets/first-row/wrapper.blade.php ENDPATH**/ ?>