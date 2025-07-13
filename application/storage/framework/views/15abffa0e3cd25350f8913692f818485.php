<div id="analysis-content">
    <div class="team-weekly-report-analysis">
        <div class="card mb-3">
            <div class="card-header bg-light text-dark border-bottom">
                <h6 style="font-weight:600;"><i class="fas fa-calendar-week"></i> Team Weekly Report</h6>
            </div>
            <div class="card-body">
                <h5>Completed Tasks (last week)</h5>
                <ul>
                    <?php $__empty_1 = true; $__currentLoopData = $completedTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li><?php echo e($task->task_title); ?> <span class="text-muted">(<?php echo e($task->task_updated); ?>)</span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-muted">None</li>
                    <?php endif; ?>
                </ul>
                <h5>In Progress Tasks</h5>
                <ul>
                    <?php $__empty_1 = true; $__currentLoopData = $inProgressTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li><?php echo e($task->task_title); ?> <span class="text-muted">(<?php echo e($task->task_updated); ?>)</span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-muted">None</li>
                    <?php endif; ?>
                </ul>
                <h5>Overdue Tasks</h5>
                <ul>
                    <?php $__empty_1 = true; $__currentLoopData = $overdueTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li><?php echo e($task->task_title); ?> <span class="text-muted">(Due: <?php echo e($task->task_date_due); ?>)</span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-muted">None</li>
                    <?php endif; ?>
                </ul>
                <button class="ai-analyze-btn btn btn-sm p-0 px-2" style="background:none;border:none;box-shadow:none;color:#007bff;cursor:pointer;transition:color 0.2s;" data-url="<?php echo e(route('team.analyze.ai.ai.weekly_report', ['team_id' => $member->id])); ?>" onmouseover="this.style.color='#0056b3'" onmouseout="this.style.color='#007bff'">
                    <i class="fas fa-magic"></i> Run AI Analysis
                </button>
                <div class="ai-analysis-result mt-4"></div>
            </div>
        </div>
    </div>
</div> <?php /**PATH D:\Grow_SaaS\application\resources\views/pages/team/views/modals/tabs/weekly_report_analysis_base.blade.php ENDPATH**/ ?>