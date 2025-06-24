<!DOCTYPE html>
<html lang="en">

<?php echo $__env->make('frontend.layout.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<body class="home-page">

    <?php echo $__env->make('frontend.layout.menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('frontend.layout.preloader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main>

        <div class="corner-image" <?php echo _clean(dynamicStyleBackgroundImage($payload['hero']->frontend_data_8,
            $payload['hero']->frontend_data_9)); ?>></div>

        <!-- HERO AREA START -->
        <section class="hero_area">
            <div class="hero_area_image" <?php echo _clean(dynamicStyleBackgroundImage($payload['hero']->frontend_directory,
                $payload['hero']->frontend_filename)); ?>></div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="hero_area_inner">
                            <h1 class="x-heading-1"><?php echo e($payload['hero']->frontend_data_1); ?></h1>
                            <h2 class="x-heading-2"><?php echo e($payload['hero']->frontend_data_2); ?></h2>
                            <div class="x-description"><?php echo e($payload['hero']->frontend_data_3); ?></div>
                            <a href="<?php echo e($payload['hero']->frontend_data_5); ?>"
                                class="site_btn x-button-1"><?php echo e($payload['hero']->frontend_data_4); ?></a>
                            <a href="<?php echo e($payload['hero']->frontend_data_7); ?>"
                                class="site_btn_2"><?php echo e($payload['hero']->frontend_data_6); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- HERO AREA END -->

        <!-- SECTION - 1 -->
        <section class="section-1 section_padding_off features features_padding section_1 m-t-10" <?php echo _clean(dynamicStyleBackgroundImage($payload['section1_title']->frontend_directory,
            $payload['section1_title']->frontend_filename)); ?>>
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section_title">
                            <span class="section_title_meta"><?php echo e($payload['section1_title']->frontend_data_1); ?></span>
                            <h2><?php echo e($payload['section1_title']->frontend_data_2); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="row text-center">
                    <?php $__currentLoopData = $payload['section1_content']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3">
                        <div class="">
                            <div class="features_items_icon">
                                <img
                                    src="<?php echo e(url('storage/frontend/'.$content->frontend_directory.'/'.$content->frontend_filename)); ?>">
                            </div>
                            <div class="features_items_inner">
                                <h4><?php echo e($content->frontend_data_1); ?></h4>
                                <p><?php echo e($content->frontend_data_2); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>


        <!-- SECTION - 2 -->
        <section class="featured_section section_padding about_us section_2 m-t-70">
            <div class="row">
                <div class="col-lg-6">
                    <div class="featured-section-image section-image-left p-l-50">
                        <img
                            src="<?php echo e(url('storage/frontend/'.$payload['section2']->frontend_directory.'/'.$payload['section2']->frontend_filename)); ?>">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about_us_inner p-r-40">
                        <span><?php echo e($payload['section2']->frontend_data_1); ?></span>
                        <h3><?php echo e($payload['section2']->frontend_data_2); ?></h3>
                        <?php echo _clean($payload['section2']->frontend_data_3); ?>

                    </div>
                </div>
            </div>
        </section>



        <!-- SECTION - 3 -->
        <section class="section_padding cta_area section_3">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="cta_area_inner">
                            <h3><?php echo e($payload['section3']->frontend_data_1); ?></h3>
                            <div class="p-t-0 p-b-40">
                                <?php echo _clean($payload['section3']->frontend_data_2); ?>

                            </div>
                            <?php if($payload['section3']->frontend_data_3 != '' && $payload['section3']->frontend_data_5 != ''): ?>
                            <div class="cta_area_inner_btns">
                                <?php if($payload['section3']->frontend_data_3 != ''): ?>
                                <a href="<?php echo e($payload['section3']->frontend_data_4); ?>"
                                    class="site_btn"><?php echo e($payload['section3']->frontend_data_3); ?></a>
                                <?php endif; ?>
                                <?php if($payload['section3']->frontend_data_5 != ''): ?>
                                <a href="<?php echo e($payload['section3']->frontend_data_6); ?>"
                                    class="site_btn_2"><?php echo e($payload['section3']->frontend_data_5); ?></a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION - 4 -->
        <section class="featured_section section_padding about_us section_4 m-t-60">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about_us_inner p-l-40">
                        <span><?php echo e($payload['section4']->frontend_data_1); ?></span>
                        <h3><?php echo e($payload['section4']->frontend_data_2); ?></h3>
                        <?php echo _clean($payload['section4']->frontend_data_3); ?>

                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="featured-section-image section-image-left p-r-50">
                        <img
                            src="<?php echo e(url('storage/frontend/'.$payload['section4']->frontend_directory.'/'.$payload['section4']->frontend_filename)); ?>">
                    </div>
                </div>
            </div>
        </section>


        <!-- SECTION - 5 -->
        <section class="section_padding features latest_features">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section_title">
                            <span class="section_title_meta"><?php echo e($payload['section5_title']->frontend_data_1); ?></span>
                            <h2><?php echo e($payload['section5_title']->frontend_data_2); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="row text-center">
                    <?php $__currentLoopData = $payload['section5_content']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4">
                        <div class="featured_box">
                            <div class="features_items_icon">
                                <img
                                    src="<?php echo e(url('storage/frontend/'.$content->frontend_directory.'/'.$content->frontend_filename)); ?>">
                            </div>
                            <div class="features_items_inner">
                                <h4 class="highlighted_title"><?php echo e($content->frontend_data_1); ?></h4>
                                <p><?php echo e($content->frontend_data_2); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>


        <!-- SECTION - 6 -->
        <section class="section_padding cta_area cta_area_showcase m-t-50">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="cta_area_inner">
                            <h3 class="m-b-10"><?php echo e($payload['splash_title']->frontend_data_1); ?></h3>
                            <div class="p-t-0 p-b-20">
                                <?php echo _clean($payload['splash_title']->frontend_data_2); ?>

                            </div>

                            <div class="x-menu">
                                <ul>
                                    <li><a class="js_home_showcase active" href="javascript:void(0);"
                                        data-element="splash-image-1"><?php echo e($payload['splash1']->frontend_data_1); ?></a>
                                    </li>
                                    <li><a class="js_home_showcase" href="javascript:void(0);"
                                            data-element="splash-image-2"><?php echo e($payload['splash2']->frontend_data_1); ?></a>
                                    </li>
                                    <li><a class="js_home_showcase" href="javascript:void(0);"
                                            data-element="splash-image-3"><?php echo e($payload['splash3']->frontend_data_1); ?></a>
                                    </li>
                                    <li><a class="js_home_showcase" href="javascript:void(0);"
                                            data-element="splash-image-4"><?php echo e($payload['splash4']->frontend_data_1); ?></a>
                                    </li>
                                    <li><a class="js_home_showcase" href="javascript:void(0);"
                                            data-element="splash-image-5"><?php echo e($payload['splash5']->frontend_data_1); ?></a>
                                    </li>
                                    <li><a class="js_home_showcase" href="javascript:void(0);"
                                            data-element="splash-image-6"><?php echo e($payload['splash6']->frontend_data_1); ?></a>
                                    </li>
                                </ul>
                            </div>


                            <div class="x-image-container">
                                <!--splash-image-1-->
                                <div class="splash-images" id="splash-image-1">
                                    <img
                                        src="<?php echo e(url('storage/frontend/'.$payload['splash1']->frontend_directory.'/'.$payload['splash1']->frontend_filename)); ?>">
                                </div>

                                <!--splash-image-2-->
                                <div class="splash-images hidden" id="splash-image-2" class="hidden">
                                    <img
                                        src="<?php echo e(url('storage/frontend/'.$payload['splash2']->frontend_directory.'/'.$payload['splash2']->frontend_filename)); ?>">
                                </div>

                                <!--splash-image-3-->
                                <div class="splash-images hidden" id="splash-image-3" class="hidden">
                                    <img
                                        src="<?php echo e(url('storage/frontend/'.$payload['splash3']->frontend_directory.'/'.$payload['splash3']->frontend_filename)); ?>">
                                </div>

                                <!--splash-image-4-->
                                <div class="splash-images hidden" id="splash-image-4" class="hidden">
                                    <img
                                        src="<?php echo e(url('storage/frontend/'.$payload['splash4']->frontend_directory.'/'.$payload['splash4']->frontend_filename)); ?>">
                                </div>

                                <!--splash-image-5-->
                                <div class="splash-images hidden" id="splash-image-5" class="hidden">
                                    <img
                                        src="<?php echo e(url('storage/frontend/'.$payload['splash5']->frontend_directory.'/'.$payload['splash5']->frontend_filename)); ?>">
                                </div>

                                <!--splash-image-6-->
                                <div class="splash-images hidden" id="splash-image-6" class="hidden">
                                    <img
                                        src="<?php echo e(url('storage/frontend/'.$payload['splash6']->frontend_directory.'/'.$payload['splash6']->frontend_filename)); ?>">
                                </div>

                            </div>
                            <?php if($payload['splash_title']->frontend_data_3 != '' && $payload['splash_title']->frontend_data_5 != ''): ?>
                            <!--call to action-->
                            <div class="p-t-70 p-b-50 cta_buttons">
                                <?php if($payload['splash_title']->frontend_data_3 != ''): ?>
                                <a href="<?php echo e($payload['splash_title']->frontend_data_4); ?>"
                                    class="site_btn"><?php echo e($payload['splash_title']->frontend_data_3); ?></a>
                                <?php endif; ?>
                                <?php if($payload['splash_title']->frontend_data_5 != ''): ?>
                                <a href="<?php echo e($payload['splash_title']->frontend_data_6); ?>"
                                    class="site_btn_2"><?php echo e($payload['splash_title']->frontend_data_5); ?></a>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- CTA AREA END -->
    </main>

    <?php echo $__env->make('frontend.layout.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    
    <?php echo $__env->make('frontend.layout.footerjs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html><?php /**PATH D:\my_apache\application\resources\views/frontend/home/page.blade.php ENDPATH**/ ?>