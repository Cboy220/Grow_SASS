<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AiAnalysisTestSeeder extends Seeder
{
    public function run()
    {
        // 1. Create a test client
        $client = \App\Models\Client::create([
            'client_company_name' => 'Test Company Inc.',
            'client_status' => 'active',
            'client_website' => 'https://testcompany.com',
            'client_vat' => '123456789',
            'client_billing_street' => '123 Test Street',
            'client_billing_city' => 'Test City',
            'client_billing_state' => 'Test State',
            'client_billing_zip' => '12345',
            'client_billing_country' => 'United States',
            'client_created' => now(),
            'client_updated' => now(),
            'client_creatorid' => 1,
            'client_categoryid' => 1,
            'client_description' => 'Test client for AI analysis',
            'client_importid' => Str::uuid(),
            'client_created_from_leadid' => 0,
        ]);

        // 2. Create a test project
        $project = \App\Models\Project::create([
            'project_title' => 'AI Analysis Test Project',
            'project_description' => 'This is a test project for testing the AI analysis feature with various data scenarios.',
            'project_clientid' => $client->client_id,
            'project_status' => 'in_progress',
            'project_progress' => 65,
            'project_date_start' => now()->subDays(30),
            'project_date_due' => now()->addDays(15),
            'project_billing_rate' => 150.00,
            'project_billing_estimated_hours' => 80,
            'project_billing_costs_estimate' => 12000.00,
            'project_active_state' => 'active',
            'project_created' => now(),
            'project_updated' => now(),
            'project_creatorid' => 1,
            'project_categoryid' => 1,
        ]);

        // 2b. Create milestones for the project
        $milestones = [];
        $milestoneData = [
            [
                'milestone_title' => 'Planning',
                'milestone_position' => 1,
                'milestone_type' => 'categorised',
                'milestone_color' => 'info',
            ],
            [
                'milestone_title' => 'Development',
                'milestone_position' => 2,
                'milestone_type' => 'categorised',
                'milestone_color' => 'primary',
            ],
        ];
        foreach ($milestoneData as $data) {
            $milestone = \App\Models\Milestone::create([
                'milestone_title' => $data['milestone_title'],
                'milestone_projectid' => $project->project_id,
                'milestone_creatorid' => 1,
                'milestone_position' => $data['milestone_position'],
                'milestone_type' => $data['milestone_type'],
                'milestone_color' => $data['milestone_color'],
                'milestone_created' => now(),
                'milestone_updated' => now(),
            ]);
            $milestones[] = $milestone;
        }

        // 3. Create test users/team members
        $users = [];
        $userData = [
            ['first_name' => 'John', 'last_name' => 'Developer', 'email' => 'john@test.com'],
            ['first_name' => 'Sarah', 'last_name' => 'Designer', 'email' => 'sarah@test.com'],
            ['first_name' => 'Mike', 'last_name' => 'Manager', 'email' => 'mike@test.com'],
            ['first_name' => 'Lisa', 'last_name' => 'Analyst', 'email' => 'lisa@test.com'],
        ];

        foreach ($userData as $userInfo) {
            $user = \App\Models\User::create([
                'first_name' => $userInfo['first_name'],
                'last_name' => $userInfo['last_name'],
                'email' => $userInfo['email'],
                'role_id' => 2,
                'type' => 'team',
                'status' => 'active',
                'password' => Hash::make('password123'),
                'created' => now(),
                'updated' => now(),
            ]);
            $users[] = $user;
        }

        // 4. Assign users to project
        foreach ($users as $user) {
            \App\Models\ProjectAssigned::create([
                'projectsassigned_projectid' => $project->project_id,
                'projectsassigned_userid' => $user->id,
            ]);
        }

        // 5. Create test tasks
        $taskData = [
            [
                'task_title' => 'Database Design',
                'task_description' => 'Design the database schema for the new system',
                'task_status' => 'completed',
                'task_priority' => 'high',
                'task_date_due' => now()->subDays(5),
                'task_created' => now()->subDays(25),
            ],
            [
                'task_title' => 'Frontend Development',
                'task_description' => 'Develop the user interface components',
                'task_status' => 'in_progress',
                'task_priority' => 'high',
                'task_date_due' => now()->addDays(3),
                'task_created' => now()->subDays(20),
            ],
            [
                'task_title' => 'API Integration',
                'task_description' => 'Integrate third-party APIs',
                'task_status' => 'in_progress',
                'task_priority' => 'medium',
                'task_date_due' => now()->addDays(7),
                'task_created' => now()->subDays(15),
            ],
            [
                'task_title' => 'Testing',
                'task_description' => 'Perform comprehensive testing',
                'task_status' => 'not_started',
                'task_priority' => 'high',
                'task_date_due' => now()->addDays(10),
                'task_created' => now()->subDays(10),
            ],
            [
                'task_title' => 'Documentation',
                'task_description' => 'Write user and technical documentation',
                'task_status' => 'not_started',
                'task_priority' => 'low',
                'task_date_due' => now()->addDays(12),
                'task_created' => now()->subDays(5),
            ],
            [
                'task_title' => 'Deployment',
                'task_description' => 'Deploy to production environment',
                'task_status' => 'not_started',
                'task_priority' => 'high',
                'task_date_due' => now()->addDays(15),
                'task_created' => now()->subDays(2),
            ],
        ];

        foreach ($taskData as $index => $taskInfo) {
            $task = \App\Models\Task::create([
                'task_title' => $taskInfo['task_title'],
                'task_description' => $taskInfo['task_description'],
                'task_status' => $taskInfo['task_status'],
                'task_priority' => $taskInfo['task_priority'],
                'task_projectid' => $project->project_id,
                'task_clientid' => $client->client_id,
                'task_date_due' => $taskInfo['task_date_due'],
                'task_created' => $taskInfo['task_created'],
                'task_updated' => now(),
            ]);
            // Assign some tasks to users
            if ($index < count($users)) {
                \App\Models\TaskAssigned::create([
                    'tasksassigned_taskid' => $task->task_id,
                    'tasksassigned_userid' => $users[$index]->id,
                ]);
            }
        }

        // 6. Create test invoices
        $invoiceData = [
            ['bill_final_amount' => 5000.00, 'bill_status' => 'paid', 'bill_date' => now()->subDays(20)],
            ['bill_final_amount' => 3500.00, 'bill_status' => 'overdue', 'bill_date' => now()->subDays(10)],
            ['bill_final_amount' => 2500.00, 'bill_status' => 'draft', 'bill_date' => now()->subDays(5)],
        ];

        foreach ($invoiceData as $invoiceInfo) {
            \App\Models\Invoice::create([
                'bill_projectid' => $project->project_id,
                'bill_clientid' => $client->client_id,
                'bill_final_amount' => $invoiceInfo['bill_final_amount'],
                'bill_status' => $invoiceInfo['bill_status'],
                'bill_date' => $invoiceInfo['bill_date'],
                'bill_due_date' => Carbon::parse($invoiceInfo['bill_date'])->addDays(30),
                'bill_created' => $invoiceInfo['bill_date'],
                'bill_updated' => now(),
            ]);
        }

        // 7. Create test estimates
        $estimateData = [
            ['bill_final_amount' => 8000.00, 'bill_status' => 'approved', 'bill_date' => now()->subDays(15)],
            ['bill_final_amount' => 4500.00, 'bill_status' => 'pending', 'bill_date' => now()->subDays(8)],
        ];

        foreach ($estimateData as $estimateInfo) {
            \App\Models\Estimate::create([
                'bill_projectid' => $project->project_id,
                'bill_clientid' => $client->client_id,
                'bill_final_amount' => $estimateInfo['bill_final_amount'],
                'bill_status' => $estimateInfo['bill_status'],
                'bill_date' => $estimateInfo['bill_date'],
                'bill_created' => $estimateInfo['bill_date'],
                'bill_updated' => now(),
            ]);
        }

        // 8. Create test contracts
        $contractData = [
            [
                'doc_title' => 'Main Development Contract',
                'doc_value' => 25000.00,
                'doc_status' => 'active',
                'doc_date_start' => now()->subDays(30),
                'doc_date_end' => now()->addDays(60),
            ],
            [
                'doc_title' => 'Support Agreement',
                'doc_value' => 5000.00,
                'doc_status' => 'awaiting_signatures',
                'doc_date_start' => now()->addDays(60),
                'doc_date_end' => now()->addDays(365),
            ],
        ];

        foreach ($contractData as $contractInfo) {
            \App\Models\Contract::create([
                'doc_project_id' => $project->project_id,
                'doc_client_id' => $client->client_id,
                'doc_title' => $contractInfo['doc_title'],
                'doc_value' => $contractInfo['doc_value'],
                'doc_status' => $contractInfo['doc_status'],
                'doc_date_start' => $contractInfo['doc_date_start'],
                'doc_date_end' => $contractInfo['doc_date_end'],
                'doc_created' => now(),
                'doc_updated' => now(),
            ]);
        }

        // 9. Create test timers (unbilled hours)
        $timerData = [
            ['timer_time' => 7200, 'timer_description' => 'Database design work'], // 2 hours
            ['timer_time' => 5400, 'timer_description' => 'Frontend development'], // 1.5 hours
            ['timer_time' => 3600, 'timer_description' => 'API integration'], // 1 hour
        ];

        foreach ($timerData as $timerInfo) {
            \App\Models\Timer::create([
                'timer_projectid' => $project->project_id,
                'timer_creatorid' => $users[0]->id,
                'timer_time' => $timerInfo['timer_time'],
                'timer_status' => 'stopped',
                'timer_billing_status' => 'unbilled',
                'timer_created' => now(),
                'timer_updated' => now(),
            ]);
        }
    }
}