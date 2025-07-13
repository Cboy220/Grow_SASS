@php use Illuminate\Support\Str; @endphp
<div class="card mb-3">
    <div class="card-header bg-info text-white">
        <h6><i class="fas fa-robot"></i> AI Weekly Report Analysis</h6>
    </div>
    <div class="card-body">
        @if(!empty($aiAnalysisMarkdown))
            <div class="alert alert-success mb-0">
                <h6><i class="fas fa-check-circle"></i> AI Analysis Complete</h6>
                <div class="mt-3">
                    <div class="ai-analysis-content d-none">{!! $aiAnalysisMarkdown !!}</div>
                    <div class="ai-analysis-html" style="font-size: 14px; line-height: 1.6;"></div>
                </div>
            </div>
        @elseif(!empty($aiAnalysisError))
            <div class="alert alert-danger mb-0">
                <h6><i class="fas fa-exclamation-triangle"></i> AI Analysis Failed</h6>
                <p>{{ $aiAnalysisError }}</p>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-spinner fa-spin"></i> Generating analysis...
            </div>
        @endif
    </div>
</div>