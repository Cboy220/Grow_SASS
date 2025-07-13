<div class="modal-dialog modal-xl" id="basicModalContainer">
    <div class="modal-content">
        <div class="modal-header" id="basicModalHeader">
            <h3 class="modal-title"><i class="fa-solid fa-wand-magic-sparkles"></i><span>AI Client Analysis - {{ $client->client_company_name ?? 'Client' }}</span></h3>
            <button type="button" class="close" data-dismiss="modal" id="basicModalCloseIcon">
                <i class="ti-close"></i>
            </button>
        </div>
        <div class="modal-body" id="basicModalBody">
            <div class="container">
                <!-- Analysis Type Tabs -->
                <ul class="nav nav-tabs" id="aiAnalysisTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active js-ajax-ux-request" id="feedback-tab" data-toggle="tab" href="#analysis-content" role="tab"
                           data-url="{{ route('clients.analyze.ai.feedback', $client->client_id) }}"
                           data-ajax-type="GET"
                           data-loading-target="analysis-content"
                           data-loading-class="loading">
                           <i class="fas fa-comments"></i> Feedback
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-ajax-ux-request" id="expectations-tab" data-toggle="tab" href="#analysis-content" role="tab"
                           data-url="{{ route('clients.analyze.ai.expectations', $client->client_id) }}"
                           data-ajax-type="GET"
                           data-loading-target="analysis-content"
                           data-loading-class="loading">
                           <i class="fas fa-bullseye"></i> Expectations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-ajax-ux-request" id="projects-tab" data-toggle="tab" href="#analysis-content" role="tab"
                           data-url="{{ route('clients.analyze.ai.projects', $client->client_id) }}"
                           data-ajax-type="GET"
                           data-loading-target="analysis-content"
                           data-loading-class="loading">
                           <i class="fas fa-tasks"></i> Projects
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-ajax-ux-request" id="comments-tab" data-toggle="tab" href="#analysis-content" role="tab"
                           data-url="{{ route('clients.analyze.ai.comments', $client->client_id) }}"
                           data-ajax-type="GET"
                           data-loading-target="analysis-content"
                           data-loading-class="loading">
                           <i class="fas fa-question-circle"></i> Comments
                        </a>
                    </li>
                </ul>
                <!-- Single Content Area -->
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="analysis-content" role="tabpanel">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2">Analyzing client feedback...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Load initial feedback analysis
    var initialTab = $('#feedback-tab');
    if (initialTab.length > 0) {
        nxAjaxUxRequest(initialTab);
    }
    // Handle tab clicks using framework
    $('#aiAnalysisTabs a[data-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        var $tab = $(this);
        // Remove active class from all tabs
        $('#aiAnalysisTabs a').removeClass('active');
        // Add active class to clicked tab
        $tab.addClass('active');
        // Show loading state in content area
        $('#analysis-content').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading analysis...</p>
            </div>
        `);
        // Trigger AJAX request
        nxAjaxUxRequest($tab);
    });
});
</script> 