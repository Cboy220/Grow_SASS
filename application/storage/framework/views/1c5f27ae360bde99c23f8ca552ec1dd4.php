
<?php $__env->startSection('document'); ?>
<div class="col-12">

    <div class="docs-main-wrapper editing-mode box-shadow">

        <!--hero header-->
        <div class="hero-header-wrapper" id="hero-header-wrapper">
            <!--[element] here header-->
            <?php echo $__env->make('pages.documents.elements.hero', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>


        <!--[element] doc to and by-->
        <?php echo $__env->make('pages.documents.elements.doc-to-by', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!--[element] dates-->
        <?php echo $__env->make('pages.documents.elements.doc-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="doc-body">
            <?php echo $document->doc_body; ?>



            <!--signatures - proposal -->
            <?php if($document->doc_type == 'proposal' && $document->doc_status == 'accepted'): ?>
            <div id="doc-signatures-container">
                <?php echo $__env->make('pages.documents.elements.signatures-proposals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <?php endif; ?>


            <!--signatures - contract -->
            <?php if($document->doc_type == 'contract'): ?>
            <div id="doc-signatures-container">
                <?php echo $__env->make('pages.documents.elements.signatures-contracts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <?php endif; ?>

            <!-- accept, decline & print buttons -->
            <?php if(config('visibility.proposal_accept_decline_button_footer')): ?>
            <div class="doc-footer-actions-container">
                <div class="line m-t-20"></div>
                <div class="p-t-25 invoice-pay" id="invoice-buttons-container">
                    <div class="text-right">

                        <!--print-->
                        <a class="btn btn-secondary btn-outline"
                            href="<?php echo e(url('proposals/'.$document->doc_id.'?render=print')); ?>" target="_blank">
                            <span><i class="sl-icon-printer"></i> <?php echo app('translator')->get('lang.print_proposal'); ?></span> </a>

                        <?php if(config('visibility.proposal_accept_decline_buttons')): ?>
                        <!--decline-->
                        <button class="buttons-accept-decline btn btn-danger confirm-action-danger"
                            data-confirm-title="<?php echo app('translator')->get('lang.decline_proposal'); ?>"
                            data-confirm-text="<?php echo app('translator')->get('lang.confirm_decline_proposal'); ?>" data-ajax-type="GET"
                            data-url="<?php echo e(url('proposals/'.$document->doc_unique_id.'/decline')); ?>">
                            <?php echo app('translator')->get('lang.decline_proposal'); ?> </button>

                        <!--accept-->
                        <button type="button"
                            class="buttons-accept-decline btn btn-success edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal" data-progress-bar="hidden"
                            data-url="<?php echo e(url('proposals/'.$document->doc_unique_id.'/sign')); ?>"
                            data-loading-target="commonModalBody" data-modal-title="<?php echo app('translator')->get('lang.accept_proposal'); ?>"
                            data-action-form-id="" data-modal-size="modal-lg"
                            data-action-url="<?php echo e(url('proposals/'.$document->doc_unique_id.'/accept')); ?>"
                            data-action-method="POST" data-action-ajax-class="js-ajax-ux-request">
                            <?php echo app('translator')->get('lang.accept_proposal'); ?>
                        </button>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>



    <!--signature details-->

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pages.documents.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/documents/preview/page.blade.php ENDPATH**/ ?>