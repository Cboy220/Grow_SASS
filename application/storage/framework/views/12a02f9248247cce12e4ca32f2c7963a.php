<div class="modal-dialog modal-md" id="basicModalContainer">
      
    <form action="" method="post" id="feedbackForm" class="form-horizontal">
        <div class="modal-content">
            <div class="modal-header" id="basicModalHeader">
                <h2 class="mb-4 text-center"><i class="fa-regular fa-star-half-stroke mr-1"></i><?php echo e(__('lang.customer_feedback')); ?></h2>
                <button type="button" class="close" data-dismiss="modal"
                    id="basicModalCloseIcon">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body min-h-200" id="basicModalBody">
                <!-- Comentario -->
                <div id="questions-scrollable">
                    <div id="questions-container">
                          <?php $__currentLoopData = $feedbackQueries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                            <div class="form-group">
                                <div class="feedback-block feedback-query-answer">
                                    <div class="pb-3">
                                        <label><strong><?php echo e($item->content); ?></strong></label>
                                    </div>
                                    <?php if($item->type == 1): ?>
                                        <div class="d-flex flex-wrap">
                                            <?php for($i = 1; $i <= $item->range; $i++): ?>
                                                <button type="button" class="btn btn-outline-info m-1 feedback-mark-button" name="<?php echo e($item->feedback_query_id); ?>" data-question="<?php echo e($item->feedback_query_id); ?>" data-value="<?php echo e($i); ?>"><?php echo e($i); ?></button>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($item->type == 2): ?>
                                        <div class="d-flex align-items-center editable feedback-stars p-2" data-question="<?php echo e($item->feedback_query_id); ?>">
                                            <?php for($i = 1; $i <= $item->range; $i++): ?>
                                                    <i class="far editable feedback-star fa-star fa-lg mr-2" role="button" data-value="<?php echo e($i); ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($item->type == 3): ?>
                                        <select class="form-control mt-2" name="<?php echo e($item->feedback_query_id); ?>" data-question="<?php echo e($item->feedback_query_id); ?>">
                                            <?php for($i = 1; $i <= $item->range; $i++): ?>
                                                <option value="<?php echo e($i); ?>"><?php echo e($i); ?></button>
                                            <?php endfor; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                  </div>
    
                <!-- Comentario -->
                <div class="form-group">
                    <label for="comment"><strong><?php echo e(__('lang.comment')); ?></strong> <small class="text-muted">(<?php echo e(__('lang.optional')); ?>)</small></label>
                    
                    <textarea class="form-control" id="comment" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer" id="basicModalFooter">
                    <button type="submit" class="btn btn-primary btn-block"><?php echo e(__('lang.send')); ?></button>
                    
            </div>
        </div>
    </form>
</div>
<?php /**PATH D:\my_apache\application\resources\views/pages/feedback/components/modals/add-edit-inc.blade.php ENDPATH**/ ?>