<?php use Illuminate\Support\Str; ?>
<div class="ai-analysis-result">
    <div class="card mb-3">
        <div class="card-header bg-info text-white">
            <h6><i class="fas fa-robot"></i> AI Productivity Analysis</h6>
        </div>
        <div class="card-body">
            <?php if(!empty($aiAnalysisMarkdown)): ?>
                <div class="alert alert-success mb-0">
                    <h6><i class="fas fa-check-circle"></i> AI Analysis Complete</h6>
                    <div class="mt-3">
                        <div class="ai-analysis-content d-none"><?php echo $aiAnalysisMarkdown; ?></div>
                        <div class="ai-analysis-html" style="font-size: 14px; line-height: 1.6;"></div>
                    </div>
                </div>
            <?php elseif(!empty($aiAnalysisError)): ?>
                <div class="alert alert-danger mb-0">
                    <h6><i class="fas fa-exclamation-triangle"></i> AI Analysis Failed</h6>
                    <p><?php echo e($aiAnalysisError); ?></p>
                </div>
            <?php else: ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-spinner fa-spin"></i> Generating analysis...
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH D:\Grow_SaaS\application\resources\views/pages/team/views/modals/tabs/productivity_analysis_ai.blade.php ENDPATH**/ ?>