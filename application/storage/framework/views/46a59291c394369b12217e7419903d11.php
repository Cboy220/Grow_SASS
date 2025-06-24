<ul class="list-group list-group-flush">
  <?php $__empty_1 = true; $__currentLoopData = $expectations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 ps-0">
          
          <div class="d-flex align-items-center flex-grow-1">
              <?php if($ex->status === 'fulfilled'): ?>
                  <i class="fas fa-check-circle text-info mr-2" style="font-size: 1.4rem;"></i>
              <?php else: ?>
                  <i class="far fa-circle text-secondary mr-2" style="font-size: 1.4rem;"></i>
              <?php endif; ?>
              <span><?php echo e($ex->title); ?></span>
          </div>

          
          <div class="d-flex align-items-center text-nowrap">
              <?php if($ex->due_date): ?>
                  <small class="text-muted mr-3">
                      <i class="far fa-calendar-alt mr-1"></i>
                      <?php echo e(\Carbon\Carbon::parse($ex->due_date)->format('Y-m-d')); ?>

                  </small>
              <?php endif; ?>

              <span class="badge badge-<?php echo e($ex->status === 'fulfilled' ? 'success' : 'warning'); ?>">
                  <?php echo e($ex->status); ?>

              </span>
          </div>
      </li>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <li class="list-group-item text-muted"><?php echo e(__('lang.no_expectations_found')); ?></li>
  <?php endif; ?>
</ul>


<div class="mt-4" id="paginationLinks">
  <?php echo $expectations->links(); ?>

</div>
<?php /**PATH D:\my_apache\application\resources\views/pages/expectation/components/misc/expectation-list.blade.php ENDPATH**/ ?>