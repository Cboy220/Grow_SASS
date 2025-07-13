<!-- Billing Analysis Tab -->
<div class="billing-analysis">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">$<?php echo e(number_format($invoices->sum('bill_final_amount'), 2)); ?></h4>
                    <p class="mb-0">Total Invoiced</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">$<?php echo e(number_format($invoices->where('bill_status', 'paid')->sum('bill_final_amount'), 2)); ?></h4>
                    <p class="mb-0">Total Paid</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold"><?php echo e(round($unbilledHours / 3600, 2)); ?></h4>
                    <p class="mb-0">Unbilled Hours</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold"><?php echo e($pendingEstimates->count() + $pendingContracts->count()); ?></h4>
                    <p class="mb-0">Pending Items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Status Breakdown -->
    <div class="billing-breakdown">
        <!-- Invoices -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6><i class="fas fa-file-invoice-dollar"></i> Invoices (<?php echo e($invoices->count()); ?>)</h6>
            </div>
            <div class="card-body">
                <?php if($invoices->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $invoices->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($invoice->bill_invoiceid); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($invoice->bill_status == 'paid' ? 'success' : ($invoice->bill_status == 'overdue' ? 'danger' : 'warning')); ?>">
                                        <?php echo e(ucfirst($invoice->bill_status)); ?>

                                    </span>
                                </td>
                                <td>$<?php echo e(number_format($invoice->bill_final_amount, 2)); ?></td>
                                <td><?php echo e(runtimeDate($invoice->bill_date)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No invoices found for this project.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estimates -->
        <?php if($estimates->count() > 0): ?>
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6><i class="fas fa-calculator"></i> Estimates (<?php echo e($estimates->count()); ?>)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Estimate #</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $estimates->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estimate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($estimate->bill_estimateid); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($estimate->bill_status == 'approved' ? 'success' : ($estimate->bill_status == 'pending' ? 'warning' : 'secondary')); ?>">
                                        <?php echo e(ucfirst($estimate->bill_status)); ?>

                                    </span>
                                </td>
                                <td>$<?php echo e(number_format($estimate->bill_final_amount, 2)); ?></td>
                                <td><?php echo e(runtimeDate($estimate->bill_date)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Contracts -->
        <?php if($contracts->count() > 0): ?>
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <h6><i class="fas fa-file-contract"></i> Contracts (<?php echo e($contracts->count()); ?>)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Contract #</th>
                                <th>Title</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $contracts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(url('/contracts/'.$contract->doc_id)); ?>" class="text-info">
                                        <?php echo e(runtimeContractIdFormat($contract->doc_id)); ?>

                                    </a>
                                </td>
                                <td><?php echo e($contract->doc_title ?? '---'); ?></td>
                                <td><?php echo e(runtimeDate($contract->doc_date_start)); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($contract->doc_status == 'active' ? 'success' : ($contract->doc_status == 'awaiting_signatures' ? 'warning' : 'secondary')); ?>">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $contract->doc_status))); ?>

                                    </span>
                                </td>
                                <td>$<?php echo e(number_format($contract->doc_value, 2)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Unbilled Hours -->
        <?php if($unbilledHours > 0): ?>
        <div class="card mb-3">
            <div class="card-header bg-warning text-white">
                <h6><i class="fas fa-clock"></i> Unbilled Hours (<?php echo e(round($unbilledHours / 3600, 2)); ?> hours)</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Unbilled Time:</strong> There are <?php echo e(round($unbilledHours / 3600, 2)); ?> hours of unbilled time for this project.
                    <?php if($project->project_billing_rate): ?>
                        <br>Estimated value: $<?php echo e(number_format(($unbilledHours / 3600) * $project->project_billing_rate, 2)); ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Items -->
        <?php if($pendingEstimates->count() > 0 || $pendingContracts->count() > 0): ?>
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6><i class="fas fa-hourglass-half"></i> Pending Items</h6>
            </div>
            <div class="card-body">
                <?php if($pendingEstimates->count() > 0): ?>
                <div class="mb-3">
                    <h6>Pending Estimates (<?php echo e($pendingEstimates->count()); ?>)</h6>
                    <ul class="list-group">
                        <?php $__currentLoopData = $pendingEstimates->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estimate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Estimate #<?php echo e($estimate->bill_estimateid); ?>

                            <span class="badge badge-primary badge-pill">$<?php echo e(number_format($estimate->bill_final_amount, 2)); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if($pendingContracts->count() > 0): ?>
                <div>
                    <h6>Pending Contracts (<?php echo e($pendingContracts->count()); ?>)</h6>
                    <ul class="list-group">
                        <?php $__currentLoopData = $pendingContracts->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contract): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Contract #<?php echo e($contract->doc_id); ?>

                            <span class="badge badge-primary badge-pill">$<?php echo e(number_format($contract->doc_value, 2)); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- AI Analysis Section -->
    <div class="ai-analysis-section mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-robot text-primary"></i> AI Analysis</h5>
            </div>
            <div class="card-body">
                <!-- Hide prompt area -->
                <div class="ai-prompt-area d-none">
                    <textarea class="form-control" rows="8" readonly><?php echo e($aiPrompt); ?></textarea>
                    <small class="text-muted">This prompt will be sent to OpenAI for analysis</small>
                </div>
                <div class="mt-3">
                    <button class="btn btn-sm btn-primary ai-analysis-btn" data-toggle="tooltip" data-placement="top" title="Generate AI-powered analysis" onclick="generateAIAnalysis('billing', <?php echo e($project->project_id); ?>)">
                        <i class="fas fa-magic"></i> Generate AI Analysis
                    </button>
                </div>
                <div id="ai-response-billing" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-spinner fa-spin"></i> Generating analysis...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ai-analysis-btn {
    background: none !important;
    border: none !important;
    color: #007bff;
    box-shadow: none !important;
    outline: none !important;
    transition: color 0.2s, background 0.2s;
}
.ai-analysis-btn:hover, .ai-analysis-btn:focus {
    color: #fff;
    background: #007bff !important;
    border-radius: 4px;
}
</style>
<script>
function generateAIAnalysis(type, projectId) {
    const responseDiv = document.getElementById('ai-response-' + type);
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    
    // Show loading state
    responseDiv.style.display = 'block';
    responseDiv.innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-spinner fa-spin"></i> Generating AI analysis...
        </div>
    `;
    
    // Disable button
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    
    // Determine endpoint based on type
    let endpoint;
    switch(type) {
        case 'tasks':
            endpoint = `/projects/${projectId}/generate-ai-tasks-analysis`;
            break;
        case 'team':
            endpoint = `/projects/${projectId}/generate-ai-team-analysis`;
            break;
        case 'billing':
            endpoint = `/projects/${projectId}/generate-ai-billing-analysis`;
            break;
        default:
            endpoint = `/projects/${projectId}/generate-ai-tasks-analysis`;
    }
    
    // Make AJAX request
    $.ajax({
        url: endpoint,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                // Convert Markdown to HTML using marked.parse()
                const htmlContent = marked.parse(response.analysis);
                responseDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> AI Analysis Complete</h6>
                        <div class="mt-3">
                            <div class="ai-analysis-content" style="font-size: 14px; line-height: 1.6;">
                                ${htmlContent}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                responseDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> AI Analysis Failed</h6>
                        <p>${response.message}</p>
                    </div>
                `;
            }
        },
        error: function(xhr, status, error) {
            responseDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> AI Analysis Failed</h6>
                    <p>An error occurred while generating the analysis. Please try again.</p>
                </div>
            `;
        },
        complete: function() {
            // Re-enable button
            button.disabled = false;
            button.innerHTML = originalText;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var modalBody = document.querySelector('.modal-body');
    if (modalBody) {
        modalBody.style.overflowY = 'auto';
        modalBody.style.minWidth = '80vh';
    }
    // Enable Bootstrap tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
});
</script> <?php /**PATH D:\Grow_SaaS\application\resources\views/pages/projects/views/list/modals/tabs/billing_analysis.blade.php ENDPATH**/ ?>