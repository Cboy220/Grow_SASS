<div class="mb-2 text-muted small">
    <i class="fas fa-check-circle text-success mr-1"></i> <?php echo e($stats['fulfilled']); ?> <?php echo e(__('lang.fulfilled')); ?> &nbsp;&nbsp;
    <i class="fas fa-hourglass-half text-warning mr-1"></i> <?php echo e($stats['pending']); ?> <?php echo e(__('lang.pending')); ?> &nbsp;&nbsp;
    <i class="fas fa-clock text-danger mr-1"></i> <?php echo e($stats['overdue']); ?> <?php echo e(__('lang.overdue')); ?>

</div>

<div class="progress mb-4 text-white text-center" style="height: 1.5rem; font-size: 0.9rem; font-weight: 500;">
    <?php if($stats['fulfilledPercent'] > 0): ?>
        <div class="progress-bar bg-success d-flex align-items-center justify-content-center"
            style="width: <?php echo e($stats['fulfilledPercent']); ?>%">
            <span class="d-flex align-items-center">
                <i class="fas fa-check-circle"></i><span class="ml-1"><?php echo e($stats['fulfilledPercent']); ?>%</span>
            </span>
        </div>
    <?php endif; ?>

    <?php if($stats['pendingPercent'] > 0): ?>
        <div class="progress-bar bg-warning text-dark d-flex align-items-center justify-content-center"
            style="width: <?php echo e($stats['pendingPercent']); ?>%">
            <span class="d-flex align-items-center">
                <i class="fas fa-hourglass-half"></i><span class="ml-1"><?php echo e($stats['pendingPercent']); ?>%</span>
            </span>
        </div>
    <?php endif; ?>

    <?php if($stats['overduePercent'] > 0): ?>
        <div class="progress-bar bg-danger d-flex align-items-center justify-content-center"
            style="width: <?php echo e($stats['overduePercent']); ?>%">
            <span class="d-flex align-items-center">
                <i class="fas fa-clock"></i><span class="ml-1"><?php echo e($stats['overduePercent']); ?>%</span>
            </span>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH D:\my_apache\application\resources\views/pages/client/components/misc/expectation-stats.blade.php ENDPATH**/ ?>