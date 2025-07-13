<?php
namespace App\Repositories;

use App\Models\User;
use League\CommonMark\CommonMarkConverter;

class TeamRepository
{
    /**
     * Generate AI prompt for a team member's weekly report and general alerts
     * @param int $teamId
     * @return string
     */
    public function generateMemberWeeklyReportPrompt($teamId)
    {
        $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return "No such team member found.";
        }
        $now = now();
        $oneWeekAgo = $now->copy()->subWeek();
        $prompt = "# Weekly Report for {$member->full_name}\n\n";
        $completedTasks = $member->assignedTasks()
            ->where('task_status', 'completed')
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();
        $inProgressTasks = $member->assignedTasks()
            ->where('task_status', 'in_progress')
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();
        $overdueTasks = $member->assignedTasks()
            ->where('task_date_due', '<', $now)
            ->where('task_status', '!=', 'completed')
            ->get();
        $prompt .= "- **Completed Tasks (last week):**\n";
        if ($completedTasks->count()) {
            foreach ($completedTasks as $task) {
                $prompt .= "  - {$task->task_title} ({$task->task_updated})\n";
            }
        } else {
            $prompt .= "  - None\n";
        }
        $prompt .= "- **In Progress Tasks (last week):**\n";
        if ($inProgressTasks->count()) {
            foreach ($inProgressTasks as $task) {
                $prompt .= "  - {$task->task_title} (Due: {$task->task_date_due})\n";
            }
        } else {
            $prompt .= "  - None\n";
        }
        $prompt .= "- **Overdue Tasks:**\n";
        if ($overdueTasks->count()) {
            foreach ($overdueTasks as $task) {
                $prompt .= "  - {$task->task_title} (Due: {$task->task_date_due})\n";
            }
        } else {
            $prompt .= "  - None\n";
        }
        $prompt .= "\n## General Alerts\n";
        $prompt .= "- **No tasks in progress:** " . ($inProgressTasks->count() == 0 ? $member->full_name : 'None') . "\n";
        $bottleneckCount = $overdueTasks->count() + $inProgressTasks->count();
        $prompt .= "- **Bottlenecks:** " . ($bottleneckCount > 5 ? $member->full_name . " ({$bottleneckCount} tasks)" : 'None') . "\n";
        return $prompt;
    }

    /**
     * Generate AI prompt for a team member's general alerts
     * @param int $teamId
     * @return string
     */
    public function generateMemberGeneralAlertsPrompt($teamId)
    {
        $member = \App\Models\User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return "No such team member found.";
        }
        // Example: You can customize this logic to gather bottlenecks, overdue tasks, etc.
        $prompt = "# General Alerts for {$member->full_name}\n\n";
        $overdueTasks = $member->assignedTasks()->where('task_status', 'overdue')->get();
        $noTasksInProgress = $member->assignedTasks()->where('task_status', '!=', 'in_progress')->count() == 0;
        $prompt .= "## Overdue Tasks\n";
        if ($overdueTasks->count()) {
            foreach ($overdueTasks as $task) {
                $prompt .= "- {$task->title} (Due: {$task->due_date})\n";
            }
        } else {
            $prompt .= "None.\n";
        }
        $prompt .= "\n## No Tasks In Progress\n";
        $prompt .= $noTasksInProgress ? "This member has no tasks in progress.\n" : "This member has tasks in progress.\n";
        return $prompt;
    }

    /**
     * Get base data for a team member's weekly report (non-AI)
     * @param int $teamId
     * @return array|null
     */
    public function getMemberWeeklyReportData($teamId)
    {
        $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return null;
        }
        $now = now();
        $oneWeekAgo = $now->copy()->subWeek();
        $completedTasks = $member->assignedTasks()
            ->where('task_status', 'completed')
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();
        $inProgressTasks = $member->assignedTasks()
            ->where('task_status', 'in_progress')
            ->where('task_updated', '>=', $oneWeekAgo)
            ->get();
        $overdueTasks = $member->assignedTasks()
            ->where('task_date_due', '<', $now)
            ->where('task_status', '!=', 'completed')
            ->get();
        return [
            'member' => $member,
            'completedTasks' => $completedTasks,
            'inProgressTasks' => $inProgressTasks,
            'overdueTasks' => $overdueTasks,
        ];
    }

    /**
     * Get base data for a team member's general alerts (non-AI)
     * @param int $teamId
     * @return array|null
     */
    public function getMemberGeneralAlertsData($teamId)
    {
        $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return null;
        }
        $overdueTasks = $member->assignedTasks()->where('task_status', 'overdue')->get();
        $noTasksInProgress = $member->assignedTasks()->where('task_status', 'in_progress')->count() == 0;
        return [
            'member' => $member,
            'overdueTasks' => $overdueTasks,
            'noTasksInProgress' => $noTasksInProgress,
        ];
    }

    public function getWeeklyReportAIAnalysis($teamId)
    {
        $prompt = $this->generateMemberWeeklyReportPrompt($teamId);
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an expert team performance analyst AI. Analyze the following weekly report and provide actionable insights in a clear, professional format.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        $aiAnalysisMarkdown = null;
        $aiAnalysisError = null;
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            $aiAnalysisMarkdown = $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            $aiAnalysisError = $e->getMessage();
        }
        return [
            'aiAnalysisMarkdown' => $aiAnalysisMarkdown,
            'aiAnalysisError' => $aiAnalysisError,
        ];
    }

    public function getGeneralAlertsAIAnalysis($teamId)
    {
        $prompt = $this->generateMemberGeneralAlertsPrompt($teamId);
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an expert team performance analyst AI. Analyze the following general alerts and provide actionable insights in a clear, professional format.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        $aiAnalysisMarkdown = null;
        $aiAnalysisError = null;
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            $aiAnalysisMarkdown = $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            $aiAnalysisError = $e->getMessage();
        }
        return [
            'aiAnalysisMarkdown' => $aiAnalysisMarkdown,
            'aiAnalysisError' => $aiAnalysisError,
        ];
    }

    /**
     * Get base data for a team member's productivity (non-AI)
     * @param int $teamId
     * @return array|null
     */
    public function getMemberProductivityData($teamId)
    {
        $member = User::where('type', 'team')->where('status', 'active')->where('id', $teamId)->first();
        if (!$member) {
            return null;
        }
        $now = now();
        $oneWeekAgo = $now->copy()->subWeek();
        $tasks = $member->assignedTasks()->where('task_updated', '>=', $oneWeekAgo)->get();
        $completed = $tasks->where('task_status', 'completed');
        $inProgress = $tasks->where('task_status', 'in_progress');
        $overdue = $tasks->where('task_status', '!=', 'completed')->where('task_date_due', '<', $now);
        $hoursWorked = $completed->sum('task_actual_hours');
        $avgCompletionTime = $completed->count() ? $completed->avg(function($task) {
            if ($task->task_started && $task->task_completed) {
                return strtotime($task->task_completed) - strtotime($task->task_started);
            }
            return null;
        }) : null;
        $metrics = [
            ['label' => 'Tasks Completed', 'value' => $completed->count()],
            ['label' => 'Tasks In Progress', 'value' => $inProgress->count()],
            ['label' => 'Overdue Tasks', 'value' => $overdue->count()],
            ['label' => 'Hours Worked', 'value' => round($hoursWorked, 1)],
        ];
        if ($avgCompletionTime) {
            $metrics[] = [
                'label' => 'Avg. Completion Time',
                'value' => gmdate('H:i:s', (int) $avgCompletionTime)
            ];
        }
        return [
            'member' => $member,
            'productivityMetrics' => $metrics,
        ];
    }

    /**
     * Get AI analysis for a team member's productivity
     * @param int $teamId
     * @return array
     */
    public function getProductivityAIAnalysis($teamId)
    {
        $data = $this->getMemberProductivityData($teamId);
        if (!$data) {
            return [
                'aiAnalysisMarkdown' => null,
                'aiAnalysisError' => 'Team member not found.'
            ];
        }
        $member = $data['member'];
        $metrics = $data['productivityMetrics'];
        $prompt = "# Productivity Report for {$member->full_name}\n\n";
        foreach ($metrics as $metric) {
            $prompt .= "- {$metric['label']}: {$metric['value']}\n";
        }
        $prompt .= "\nPlease analyze the above productivity metrics and provide actionable insights and suggestions for improvement in markdown format.";
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an expert productivity analyst AI. Analyze the following productivity metrics and provide actionable insights in a clear, professional format.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];
        $aiAnalysisMarkdown = null;
        $aiAnalysisError = null;
        try {
            $response = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => config('openai.model', 'gpt-3.5-turbo'),
                'messages' => $messages,
                'max_tokens' => 800,
                'temperature' => 0.7,
            ]);
            $aiAnalysisMarkdown = $response['choices'][0]['message']['content'] ?? '';
        } catch (\Exception $e) {
            $aiAnalysisError = $e->getMessage();
        }
        return [
            'aiAnalysisMarkdown' => $aiAnalysisMarkdown,
            'aiAnalysisError' => $aiAnalysisError,
        ];
    }
} 