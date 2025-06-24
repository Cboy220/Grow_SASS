<ul class="list-group list-group-flush" id="expectationList">
    <?php $__empty_1 = true; $__currentLoopData = $expectations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 ps-0">

            
            <div class="d-flex align-items-center flex-grow-1">
                <button style="font-size: 1.5rem" class="btn btn-sm p-0 mr-3 toggle-status"
                    data-action-url="<?php echo e(route('expectation.toggleCheck', $ex->client_expectation_id)); ?>"
                    data-id="<?php echo e($ex->client_expectation_id); ?>" style="background: none; border: none;">
                    <?php if($ex->status === 'fulfilled'): ?>
                        <i class="fas fa-check-circle text-info"></i>
                    <?php else: ?>
                        <i class="far fa-circle text-secondary"></i>
                    <?php endif; ?>
                </button>
                <span><?php echo e($ex->title); ?></span>
            </div>

            
            <div class="d-flex align-items-center text-nowrap">
                <small class="text-muted mr-3">
                    <i class="far fa-calendar-alt mr-1"></i>
                    <?php echo e(\Carbon\Carbon::parse($ex->due_date)->format('Y-m-d')); ?>

                </small>

                <span class="badge badge-<?php echo e($ex->status === 'fulfilled' ? 'success' : 'warning'); ?>">
                    <?php echo e($ex->status); ?>

                </span>

                <button
                    class="btn btn-sm btn-outline-secondary ml-3 edit-btn edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    data-id="<?php echo e($ex->client_expectation_id); ?>" data-toggle="modal" data-target="#basicModal"
                    data-url="<?php echo e(url('/expectation/edit/' . $ex->client_expectation_id)); ?>"
                    data-loading-target="basicModal">
                    <i class="fas fa-edit"></i>
                </button>

                <button
                    class="btn btn-sm btn-outline-danger ml-2 delete-btn edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    data-id="<?php echo e($ex->client_expectation_id); ?>" data-toggle="modal" data-target="#basicModal"
                    data-url="<?php echo e(url('/expectation/delete/' . $ex->client_expectation_id)); ?>"
                    data-loading-target="basicModal">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <li class="list-group-item text-muted"><?php echo e(__('lang.no_expectations_found')); ?></li>
    <?php endif; ?>
</ul>


<div class="mt-4" id="paginationLinks">
    <?php echo $expectations->links(); ?>

</div>
<?php /**PATH D:\my_apache\application\resources\views/pages/client/components/misc/expectation-list.blade.php ENDPATH**/ ?>