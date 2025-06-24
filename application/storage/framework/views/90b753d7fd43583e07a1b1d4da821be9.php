 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">


        <?php echo $__env->make('pages.client.components.misc.crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('pages.client.components.misc.actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
    <!--page heading-->

    <!-- page content -->
    <div class="row">
        <!--left panel-->
        <div class="col-xl-3 d-none d-xl-block">
            <?php echo $__env->make('pages.client.components.misc.leftpanel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <!--left panel-->
        <!-- Column -->
        <div class="col-xl-9 col-lg-12">
            <div class="card h-100">

                <!--top nav-->
                <?php echo $__env->make('pages.client.components.misc.topnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <!-- main content -->
                <div class="tab-content">
                    <div class="tab-pane active ext-ajax-container" id="clients_ajaxtab" role="tabpanel">
                        <div class="card-body tab-body tab-body-embedded p-t-40" id="embed-content-container">
                            <!--dynamic content here-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!--page content -->

</div>
<!--main content -->
<span class="hidden" id="dynamic-client-content" class="js-ajax-ux-request"  data-url="<?php echo e($page['dynamic_url'] ?? ''); ?>" data-loading-target="embed-content-container">placeholder</span>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/client/wrapper.blade.php ENDPATH**/ ?>