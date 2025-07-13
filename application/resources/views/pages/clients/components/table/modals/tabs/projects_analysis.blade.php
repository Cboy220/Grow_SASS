<div class="projects-analysis">
    <div class="card mb-3">
        <div class="card-header bg-warning text-white">
            <h6><i class="fas fa-tasks"></i> Project Deadlines</h6>
        </div>
        <div class="card-body">
            @if(count($projectData['overdue']) > 0)
                <div class="alert alert-danger">Overdue projects:
                    <ul>
                        @foreach($projectData['overdue'] as $proj)
                            <li>{{ $proj->project_title }} (Due: {{ $proj->project_date_due }})</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="alert alert-success">No overdue projects.</div>
            @endif
            @if(count($projectData['upcoming']) > 0)
                <div class="alert alert-info">Projects with upcoming deadlines:
                    <ul>
                        @foreach($projectData['upcoming'] as $proj)
                            <li>{{ $proj->project_title }} (Due: {{ $proj->project_date_due }})</li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="alert alert-success">No projects with upcoming deadlines.</div>
            @endif
        </div>
    </div>
    <div class="ai-analysis-section mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-robot text-primary"></i> AI Analysis</h5>
            </div>
            <div class="card-body">
                <div class="ai-prompt-area d-none">
                    <textarea class="form-control" rows="8" readonly>{{ json_encode($projectData) }}</textarea>
                    <small class="text-muted">This prompt will be sent to OpenAI for analysis</small>
                </div>
                <div class="mt-3">
                    <button class="btn btn-sm btn-primary ai-analysis-btn" data-toggle="tooltip" data-placement="top" title="Generate AI-powered analysis" onclick="generateAIAnalysis('projects', {{ $client->client_id }})">
                        <i class="fas fa-magic"></i> Generate AI Analysis
                    </button>
                </div>
                <div id="ai-response-projects" class="mt-3" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-spinner fa-spin"></i> Generating analysis...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function generateAIAnalysis(type, clientId) {
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
        case 'feedback':
            endpoint = `/clients/${clientId}/generate-ai-feedback-analysis`;
            break;
        case 'expectations':
            endpoint = `/clients/${clientId}/generate-ai-expectations-analysis`;
            break;
        case 'projects':
            endpoint = `/clients/${clientId}/generate-ai-projects-analysis`;
            break;
        case 'comments':
            endpoint = `/clients/${clientId}/generate-ai-comments-analysis`;
            break;
        default:
            endpoint = `/clients/${clientId}/generate-ai-feedback-analysis`;
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