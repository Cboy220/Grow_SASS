    <div class="modal-dialog modal-xl" id="basicModalContainer">
        <div class="modal-content">
            <div class="modal-header" id="basicModalHeader">
                <h3 class="modal-title">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    <span>AI Team Analysis - {{ $team->full_name ?? 'Team Member' }}</span>
                </h3>
                <button type="button" class="close" data-dismiss="modal" id="basicModalCloseIcon">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body" id="basicModalBody">
                <div class="container">
                    <!-- Analysis Type Tabs -->
                    <ul class="nav nav-tabs" id="aiAnalysisTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active js-ajax-ux-request" id="weekly-report-tab" data-toggle="tab"
                                href="#analysis-content" role="tab"
                                data-url="{{ route('team.analyze.ai.base.weekly_report', ['team_id' => $team->id ?? '']) }}"
                                data-ajax-type="GET" data-loading-target="analysis-content"
                                data-loading-class="loading">
                                <i class="fas fa-calendar-week"></i> Weekly Report
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-ajax-ux-request" id="general-alerts-tab" data-toggle="tab"
                                href="#analysis-content" role="tab"
                                data-url="{{ route('team.analyze.ai.base.general_alerts', ['team_id' => $team->id ?? '']) }}"
                                data-ajax-type="GET" data-loading-target="analysis-content"
                                data-loading-class="loading">
                                <i class="fas fa-exclamation-triangle"></i> General Alerts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-ajax-ux-request" id="productivity-tab" data-toggle="tab"
                                href="#analysis-content" role="tab"
                                data-url="{{ route('team.analyze.ai.base.productivity', ['team_id' => $team->id ?? '']) }}"
                                data-ajax-type="GET" data-loading-target="analysis-content"
                                data-loading-class="loading">
                                <i class="fas fa-chart-line"></i> Productivity
                            </a>
                        </li>
                    </ul>
                    <!-- Single Content Area -->
                    <div class="tab-content mt-3">
                        <div class="tab-pane show active" id="analysis-content" role="tabpanel">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Analyzing team member activity...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- No inline JS here; all tab logic should be in global JS -->
