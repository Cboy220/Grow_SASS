<div class="modal-dialog modal-lg" id="basicModalContainer">

  <div class="modal-content">
    <div class="modal-header" id="basicModalHeader">
      <h3 class="modal-title"><i class="fa-solid fa-wand-magic-sparkles"></i><span>AI Project Analysis - {{ $project->project_title ?? 'Project' }}</span></h3>
      <button type="button" class="close" data-dismiss="modal"
          id="basicModalCloseIcon">
          <i class="ti-close"></i>
      </button>
    </div>
    <div class="modal-body" id="basicModalBody">
      <div class="container">
        <!-- Analysis Type Tabs -->
        <ul class="nav nav-tabs" id="aiAnalysisTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active js-ajax-ux-request" id="tasks-tab" data-toggle="tab" href="#analysis-content" role="tab" 
               data-url="{{ url('/projects/'.$project->project_id.'/analyze-ai/tasks') }}"
               data-ajax-type="GET"
               data-loading-target="analysis-content"
               data-loading-class="loading">
               <i class="fas fa-tasks"></i> Tasks Analysis
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-ajax-ux-request" id="team-tab" data-toggle="tab" href="#analysis-content" role="tab"
               data-url="{{ url('/projects/'.$project->project_id.'/analyze-ai/team') }}"
               data-ajax-type="GET"
               data-loading-target="analysis-content"
               data-loading-class="loading">
               <i class="fas fa-users"></i> Team Analysis
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-ajax-ux-request" id="billing-tab" data-toggle="tab" href="#analysis-content" role="tab"
               data-url="{{ url('/projects/'.$project->project_id.'/analyze-ai/billing') }}"
               data-ajax-type="GET"
               data-loading-target="analysis-content"
               data-loading-class="loading">
               <i class="fas fa-dollar-sign"></i> Billing Analysis
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
              <p class="mt-2">Analyzing project tasks...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Load initial tasks analysis
    var initialTab = $('#tasks-tab');
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
