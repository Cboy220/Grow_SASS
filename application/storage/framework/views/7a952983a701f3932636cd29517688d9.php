 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- Page Title & Bread Crumbs -->
        <?php echo $__env->make('misc.heading-crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!--Page Title & Bread Crumbs -->


        <!-- action buttons -->
        <?php echo $__env->make('pages.payments.components.misc.list-page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!-- action buttons -->

    </div>
    <!--page heading-->

    <!--stats panel-->
    <?php if(auth()->user()->is_team): ?>
    <div class="stats-wrapper" id="payments-stats-wrapper">
    <?php echo $__env->make('misc.list-pages-stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <?php endif; ?>
    <!--stats panel-->

    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <!--payments table-->
            <?php echo $__env->make('pages.payments.components.table.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!--payments table-->
        </div>
    </div>
    <!--page content -->

</div>
<!--main content -->
<!--dynamic load payment payment (dynamic_trigger_dom)-->
<?php if(config('visibility.dynamic_load_modal')): ?>
<a href="javascript:void(0)" id="dynamic-payment-content"
    class="show-modal-button edit-add-modal-button js-ajax-ux-request reset-target-modal-form" data-toggle="modal" data-modal-title="<?php echo e(cleanLang(__('lang.payment'))); ?>"
    data-target="#plainModal" data-url="<?php echo e(url('/payments/'.request()->route('payment').'?ref=list')); ?>"
    data-loading-target="plainModalBody"></a>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/payments/wrapper.blade.php ENDPATH**/ ?>