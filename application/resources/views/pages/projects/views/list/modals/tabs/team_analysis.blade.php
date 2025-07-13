<!-- Team Analysis Tab -->
<div class="team-analysis">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $teamMembers->count() }}</h4>
                    <p class="mb-0">Team Members</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $overloadedMembers->count() }}</h4>
                    <p class="mb-0">Overloaded</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $unassignedMembers->count() }}</h4>
                    <p class="mb-0">Unassigned</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="text-white font-weight-bold">{{ $teamMembers->count() - $overloadedMembers->count() - $unassignedMembers->count() }}</h4>
                    <p class="mb-0">Balanced</p>
                </div>
            </div>
        </div>
    </div>

   
    <!-- Team Workload Breakdown -->
    <div class="team-workload">
        @if($overloadedMembers->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-warning text-white">
                <h6><i class="fas fa-exclamation-triangle"></i> Overloaded Team Members ({{ $overloadedMembers->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($overloadedMembers as $member)
                    @php
                        $activeTasksCount = $member->active_tasks_count ?? 0;
                        $totalTasksCount = $member->total_tasks_count ?? 0;
                    @endphp
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ getUsersAvatar($member->avatar_directory ?? '', $member->avatar_filename ?? '') }}" class="rounded-circle mr-3" width="40" height="40" alt="{{ $member->first_name ?? '' }}">
                                <div>
                                    <h6 class="mb-1">{{ $member->first_name ?? '' }} {{ $member->last_name ?? '' }}</h6>
                                    <small class="text-muted">{{ $member->email ?? '' }}</small>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-warning">{{ $activeTasksCount }} Active Tasks</span>
                                <br>
                                <small class="text-muted">Total: {{ $totalTasksCount }} tasks</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @if($unassignedMembers->count() > 0)
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h6><i class="fas fa-user-plus"></i> Unassigned Team Members ({{ $unassignedMembers->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($unassignedMembers as $member)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ getUsersAvatar($member->avatar_directory ?? '', $member->avatar_filename ?? '') }}" class="rounded-circle mr-3" width="40" height="40" alt="{{ $member->first_name ?? '' }}">
                                <div>
                                    <h6 class="mb-1">{{ $member->first_name ?? '' }} {{ $member->last_name ?? '' }}</h6>
                                    <small class="text-muted">{{ $member->email ?? '' }}</small>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-info">No Tasks Assigned</span>
                                <br>
                                <small class="text-muted">Available for assignment</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- All Team Members -->
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <h6><i class="fas fa-users"></i> All Team Members ({{ $teamMembers->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($teamMembers as $member)
                    @php
                        $activeTasksCount = $member->active_tasks_count ?? 0;
                        $totalTasksCount = $member->total_tasks_count ?? 0;
                        $isOverloaded = $activeTasksCount > 5;
                        $isUnassigned = $totalTasksCount == 0;
                    @endphp
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ getUsersAvatar($member->avatar_directory ?? '', $member->avatar_filename ?? '') }}" class="rounded-circle mr-3" width="40" height="40" alt="{{ $member->first_name ?? '' }}">
                                <div>
                                    <h6 class="mb-1">{{ $member->first_name ?? '' }} {{ $member->last_name ?? '' }}</h6>
                                    <small class="text-muted">{{ $member->email ?? '' }}</small>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($isOverloaded)
                                    <span class="badge badge-warning">{{ $activeTasksCount }} Active Tasks</span>
                                @elseif($isUnassigned)
                                    <span class="badge badge-info">No Tasks</span>
                                @else
                                    <span class="badge badge-success">{{ $activeTasksCount }} Active Tasks</span>
                                @endif
                                <br>
                                <small class="text-muted">Total: {{ $totalTasksCount }} tasks</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
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
                    <button class="btn btn-sm btn-primary ai-analysis-btn" data-toggle="tooltip" data-placement="top" title="Generate AI-powered analysis" onclick="generateAIAnalysis('team', {{ $project->project_id }})">
                        <i class="fas fa-magic"></i> Generate AI Analysis
                    </button>
                </div>
                <div id="ai-response-team" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-spinner fa-spin"></i> Generating analysis...
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

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
</script> 

<script>
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