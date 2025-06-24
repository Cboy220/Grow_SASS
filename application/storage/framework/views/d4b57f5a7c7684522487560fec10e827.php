<!DOCTYPE html>
<html lang="en" class="team">

<!--html header-->
<?php echo $__env->make('layout.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!--html header-->

<body class="loggedin fix-header card-no-border fix-sidebar <?php echo e($page['page'] ?? ''); ?>">

    <!--main wrapper-->
    <div id="main-wrapper">

        <!--top nav-->
        <?php echo $__env->make('nav.topnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php echo $__env->make('pages.settings.leftmenu-settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!--top nav-->

        <!--page wrapper-->
        <div class="page-wrapper" id="settings-wrapper">

            <!--overlay-->
            <div class="page-wrapper-overlay js-close-side-panels hidden" data-target=""></div>
            <!--overlay-->

            <!--preloader-->
            <div class="preloader">
                <div class="loader">
                    <div class="loader-loading"></div>
                </div>
            </div>
            <!--preloader-->


            <!-- main content -->
            <?php echo $__env->yieldContent('content'); ?>
            <!-- /#main content -->

        </div>
        <!--page wrapper-->
    </div>
    <!--main wrapper-->
</body>
<!--common modals h-->
<?php echo $__env->make('modals.common-modal-wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('modals.plain-modal-wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('modals.actions-modal-wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('pages.authentication.modal.relogin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!--common modals-->
<!--js footer-->
<?php echo $__env->make('layout.footerjs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!--js footer-->



</html><?php /**PATH D:\my_apache\application\resources\views/pages/settings/wrapper-settings.blade.php ENDPATH**/ ?>