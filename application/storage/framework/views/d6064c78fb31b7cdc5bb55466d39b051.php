 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid ticket" id="ticket">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        <?php echo $__env->make('misc.heading-crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('pages.ticket.components.misc.actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
    <!--page heading-->


    <!-- page content -->
    <div class="row">
        <div class="col-12" id="tickets-table-wrapper">
            <!--ticket-->
            <?php echo $__env->make('pages.ticket.components.body', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!--ticket-->
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
<!--canned-->
<?php if(auth()->user()->is_team): ?>
<?php echo $__env->make('pages.ticket.components.misc.canned-side-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/ticket/wrapper.blade.php ENDPATH**/ ?>