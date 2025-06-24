<div class="row">

    <!--INCOME-->
    <?php echo $__env->make('pages.home.admin.widgets.second-row.income', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <!--LEADS-->
    <?php echo $__env->make('pages.home.admin.widgets.second-row.leads', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <!--TICKETS (optional)-->
    <?php echo $__env->make('pages.home.admin.widgets.second-row.tickets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!--Healthy information of client (optional)-->
    <?php echo $__env->make('pages.home.admin.widgets.second-row.clients-healthy', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <!--Feedback recently information of client (optional)-->
    <?php echo $__env->make('pages.home.admin.widgets.second-row.feedbacks', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
</div><?php /**PATH D:\my_apache\application\resources\views/pages/home/admin/widgets/second-row/wrapper.blade.php ENDPATH**/ ?>