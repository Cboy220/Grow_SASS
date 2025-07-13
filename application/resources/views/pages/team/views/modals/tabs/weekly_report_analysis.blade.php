<div class="ai-analysis-section mt-3">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h6><i class="fas fa-robot"></i> AI Analysis</h6>
        </div>
        <div class="card-body">
            <div id="ai-response-team">
                @if(!empty($aiAnalysis))
                    <div class="alert alert-info mb-0">
                        <div class="mt-3">
                            <div class="ai-analysis-content d-none">{{ $aiAnalysisMarkdown ?? $aiAnalysis }}</div>
                            <div class="ai-analysis-html"></div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Generating analysis...</div>
                @endif
            </div>
        </div>
    </div>
</div>
