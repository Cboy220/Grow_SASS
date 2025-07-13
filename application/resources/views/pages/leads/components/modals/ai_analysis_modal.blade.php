<div class="modal-dialog modal-xl" id="basicModalContainer">
    <div class="modal-content">
        <div class="modal-header bg-light text-dark border-bottom">
            <h5 class="modal-title"><i class="fa-solid fa-wand-magic-sparkles"></i> Lead AI Analysis</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body p-0">
            <ul class="nav nav-tabs nav-tabs-bottom border-bottom-0" role="tablist">
                <li class="nav-item">
                    <a href="javascript:void(0)" class="nav-link active js-ajax-ux-request js-lead-ai-tab" data-url="{{ route('leads.analyze.ai.tab.analysis', ['lead_id' => $lead->lead_id]) }}" data-ajax-type="GET" data-loading-target="analysis-content" data-loading-class="loading">Analysis</a>
                </li>
                <li class="nav-item">
                    <a href="javascript:void(0)" class="nav-link js-ajax-ux-request js-lead-ai-tab" data-url="{{ route('leads.analyze.ai.tab.scoring', ['lead_id' => $lead->lead_id]) }}" data-ajax-type="GET" data-loading-target="analysis-content" data-loading-class="loading">Scoring & Suggestions</a>
                </li>
            </ul>
            <div id="analysis-content" class="p-3">
                <!-- Tab content will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>
{{-- postrun_functions: ["initLeadAIModalEvents"] --}} 