<!--modal-->
<div class="modal actions-modal" role="dialog" aria-labelledby="foo" id="termsModal" <?php echo _clean(runtimeAllowCloseModalOptions()); ?>>
    <div class="modal-dialog">
        <form action="" method="post" id="termsModalForm" class="form-horizontal">
            <div class="modal-content">
                <div class="modal-body" id="termsModalBody">
                    
                    <?php echo config('system.settings_terms_of_service') ?? ''; ?>


                </div>
                <div class="modal-footer" id="termsModalFooter">
                    <button type="button" class="btn btn-rounded-x btn-secondary waves-effect text-left" data-dismiss="modal"><?php echo e(cleanLang(__('lang.close'))); ?></button>
                </div>
            </div>
        </form>
    </div>
</div><?php /**PATH D:\my_apache\application\resources\views/frontend/signup/terms.blade.php ENDPATH**/ ?>