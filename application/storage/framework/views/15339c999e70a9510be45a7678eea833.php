 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        <?php echo $__env->make('misc.heading-crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!--Page Title & Bread Crumbs -->

        <!-- action buttons -->
        <div class="col-md-12 col-lg-7 align-self-center text-right parent-page-actions">
        </div>
        <!-- action buttons -->
    </div>
    <!--page heading-->


    <!-- main content -->
    <div class="card min-h-300">
            <div class="card-body tab-body tab-body-embedded" id="embed-content-container">
            <?php echo $__env->yieldContent('settings-page'); ?>
        </div>
    </div>
    <!-- /#main content -->

</div>
<!--page content -->
</div>
<!--main content -->
<!--dynamically load comments-->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pages.settings.wrapper-settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/settings/wrapper.blade.php ENDPATH**/ ?>