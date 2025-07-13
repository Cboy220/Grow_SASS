<div id="analysis-content">
    <div class="team-weekly-report-analysis">
        <div class="card mb-3">
            <div class="card-header bg-light text-dark border-bottom">
                <h6 style="font-weight:600;"><i class="fas fa-calendar-week"></i> Team Weekly Report</h6>
            </div>
            <div class="card-body">
                <h5>Completed Tasks (last week)</h5>
                <ul>
                    @forelse($completedTasks as $task)
                        <li>{{ $task->task_title }} <span class="text-muted">({{ $task->task_updated }})</span></li>
                    @empty
                        <li class="text-muted">None</li>
                    @endforelse
                </ul>
                <h5>In Progress Tasks</h5>
                <ul>
                    @forelse($inProgressTasks as $task)
                        <li>{{ $task->task_title }} <span class="text-muted">({{ $task->task_updated }})</span></li>
                    @empty
                        <li class="text-muted">None</li>
                    @endforelse
                </ul>
                <h5>Overdue Tasks</h5>
                <ul>
                    @forelse($overdueTasks as $task)
                        <li>{{ $task->task_title }} <span class="text-muted">(Due: {{ $task->task_date_due }})</span></li>
                    @empty
                        <li class="text-muted">None</li>
                    @endforelse
                </ul>
                <button class="ai-analyze-btn btn btn-sm p-0 px-2" style="background:none;border:none;box-shadow:none;color:#007bff;cursor:pointer;transition:color 0.2s;" data-url="{{ route('team.analyze.ai.ai.weekly_report', ['team_id' => $member->id]) }}" onmouseover="this.style.color='#0056b3'" onmouseout="this.style.color='#007bff'">
                    <i class="fas fa-magic"></i> Run AI Analysis
                </button>
                <div class="ai-analysis-result mt-4"></div>
            </div>
        </div>
    </div>
</div> 