 <?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h2 class="mb-4">
        <i class="fas fa-user-tie mr-2"></i> Client AI Analysis
    </h2>

    
    <div id="ai-summary" class="alert alert-primary shadow-sm fade show rounded">
        <h5 class="mb-2">
            <i class="fas fa-brain mr-2 text-info"></i> AI Insight Summary
        </h5>
        <p class="mb-0" id="summaryContent">
            <i class="fas fa-spinner fa-spin text-muted"></i> Loading client insights...
        </p>
    </div>

    
    <div class="card mb-4 shadow border-0">
        <div class="card-header bg-dark text-white d-flex align-items-center">
            <i class="fas fa-comments mr-2"></i> Ask AI About This Client
        </div>
        <div class="card-body bg-light">
            <div class="input-group">
                <textarea id="clientQuestion" class="form-control" rows="2" placeholder="Ask about satisfaction, payments, projects, etc..."></textarea>
                <div class="input-group-append">
                    <button id="askAI" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Ask
                    </button>
                </div>
            </div>
            <div id="aiAnswer" class="mt-3 alert alert-light d-none shadow-sm">
                <i class="fas fa-robot text-info mr-2"></i><span class="answer-text"></span>
            </div>
        </div>
    </div>

    
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    const initAnalayze = function () {
        const clientId = <?php echo e($clientId ?? '3'); ?>;
        const url = <?php echo e(route('clientai.analyze', 3)); ?>;

        $.ajax({
            url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if ( res.success ) {
                    $('#summaryContent').html(`<i class="fas fa-check-circle text-success mr-1"></i> ${res.result}`);
                } else {
                    NX.notification({
                        type: error,
                        message: res.message
                    })
                }
            },
            error: function (err) {
                NX.notification({
                    type: 'error',
                    message: err.message
                });
                console.log(err);
            }
        })
    };
$(document).ready(function () {
    const clientId = <?php echo e($clientId ?? '3'); ?>;

    initAnalayze();

    // Ask AI a question
    $('#askAI').on('click', function () {
        const question = $('#clientQuestion').val().trim();
        if (!question) return;

        $('#aiAnswer').removeClass('d-none').html(`<i class="fas fa-spinner fa-spin text-muted mr-2"></i> Thinking...`);

        $.post(`/client-ai/ask`, {
            question,
            client_id: clientId,
            _token: "<?php echo e(csrf_token()); ?>"
        }, function (res) {
            $('#aiAnswer').html(`<i class="fas fa-robot text-info mr-2"></i>${res.answer}`);
        }).fail(function () {
            $('#aiAnswer').html(`<i class="fas fa-times-circle text-danger mr-2"></i> Error reaching AI.`);
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/pages/clientai/index.blade.php ENDPATH**/ ?>