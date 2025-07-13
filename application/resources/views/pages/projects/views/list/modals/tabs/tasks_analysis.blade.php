<!-- Tasks Analysis Tab -->
<div class="tasks-analysis">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $tasks->count() }}</h4>
                    <p class="mb-0">Total Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $overdueTasks->count() }}</h4>
                    <p class="mb-0">Overdue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $upcomingDeadlines->count() }}</h4>
                    <p class="mb-0">Due Soon</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $criticalTasks->count() }}</h4>
                    <p class="mb-0">Critical</p>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Task Breakdown -->
    <div class="task-breakdown">
        @if($overdueTasks->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <h6><i class="fas fa-exclamation-triangle"></i> Overdue Tasks ({{ $overdueTasks->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($overdueTasks->take(5) as $task)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $task->task_title }}</h6>
                                <small class="text-muted">
                                    Due: {{ $task->task_date_due ? (\Carbon\Carbon::parse($task->task_date_due)->format('M d, Y')) : 'No due date' }}
                                    @if($task->task_date_due)
                                        <span class="badge badge-danger ml-2">
                                            {{ now()->diffInDays($task->task_date_due) }} days overdue
                                        </span>
                                    @endif
                                </small>
                            </div>
                            <div>
                                <span class="badge badge-{{ $task->task_priority == 'high' ? 'danger' : ($task->task_priority == 'medium' ? 'warning' : 'info') }}">
                                    {{ ucfirst($task->task_priority) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($upcomingDeadlines->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-warning text-white">
                <h6><i class="fas fa-clock"></i> Upcoming Deadlines ({{ $upcomingDeadlines->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($upcomingDeadlines->take(5) as $task)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $task->task_title }}</h6>
                                <small class="text-muted">
                                    Due: {{ $task->task_date_due ? (\Carbon\Carbon::parse($task->task_date_due)->format('M d, Y')) : 'No due date' }}
                                    @if($task->task_date_due)
                                        <span class="badge badge-warning ml-2">
                                            Due in {{ now()->diffInDays($task->task_date_due, false) }} days
                                        </span>
                                    @endif
                                </small>
                            </div>
                            <div>
                                <span class="badge badge-{{ $task->task_priority == 'high' ? 'danger' : ($task->task_priority == 'medium' ? 'warning' : 'info') }}">
                                    {{ ucfirst($task->task_priority) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($criticalTasks->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6><i class="fas fa-star"></i> Critical Tasks ({{ $criticalTasks->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($criticalTasks->take(5) as $task)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $task->task_title }}</h6>
                                <small class="text-muted">
                                    Status: {{ ucfirst($task->task_status) }}
                                    @if($task->task_date_due)
                                        | Due: {{ $task->task_date_due ? (\Carbon\Carbon::parse($task->task_date_due)->format('M d, Y')) : '' }}
                                    @endif
                                </small>
                            </div>
                            <div>
                                <span class="badge badge-danger">High Priority</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
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
                    <textarea class="form-control" rows="8" readonly>{{ $aiPrompt }}</textarea>
                    <small class="text-muted">This prompt will be sent to OpenAI for analysis</small>
                </div>
                <div class="mt-3">
                    <button class="btn btn-sm btn-primary ai-analysis-btn" data-toggle="tooltip" data-placement="top" title="Generate AI-powered analysis" onclick="generateAIAnalysis('tasks', {{ $project->project_id }})">
                        <i class="fas fa-magic"></i> Generate AI Analysis
                    </button>
                </div>
                <div id="ai-response-tasks" class="mt-3" style="display: none;">
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
</script> 