<div id="analysis-content">
    <div class="team-productivity-analysis">
        <div class="card mb-3">
            <div class="card-header bg-light text-dark border-bottom">
                <h6 style="font-weight:600;"><i class="fas fa-chart-line"></i> Productivity Overview</h6>
            </div>
            <div class="card-body">
                <h5>Productivity Metrics (last week)</h5>
                <ul>
                    <?php $__empty_1 = true; $__currentLoopData = $productivityMetrics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metric): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li><?php echo e($metric['label']); ?>: <span class="text-info"><?php echo e($metric['value']); ?></span></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li class="text-muted">No productivity data available.</li>
                    <?php endif; ?>
                </ul>
                <button class="ai-analyze-btn btn btn-sm p-0 px-2" style="background:none;border:none;box-shadow:none;color:#007bff;cursor:pointer;transition:color 0.2s;" data-url="<?php echo e(route('team.analyze.ai.ai.productivity', ['team_id' => $member->id])); ?>" onmouseover="this.style.color='#0056b3'" onmouseout="this.style.color='#007bff'">
                    <i class="fas fa-magic"></i> Run AI Analysis
                </button>
                <div class="ai-analysis-result mt-3"></div>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\Grow_SaaS\application\resources\views/pages/team/views/modals/tabs/productivity_analysis_base.blade.php ENDPATH**/ ?>