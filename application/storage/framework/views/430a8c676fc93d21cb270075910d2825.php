<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        <?php echo $__env->make('misc.heading-crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!--Page Title & Bread Crumbs -->

        <!-- action buttons x -->
        <div class="col-md-12 col-lg-7 align-self-center text-right parent-page-actions">
        </div>
        <!-- action buttons -->
    </div>
    <!--page heading-->

    
    <!-- main content -->

    <div class="card min-h-300">
        <div class="card-body tab-body tab-body-embedded" id="embed-content-container">
            <?php echo $__env->yieldContent('account-page'); ?>
        </div>
    </div>
    <!-- /#main content -->

</div>
<!--page content -->
</div>
<!--main content -->
<!--GENERAL CHECKOUT JS-->
<script src="public/js/landlord/frontend/account.js?v=<?php echo e(config('system.versioning')); ?>"></script><?php /**PATH D:\my_apache\application\resources\views/account/wrapper.blade.php ENDPATH**/ ?>