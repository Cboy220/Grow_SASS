<div class="card-header bg-light text-dark border-bottom">
    <h6 style="font-weight:600;"><i class="fa-solid fa-chart-simple"></i> Lead Analysis</h6>
</div>
<div class="card-body">
    <!-- Lead Summary Predata -->
    <div class="mb-3">
        <h5 class="mb-2"><i class="fa-solid fa-user"></i> {{ $lead->full_name ?? ($lead->lead_firstname . ' ' . $lead->lead_lastname) }}</h5>
        <ul class="list-unstyled mb-2" style="font-size:15px;">
            <li><strong>Status:</strong> {{ $lead->leadstatus->leadstatus_title ?? 'N/A' }}</li>
            <li><strong>Category:</strong> {{ $lead->category->category_name ?? 'N/A' }}</li>
            <li><strong>Last Contacted:</strong> {{ $lead->carbon_last_contacted ?? ($lead->lead_last_contacted ?? '---') }}</li>
            <li><strong>Assigned Users:</strong> {{ $lead->assigned()->count() }}</li>
            <li><strong>Comments:</strong> {{ $lead->comments()->count() }}</li>
            <li><strong>Proposals:</strong> {{ $lead->proposals()->count() }}</li>
            <li><strong>Contracts:</strong> {{ $lead->contracts()->count() }}</li>
        </ul>
    </div>
    <!-- /Lead Summary Predata -->
    <button class="ai-analyze-btn btn btn-sm p-0 px-2" style="background:none;border:none;box-shadow:none;color:#007bff;cursor:pointer;transition:color 0.2s;" data-url="{{ route('leads.analyze.ai.ai.analysis', ['lead_id' => $lead->lead_id]) }}" onmouseover="this.style.color='#0056b3'" onmouseout="this.style.color='#007bff'">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Run AI Analysis
    </button>
    <div class="ai-analysis-result mt-3">
        <!-- AI result will be loaded here -->
    </div>
</div> 