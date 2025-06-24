    <!-- HEADING AREA START -->
    <header class="heading" id="frontend-top-menu">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-4">
                    <div class="heading_mobile">
                        <a href="/" class="heading_logo">
                            <img src="<?php echo e(runtimeLogoFrontEnd()); ?>" alt="">
                        </a>
                        <div class="heading_mobile_thum"></div>
                    </div>
                </div>
                <div class="col-md-9 col-8 text-right">
                    <nav class="heading_menu">
                        <ul>
                            <?php $__currentLoopData = $mainmenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="heading_menu_list">
                                <a href="<?php echo e($menu->frontend_data_2); ?>"
                                    target="<?php echo e(saasLinktarget($menu->frontend_data_6)); ?>"
                                    class="heading_menu_list_links <?php echo e(runtimeFrontendMenuSignup($menu->frontend_data_2)); ?>"><?php echo e($menu->frontend_data_1); ?></a></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </nav>
                </div>
            </div>

            <!--nav-->
            <a href="javascript:void(0);" class="mobile-menu-icon" id="mobile-menu-icon">
                <i class="sl-icon-menu"></i>
            </a>
        </div>

        <!--mobile menu-->
        <div class="mobile-menu-container">
            <div class="mobile-menu hidden" id="mobile-menu">
                <ul>
                    <?php $__currentLoopData = $mainmenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="heading_menu_list">
                        <a href="<?php echo e($menu->frontend_data_2); ?>" target="<?php echo e(saasLinktarget($menu->frontend_data_6)); ?>"
                            class='heading_menu_list_links'><?php echo e($menu->frontend_data_1); ?></a></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </header>
    <!-- HEADING AREA END --><?php /**PATH D:\my_apache\application\resources\views/frontend/layout/menu.blade.php ENDPATH**/ ?>