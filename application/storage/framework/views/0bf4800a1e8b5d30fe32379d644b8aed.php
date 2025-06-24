<!-- CTA PANEL -->
<?php if(isset($payload['show_footer_cta']) && $payload['show_footer_cta']): ?>
<section class="section_padding cta_area section_3 m-t-80" id="footer_cta_panel">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="cta_area_inner">
                    <h3><?php echo e($cta->frontend_data_1); ?></h3>
                    <div class="p-t-0 p-b-40">
                        <?php echo _clean($cta->frontend_data_2); ?>

                    </div>

                    <?php if($cta->frontend_data_3 != '' && $cta->frontend_data_5 != ''): ?>
                    <div class="cta_area_inner_btns">
                        <?php if($cta->frontend_data_3 != ''): ?>
                        <a href="<?php echo e($cta->frontend_data_4); ?>"
                            class="site_btn"><?php echo e($cta->frontend_data_3); ?></a>
                        <?php endif; ?>
                        <?php if($cta->frontend_data_5 != ''): ?>
                        <a href="<?php echo e($cta->frontend_data_6); ?>"
                            class="site_btn_2"><?php echo e($cta->frontend_data_5); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>



<!-- FOOTER AREA START -->
<footer class="footer"  id="footer_wrapper">
    <div class="container">
        <div class="row">

            <!--section 1-->
            <?php if($footer->frontend_data_1 != ''): ?>
            <div class="col-sm-12 col-lg-3" id="footer_section_1">
                <div class="footer-content">
                    <?php echo _clean($footer->frontend_data_1); ?>

                </div>
            </div>
            <?php endif; ?>

            <!--section 2-->
            <?php if($footer->frontend_data_2 != ''): ?>
            <div class="col-sm-12 col-lg-3" id="footer_section_2">
                <div class="footer-content">
                    <?php echo _clean($footer->frontend_data_2); ?>

                </div>
            </div>
            <?php endif; ?>


            <!--section 3-->
            <?php if($footer->frontend_data_3 != ''): ?>
            <div class="col-sm-12 col-lg-3" id="footer_section_3">
                <div class="footer-content">
                    <?php echo _clean($footer->frontend_data_3); ?>

                </div>
            </div>
            <?php endif; ?>


            <!--section 4-->
            <?php if($footer->frontend_data_4 != ''): ?>
            <div class="col-sm-12 col-lg-3" id="footer_section_4">
                <div class="footer-content">
                    <?php echo _clean($footer->frontend_data_4); ?>

                </div>
            </div>
            <?php endif; ?>



        </div>
    </div>
</footer>
<!-- FOOTER AREA END --><?php /**PATH D:\my_apache\application\resources\views/frontend/layout/footer.blade.php ENDPATH**/ ?>