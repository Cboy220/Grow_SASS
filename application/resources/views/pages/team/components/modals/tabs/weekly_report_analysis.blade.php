<div class="card-body">
    <h5 class="mb-3"><i class="fa-solid fa-calendar-week"></i> Weekly Report Analysis</h5>
    <div class="ai-analysis-result">
        <!-- AI analysis result will be loaded here -->
    </div>
    <button type="button"
            class="btn btn-info ai-analyze-btn mt-3"
            data-url="{{ route('team.analyze.ai.weekly_report.ai', ['team_id' => $team->team_id]) }}">
        <i class="fas fa-robot"></i> Run AI Analysis
    </button>
</div> 