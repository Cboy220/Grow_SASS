<?php if(config('visibility.signatures_show_top_line')): ?>
<div class="line m-t-50"></div>
<?php endif; ?>

<div class="doc-signed-panel">
    <div class="row">

        <!--provider signature-->
        <div class="col-6 text-left">
            <div class="p-l-0">
                <ul>
                    <li>
                        <h5><?php echo app('translator')->get('lang.service_provider'); ?></h5>
                    </li>

                    <!--signed-->
                    <?php if(config('visibility.doc_provider_signed')): ?>
                    <li class="p-t-10"><?php echo e($document->doc_provider_signed_first_name); ?>

                        <?php echo e($document->doc_provider_signed_last_name); ?></li>
                    <li>
                        <img src="<?php echo e(url('storage/files/'.$document->doc_provider_signed_signature_directory .'/'.$document->doc_provider_signed_signature_filename)); ?>"
                            alt="<?php echo app('translator')->get('lang.signature'); ?>" />
                    </li>

                    <!--signature date-->
                    <li class="p-t-15 m-b-10"><?php echo app('translator')->get('lang.date'); ?> :
                        <?php echo e(runtimeDate($document->doc_provider_signed_date)); ?>

                        <!--info on why the providers signature can nolonger be delete-->
                        <?php if(config('visibility.doc_provider_delete_signature_disabled')): ?>
                        <span class="align-middle text-info font-16" data-toggle="tooltip"
                            title="<?php echo app('translator')->get('lang.contract_signature_cannot_be_delete'); ?>" data-placement="top"><i
                                class="ti-info-alt"></i></span>
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>

                    <!--unsigned - viewing mode-->
                    <?php if(config('visibility.doc_provider_unsigned')): ?>
                    <li>
                        <h3 class="muted p-t-10"><?php echo app('translator')->get('lang.unsigned'); ?></h3>
                    </li>
                    <?php endif; ?>


                    <!--add signature-->
                    <?php if(config('visibility.doc_provider_add_signature')): ?>
                    <li>
                        <button type="button"
                            class="btn btn-secondary btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal" data-progress-bar="hidden"
                            data-url="<?php echo e(url('contracts/'.$document->doc_unique_id.'/sign/team')); ?>"
                            data-loading-target="commonModalBody" data-modal-title="<?php echo app('translator')->get('lang.sign_contract'); ?>"
                            data-action-form-id="" data-modal-size="modal-lg"
                            data-action-url="<?php echo e(url('contracts/'.$document->doc_unique_id.'/sign/team')); ?>"
                            data-action-method="POST" data-action-ajax-class="js-ajax-ux-request">
                            <?php echo app('translator')->get('lang.sign_contract'); ?>
                        </button>
                    </li>
                    <?php endif; ?>

                    <!--delete signature-->
                    <?php if(config('visibility.doc_provider_delete_signature')): ?>
                    <li>
                        <button type="button" class="btn btn-outline-danger btn-sm confirm-action-danger"
                            data-confirm-title="<?php echo app('translator')->get('lang.delete_signature'); ?>"
                            data-confirm-text="<?php echo app('translator')->get('lang.are_you_sure'); ?>" data-ajax-type="DELETE"
                            data-url="<?php echo e(url('contracts/'.$document->doc_unique_id.'/sign/delete-signature')); ?>">
                            <?php echo app('translator')->get('lang.delete_signature'); ?>
                        </button>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
        <!--client signature-->
        <div class="col-6 text-right">
            <div class="p-r-0">
                <ul>
                    <li>
                        <h5><?php echo app('translator')->get('lang.client'); ?></h5>
                    </li>

                    <!--signed-->
                    <?php if(config('visibility.doc_client_signed')): ?>
                    <li><?php echo e($document->doc_signed_first_name); ?>

                        <?php echo e($document->doc_signed_last_name); ?></li>
                    <li>
                        <img src="<?php echo e(url('storage/files/'.$document->doc_signed_signature_directory .'/'.$document->doc_signed_signature_filename)); ?>"
                            alt="<?php echo app('translator')->get('lang.signature'); ?>" />
                    </li>

                    <!--signature date-->
                    <li class="p-t-15 m-b-10"><?php echo app('translator')->get('lang.date'); ?> :
                        <?php echo e(runtimeDate($document->doc_signed_date)); ?>

                    </li>
                    <?php endif; ?>

                    <!--unsigned - viewing mode-->
                    <?php if(config('visibility.doc_client_unsigned')): ?>
                    <li>
                        <h3 class="muted p-t-10"><?php echo app('translator')->get('lang.unsigned'); ?></h3>
                    </li>
                    <?php endif; ?>

                    <!--add signature-->
                    <?php if(config('visibility.doc_client_add_signature')): ?>
                    <li>
                        <button type="button"
                            class="btn btn-secondary btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                            data-toggle="modal" data-target="#commonModal" data-progress-bar="hidden"
                            data-url="<?php echo e(url('contracts/'.$document->doc_unique_id.'/sign/client')); ?>"
                            data-loading-target="commonModalBody" data-modal-title="<?php echo app('translator')->get('lang.sign_contract'); ?>"
                            data-action-form-id="" data-modal-size="modal-lg"
                            data-action-url="<?php echo e(url('contracts/'.$document->doc_unique_id.'/sign/client')); ?>"
                            data-action-method="POST" data-action-ajax-class="js-ajax-ux-request">
                            <?php echo app('translator')->get('lang.sign_contract'); ?>
                        </button>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>
</div>

<?php if(config('visibility.signatures_show_bottom_line')): ?>
<div class="line m-t-20"></div>
<?php endif; ?><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/documents/elements/signatures-contracts.blade.php ENDPATH**/ ?>