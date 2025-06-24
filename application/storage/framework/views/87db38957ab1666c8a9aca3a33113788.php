<?php
    $stats = $payload['stats'];
?>

<div class="col-lg-3 col-md-12" id="dashboard-widgets-client-healthy-status">
    <div class="card">
        <div class="card-body">
            <div class="d-flex m-b-30 no-block">
                <h5 class="card-title m-b-0 align-self-center"><?php echo e(__('lang.customer_success')); ?></h5>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        <span><?php echo e(__('lang.expectation_compliance_percent')); ?></span>
                    </div>
                    <div class="font-weight-bold text-success"><?php echo e($stats['expectation_percent']); ?>%</div>
                </div>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-star text-warning mr-2"></i>
                        <span><?php echo e(__('lang.average_feedback')); ?></span>
                    </div>
                    <div class="font-weight-bold text-primary"><?php echo e($stats['average_feedback']); ?></div>
                </div>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-comment-dots text-info mr-2"></i>
                        <span>Últimos comentarios</span>
                    </div>
                    <div class="pl-4">
                        <?php $__empty_1 = true; $__currentLoopData = $stats['recent_comments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div>“<?php echo e($comment); ?>”</div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-muted">No hay comentarios recientes.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card customer-success-block mb-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <i
                            class="fas fa-heart 
                    <?php if($stats['health_status'] === 'green'): ?> text-success
                    <?php elseif($stats['health_status'] === 'yellow'): ?> text-warning
                    <?php else: ?> text-danger <?php endif; ?>
                    mr-2"></i>
                        <span>Salud del cliente</span>
                    </div>
                    <div
                        class="font-weight-bold
                    <?php if($stats['health_status'] === 'green'): ?> text-success
                    <?php elseif($stats['health_status'] === 'yellow'): ?> text-warning
                    <?php else: ?> text-danger <?php endif; ?>">
                        <?php echo e(ucfirst($stats['health_status'])); ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php /**PATH D:\my_apache\application\resources\views/pages/home/admin/widgets/second-row/clients-healthy.blade.php ENDPATH**/ ?>