<div class="card mb-3">
    <div class="card-header bg-info text-white">
        <h6><i class="fas fa-robot"></i> AI Scoring Analysis</h6>
    </div>
    <div class="card-body">
        <?php if(!empty($aiAnalysisMarkdown)): ?>
            <div class="alert alert-success mb-0">
                <h6><i class="fas fa-check-circle"></i> AI Analysis Complete</h6>
                <div class="mt-3">
                    <div class="ai-analysis-content d-none"><?php echo e($aiAnalysisMarkdown); ?></div>
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
</div> <?php /**PATH D:\Grow_SaaS\application\resources\views/pages/leads/components/modals/tabs/scoring_ai.blade.php ENDPATH**/ ?>