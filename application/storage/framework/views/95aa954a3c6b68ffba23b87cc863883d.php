 <?php $__env->startSection('content'); ?>
    <!-- main content -->
    <div class="container-fluid">

        <!--page heading-->
        <div class="row page-titles">

            <!-- Page Title & Bread Crumbs -->
            <?php echo $__env->make('misc.heading-crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!--Page Title & Bread Crumbs -->


            <!-- action buttons -->
            <?php echo $__env->make('pages.expectation.components.misc.list-page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!-- action buttons -->

        </div>
        <!--page heading-->

        <!-- page content -->
        <div class="row">
            <div class="col-sm-12 col-lg-9 m-auto" id="expectation-lists-part-wrapper">
                <div class="container py-4">
                    <h5><i class="fas fa-bullseye mr-2"></i><?php echo e(__('lang.my_expectations')); ?></h5>

                    <!-- Search box -->
                    <div class="input-group mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="<?php echo e(__('lang.search_expectations_placeholder')); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Stats and progress bar -->
                    <div id="expectationStatsContainer"></div>

                    <!-- Expectation list -->
                    <div id="expectationListContainer"></div>
                </div>
            </div>
        </div>
        <!--page content -->
    </div>
    
    <?php echo $__env->make('pages.expectation.js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!--main content -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/expectation/wrapper.blade.php ENDPATH**/ ?>