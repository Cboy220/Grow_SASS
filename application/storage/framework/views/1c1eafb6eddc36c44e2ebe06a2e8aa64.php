<div class="container mt-5">
    <h2 class="mb-4">
        <i class="fas fa-user-tie mr-2"></i> <?php echo e(__('lang.client_ai_analysis')); ?>

    </h2>

    
    <div id="ai-summary" class="alert alert-primary shadow-sm fade show rounded">
        <h5 class="mb-2">
            <i class="fas fa-brain mr-2 text-info"></i> <?php echo e(__('lang.ai_insight_summary')); ?>

        </h5>
        <p class="mb-0" id="summaryContent">
            <i class="fas fa-spinner fa-spin text-muted"></i> <?php echo e(__('lang.loading_client_insights')); ?>

        </p>
    </div>

    
    <div class="card mb-4 shadow border-0">
        <div class="card-header bg-dark text-white d-flex align-items-center">
            <i class="fas fa-comments mr-2"></i> <?php echo e(__('lang.ask_ai_about_client')); ?>

        </div>
        <div class="card-body bg-light">
            <div class="input-group">
                <textarea id="clientQuestion" class="form-control" rows="2" placeholder="<?php echo e(__('lang.ask_ai_placeholder')); ?>"></textarea>
                <div class="input-group-append">
                    <button id="askAI" class="btn btn-primary" data-init-url="<?php echo e(route('clientai.analyze', $client->client_id)); ?>" data-action-url="<?php echo e(route('clientai.ask', $client->client_id)); ?>">
                        <i class="fas fa-paper-plane"></i> <?php echo e(__('lang.ask')); ?>

                    </button>
                </div>
            </div>
            <div id="aiAnswer" class="mt-3 alert alert-light d-none shadow-sm">
                <i class="fas fa-robot text-info mr-2"></i><span class="answer-text"></span>
            </div>
        </div>
    </div>

    
</div>
<?php echo $__env->make('pages.client.components.tabs.clientai.js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/client/components/tabs/clientai/index.blade.php ENDPATH**/ ?>