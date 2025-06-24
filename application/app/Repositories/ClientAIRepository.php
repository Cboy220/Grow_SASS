<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Client;

class ClientAIRepository
{
    public function fetchClientAnalysisData($clientId, $topics, $startDate, $endDate)
    {
        $data = [];

        if (in_array('billing', $topics)) {
            $data['billing'] = DB::table('invoices')
                ->select('bill_invoiceid', 'bill_final_amount', 'bill_date', 'bill_due_date', 'bill_status')
                ->where('bill_clientid', $clientId)
                ->whereBetween('bill_date', [$startDate, $endDate])
                ->get();
        }

        if (in_array('projects', $topics)) {
            $data['projects'] = DB::table('projects')
                ->select('project_title', 'project_status', 'project_date_due')
                ->where('project_clientid', $clientId)
                ->whereBetween('project_created', [$startDate, $endDate])
                ->get();
        }

        if (in_array('feedback', $topics)) {
            $data['feedback'] = DB::table('feedbacks')
                ->select('feedback_id', 'feedback_date', 'comment', 'feedback_created')
                ->where('client_id', $clientId)
                ->whereBetween('feedback_created', [$startDate, $endDate])
                ->get();
        }

        return $data;
    }

    public function formatDataForAI($clientId, array $data): string
    {
        $clientName = DB::table('clients')->where('client_id', $clientId)->value('client_company_name');
        $summary = "Analyze this client: {$clientName} (ID: {$clientId})\n\n";

        if (!empty($data['billing'])) {
            $summary .= "Billing:\n";
            foreach ($data['billing'] as $item) {
                $summary .= "- {$item->bill_status} invoice of {$item->bill_final_amount} due on {$item->bill_due_date}\n";
            }
        }

        if (!empty($data['projects'])) {
            $summary .= "\nProjects:\n";
            foreach ($data['projects'] as $p) {
                $summary .= "- {$p->project_title} ({$p->project_status}), deadline: {$p->project_date_due}\n";
            }
        }

        if (!empty($data['tasks'])) {
            $summary .= "\nTasks:\n";
            foreach ($data['tasks'] as $t) {
                $summary .= "- {$t->task_title}, status: {$t->task_status}, due: {$t->task_date_due}\n";
            }
        }

        if (!empty($data['comments'])) {
            $summary .= "\nClient Comments:\n";
            foreach ($data['comments'] as $c) {
                $summary .= "- \"{$c}\"\n";
            }
        }

        if (!empty($data['surveys'])) {
            $summary .= "\nSurvey Responses:\n";
            foreach ($data['surveys'] as $s) {
                $summary .= "- {$s->title}: {$s->value}/{$s->range} (weight {$s->weight})\n";
            }
        }

        if (!empty($data['expectations_summary'])) {
            $s = $data['expectations_summary'];
            $summary .= "\nExpectation Summary:\n";
            $summary .= "- Fulfilled: {$s['fulfilled_percent']}%\n";
            $summary .= "- Overdue expectations: {$s['overdue_count']}\n";
            $summary .= "- Total expectations: {$s['total_count']}\n";
        }

        return $summary;
    }

    /**
     * Get a client with all related data for AI analysis.
     *
     * @param int $clientId
     * @return Client|null
     */
    public function getClientWithRelations($clientId)
    {
        return Client::with([
            'creator',
            'projects.tasks',
            'projects.invoices',
            'projects.estimates',
            'projects.contracts',
            'projects.expenses',
            'projects.payments',
            'projects.milestones',
            'projects.tickets',
            'projects.files',
            'projects.tags',
            'projects.comments',
            'users.role',
            'invoices',
            'estimates',
            'notes',
            'proposals',
            'contracts',
            'expenses',
            'files',
            'payments',
            'tags',
            'tickets',
            'category',
            'feedbacks.feedbackDetails',
            'clientExpectations',
        ])->find($clientId);
    }

    /**
     * Generate a comprehensive prompt for OpenAI based on a client's full profile and activity, with detailed date-based analysis.
     * This enhanced version includes all important relationships and detailed feedback analysis.
     *
     * @param int $clientId
     * @return string
     */
    public function generateComprehensiveClientPrompt($clientId)
    {
        $now = Carbon::now();

        // --- Basic Client Info with Category ---
        $client = DB::table('clients')
            ->leftJoin('categories', 'categories.category_id', '=', 'clients.client_categoryid')
            ->select(
                'clients.*',
                'categories.category_name as category'
            )
            ->where('client_id', $clientId)
            ->first();

        // Always initialize $prompt
        $prompt = [];

        // Guard: If client not found, return a helpful message
        if (!$client) {
            $prompt[] = "No client found with ID: {$clientId}";
            return implode("\n", $prompt);
        }

        // --- Contacts/Users with Role Information ---
        $users = DB::table('users')
            ->leftJoin('roles', 'roles.role_id', '=', 'users.role_id')
            ->select(
                'users.*',
                'roles.role_name'
            )
            ->where('clientid', $clientId)
            ->get();

        // --- Projects with Tasks and Milestones ---
        $projects = DB::table('projects')
            ->leftJoin('categories', 'categories.category_id', '=', 'projects.project_categoryid')
            ->select(
                'projects.*',
                'categories.category_name as project_category'
            )
            ->where('project_clientid', $clientId)
            ->orderByDesc('project_created')
            ->get();
        $lastProject = $projects->first();
        $daysSinceLastProject = $lastProject ? Carbon::parse($lastProject->project_created)->diffInDays($now) : null;

        // --- Project Tasks ---
        $projectTasks = DB::table('tasks')
            ->leftJoin('projects', 'projects.project_id', '=', 'tasks.task_projectid')
            ->leftJoin('tasks_assigned', 'tasks_assigned.tasksassigned_taskid', '=', 'tasks.task_id')
            ->leftJoin('users', 'users.id', '=', 'tasks_assigned.tasksassigned_userid')
            ->select(
                'tasks.*',
                'projects.project_title',
                DB::raw("GROUP_CONCAT(CONCAT(users.first_name, ' ', users.last_name)) as assigned_users")
            )
            ->where('projects.project_clientid', $clientId)
            ->groupBy('tasks.task_id')
            ->orderByDesc('tasks.task_created')
            ->get();

        // --- Invoices with Line Items ---
        $invoices = DB::table('invoices')
            ->leftJoin('categories', 'categories.category_id', '=', 'invoices.bill_categoryid')
            ->select(
                'invoices.*',
                'categories.category_name as invoice_category'
            )
            ->where('bill_clientid', $clientId)
            ->orderByDesc('bill_date')
            ->get();
        $lastInvoice = $invoices->first();
        $daysSinceLastInvoice = $lastInvoice ? Carbon::parse($lastInvoice->bill_date)->diffInDays($now) : null;

        // --- Invoice Line Items ---
        $invoiceItems = DB::table('lineitems')
            ->where('lineitemresource_type', 'invoice')
            ->whereIn('lineitemresource_id', $invoices->pluck('bill_invoiceid'))
            ->get();

        // --- Payments with Gateway Information ---
        $payments = DB::table('payments')
            ->leftJoin('invoices', 'invoices.bill_invoiceid', '=', 'payments.payment_invoiceid')
            ->select(
                'payments.*',
                'invoices.bill_invoiceid',
                'invoices.bill_final_amount'
            )
            ->where('payment_clientid', $clientId)
            ->orderByDesc('payment_date')
            ->get();
        $lastPayment = $payments->first();
        $daysSinceLastPayment = $lastPayment ? Carbon::parse($lastPayment->payment_date)->diffInDays($now) : null;

        // --- Enhanced Feedbacks with Detailed Analysis ---
        $feedbacks = DB::table('feedbacks')
            ->leftJoin('users', 'users.clientid', '=', 'feedbacks.client_id')
            ->select(
                'feedbacks.*',
                'users.first_name',
                'users.last_name',
                'users.email'
            )
            ->where('feedbacks.client_id', $clientId)
            ->orderByDesc('feedback_created')
            ->get();
        $lastFeedback = $feedbacks->first();
        $daysSinceLastFeedback = $lastFeedback ? Carbon::parse($lastFeedback->feedback_created)->diffInDays($now) : null;

        // --- Detailed Feedback Analysis with Query Information ---
        $feedbackDetails = DB::table('feedback_details')
            ->join('feedbacks', 'feedbacks.feedback_id', '=', 'feedback_details.feedback_id')
            ->join('feedback_queries', 'feedback_queries.feedback_query_id', '=', 'feedback_details.feedback_query_id')
            ->leftJoin('users', 'users.clientid', '=', 'feedbacks.client_id')
            ->select(
                'feedback_details.*',
                'feedback_queries.title as query_title',
                'feedback_queries.content as query_content',
                'feedback_queries.type as query_type',
                'feedback_queries.range as query_range',
                'feedback_queries.weight as query_weight',
                'feedbacks.comment as feedback_comment',
                'feedbacks.feedback_date',
                'users.first_name',
                'users.last_name'
            )
            ->where('feedbacks.client_id', $clientId)
            ->orderByDesc('feedbacks.feedback_created')
            ->get();

        // --- Client Expectations ---
        $expectations = DB::table('client_expectations')
            ->where('client_id', $clientId)
            ->orderByDesc('expectation_created')
            ->get();
        $lastExpectation = $expectations->first();
        $daysSinceLastExpectation = $lastExpectation ? Carbon::parse($lastExpectation->expectation_created)->diffInDays($now) : null;

        // --- Support Tickets with Replies ---
        $tickets = DB::table('tickets')
            ->leftJoin('categories', 'categories.category_id', '=', 'tickets.ticket_categoryid')
            ->select(
                'tickets.*',
                'categories.category_name as ticket_category'
            )
            ->where('ticket_clientid', $clientId)
            ->orderByDesc('ticket_created')
            ->get();
        $lastTicket = $tickets->first();
        $daysSinceLastTicket = $lastTicket ? Carbon::parse($lastTicket->ticket_created)->diffInDays($now) : null;

        // --- Ticket Replies ---
        $ticketReplies = DB::table('ticket_replies')
            ->leftJoin('tickets', 'tickets.ticket_id', '=', 'ticket_replies.ticketreply_ticketid')
            ->leftJoin('users', 'users.id', '=', 'ticket_replies.ticketreply_creatorid')
            ->select(
                'ticket_replies.*',
                'tickets.ticket_subject',
                'users.first_name',
                'users.last_name',
                'users.type as user_type'
            )
            ->where('tickets.ticket_clientid', $clientId)
            ->orderByDesc('ticket_replies.ticketreply_created')
            ->get();

        // --- Notes with Creator Information ---
        $notes = DB::table('notes')
            ->leftJoin('users', 'users.id', '=', 'notes.note_creatorid')
            ->select(
                'notes.*',
                'users.first_name as creator_first_name',
                'users.last_name as creator_last_name'
            )
            ->where('noteresource_id', $clientId)
            ->where('noteresource_type', 'client')
            ->orderByDesc('note_created')
            ->get();
        $lastNote = $notes->first();
        $daysSinceLastNote = $lastNote ? Carbon::parse($lastNote->note_created)->diffInDays($now) : null;

        // --- Files ---
        $files = DB::table('files')
            ->leftJoin('users', 'users.id', '=', 'files.file_creatorid')
            ->select(
                'files.*',
                'users.first_name as creator_first_name',
                'users.last_name as creator_last_name'
            )
            ->where('fileresource_id', $clientId)
            ->where('fileresource_type', 'client')
            ->orderByDesc('file_created')
            ->get();

        // --- Tags ---
        $tags = DB::table('tags')
            ->where('tagresource_id', $clientId)
            ->where('tagresource_type', 'client')
            ->get();

        // --- Estimates ---
        $estimates = DB::table('estimates')
            ->leftJoin('categories', 'categories.category_id', '=', 'estimates.bill_categoryid')
            ->select(
                'estimates.*',
                'categories.category_name as estimate_category'
            )
            ->where('bill_clientid', $clientId)
            ->orderByDesc('bill_created')
            ->get();

        // --- Contracts ---
        $contracts = DB::table('contracts')
            ->where('doc_lead_id', $clientId)
            ->orderByDesc('doc_created')
            ->get();

        // --- Proposals ---
        $proposals = DB::table('proposals')
            ->where('doc_lead_id', $clientId)
            ->orderByDesc('doc_created')
            ->get();

        // --- Expenses ---
        $expenses = DB::table('expenses')
            ->leftJoin('categories', 'categories.category_id', '=', 'expenses.expense_categoryid')
            ->select(
                'expenses.*',
                'categories.category_name as expense_category'
            )
            ->where('expense_clientid', $clientId)
            ->orderByDesc('expense_created')
            ->get();

        // --- Days since client joined ---
        $daysSinceJoined = $client && $client->client_created ? Carbon::parse($client->client_created)->diffInDays($now) : null;

        // --- Calculate Financial Summary ---
        $totalInvoiced = $invoices->sum('bill_final_amount');
        $totalPaid = $payments->sum('payment_amount');
        $outstandingBalance = $totalInvoiced - $totalPaid;

        // --- Calculate Feedback Summary ---
        $feedbackSummary = $this->calculateFeedbackSummary($feedbackDetails);

        // --- Calculate Expectations Summary ---
        $expectationsSummary = $this->calculateExpectationsSummary($expectations);

        // --- Summarize Data ---
        $prompt[] = "You are an expert business analyst AI. Here is a comprehensive profile of a client from our CRM system.";
        $prompt[] = "\nClient Profile:";
        $prompt[] = "- Company Name: {$client->client_company_name}";
        $prompt[] = "- Industry: " . ($client->industry ?? 'N/A');
        $prompt[] = "- Category: " . ($client->category ?? 'N/A');
        $prompt[] = "- Status: " . ($client->client_status ?? 'N/A');
        $prompt[] = "- Joined: {$client->client_created} (" . ($daysSinceJoined !== null ? "$daysSinceJoined days ago" : 'N/A') . ")";
        $prompt[] = "- Description: " . ($client->client_description ?? 'N/A');
        $prompt[] = "- Website: " . ($client->client_website ?? 'N/A');
        $prompt[] = "- Phone: " . ($client->client_phone ?? 'N/A');
        $prompt[] = "- VAT: " . ($client->client_vat ?? 'N/A') . "\n";

        $prompt[] = "Contacts (Total: {$users->count()}):";
        foreach ($users as $user) {
            $roleName = $user->role_name ?? 'No Role';
            $prompt[] = "- {$user->first_name} {$user->last_name} ({$user->email}), Role: {$roleName}, Type: {$user->type}";
        }

        $prompt[] = "\nProjects (Total: {$projects->count()}, Last: " . ($lastProject ? $lastProject->project_created . ", $daysSinceLastProject days ago" : 'N/A') . "):";
        foreach ($projects->take(5) as $project) {
            $prompt[] = "- {$project->project_title} (Status: {$project->project_status}, Category: {$project->project_category}, Created: {$project->project_created}, Deadline: {$project->project_date_due})";
        }

        $prompt[] = "\nProject Tasks (Total: {$projectTasks->count()}):";
        foreach ($projectTasks->take(5) as $task) {
            $assignedUser = $task->assigned_users ?? 'Unassigned';
            $prompt[] = "- {$task->task_title} (Project: {$task->project_title}, Status: {$task->task_status}, Assigned: {$assignedUser}, Due: {$task->task_date_due})";
        }

        $prompt[] = "\nFinancial Summary:";
        $prompt[] = "- Total Invoiced: {$totalInvoiced}";
        $prompt[] = "- Total Paid: {$totalPaid}";
        $prompt[] = "- Outstanding Balance: {$outstandingBalance}";

        $prompt[] = "\nInvoices (Total: {$invoices->count()}, Last: " . ($lastInvoice ? $lastInvoice->bill_date . ", $daysSinceLastInvoice days ago" : 'N/A') . "):";
        foreach ($invoices->take(5) as $invoice) {
            $prompt[] = "- Invoice #{$invoice->bill_invoiceid}, Amount: {$invoice->bill_final_amount}, Status: {$invoice->bill_status}, Category: {$invoice->invoice_category}, Date: {$invoice->bill_date}";
        }

        $prompt[] = "\nPayments (Total: {$payments->count()}, Last: " . ($lastPayment ? $lastPayment->payment_date . ", $daysSinceLastPayment days ago" : 'N/A') . "):";
        foreach ($payments->take(5) as $payment) {
            $prompt[] = "- Payment #{$payment->payment_id}, Amount: {$payment->payment_amount}, Gateway: {$payment->payment_gateway}, Date: {$payment->payment_date}";
        }

        $prompt[] = "\nEnhanced Feedback Analysis:";
        $prompt[] = "- Total Feedback Entries: {$feedbacks->count()}";
        $prompt[] = "- Last Feedback: " . ($lastFeedback ? $lastFeedback->feedback_created . ", $daysSinceLastFeedback days ago" : 'N/A');
        $prompt[] = "- Average Overall Score: {$feedbackSummary['average_score']}";
        $prompt[] = "- Feedback Trend: {$feedbackSummary['trend']}";
        $prompt[] = "- Most Recent Feedback: " . ($lastFeedback ? "\"{$lastFeedback->comment}\"" : 'N/A');

        $prompt[] = "\nDetailed Feedback Breakdown:";
        foreach ($feedbackDetails->take(10) as $fd) {
            $userName = $fd->first_name ? "{$fd->first_name} {$fd->last_name}" : 'Anonymous';
            $prompt[] = "- Query: \"{$fd->query_title}\" - Score: {$fd->value}/{$fd->query_range} (Weight: {$fd->query_weight}) - User: {$userName} - Date: {$fd->feedback_date}";
        }

        $prompt[] = "\nClient Expectations (Total: {$expectations->count()}, Last: " . ($lastExpectation ? $lastExpectation->expectation_created . ", $daysSinceLastExpectation days ago" : 'N/A') . "):";
        $prompt[] = "- Fulfilled: {$expectationsSummary['fulfilled_count']} ({$expectationsSummary['fulfilled_percent']}%)";
        $prompt[] = "- Pending: {$expectationsSummary['pending_count']}";
        $prompt[] = "- Overdue: {$expectationsSummary['overdue_count']}";
        foreach ($expectations->take(5) as $exp) {
            $prompt[] = "- \"{$exp->title}\" (Status: {$exp->status}, Due: {$exp->due_date}, Weight: {$exp->weight})";
        }

        $prompt[] = "\nSupport Tickets (Total: {$tickets->count()}, Last: " . ($lastTicket ? $lastTicket->ticket_created . ", $daysSinceLastTicket days ago" : 'N/A') . "):";
        foreach ($tickets->take(5) as $ticket) {
            $prompt[] = "- Ticket #{$ticket->ticket_id}, Subject: {$ticket->ticket_subject}, Status: {$ticket->ticket_status}, Created: {$ticket->ticket_created}";
        }

        $prompt[] = "\nNotes (Total: {$notes->count()}, Last: " . ($lastNote ? $lastNote->note_created . ", $daysSinceLastNote days ago" : 'N/A') . "):";
        foreach ($notes->take(5) as $note) {
            $creator = $note->creator_first_name ? "{$note->creator_first_name} {$note->creator_last_name}" : 'Unknown';
            $prompt[] = "- \"{$note->note_title}\": {$note->note_description} (Created by: {$creator}, Date: {$note->note_created})";
        }

        $prompt[] = "\nFiles (Total: {$files->count()}):";
        foreach ($files->take(5) as $file) {
            $creator = $file->creator_first_name ? "{$file->creator_first_name} {$file->creator_last_name}" : 'Unknown';
            $prompt[] = "- {$file->file_filename} (Type: {$file->file_type}, Size: {$file->file_size}, Created by: {$creator})";
        }

        $prompt[] = "\nTags:";
        if ($tags->count() > 0) {
            foreach ($tags as $tag) {
                $prompt[] = "- {$tag->tag_title}";
            }
        } else {
            $prompt[] = "- No tags assigned";
        }

        $prompt[] = "\nEstimates (Total: {$estimates->count()}):";
        foreach ($estimates->take(3) as $estimate) {
            $prompt[] = "- Estimate #{$estimate->bill_estimateid}, Amount: {$estimate->bill_final_amount}, Status: {$estimate->bill_status}, Category: {$estimate->estimate_category}";
        }

        $prompt[] = "\nContracts (Total: {$contracts->count()}):";
        foreach ($contracts->take(3) as $contract) {
            $prompt[] = "- Contract #{$contract->doc_id}, Title: {$contract->doc_title}, Status: {$contract->doc_status}";
        }

        $prompt[] = "\nProposals (Total: {$proposals->count()}):";
        foreach ($proposals->take(3) as $proposal) {
            $prompt[] = "- Proposal #{$proposal->doc_id}, Title: {$proposal->doc_title}, Status: {$proposal->doc_status}";
        }

        $prompt[] = "\nExpenses (Total: {$expenses->count()}):";
        foreach ($expenses->take(3) as $expense) {
            $prompt[] = "- Expense #{$expense->expense_id}, Amount: {$expense->expense_amount}, Category: {$expense->expense_category}, Description: {$expense->expense_description}";
        }

        $prompt[] = "\nPlease analyze this client and provide:";
        $prompt[] = "1. A comprehensive summary of the client's current status and relationship with us.";
        $prompt[] = "2. Key insights from feedback analysis, including satisfaction trends and specific concerns.";
        $prompt[] = "3. Financial health assessment based on payment history and outstanding balances.";
        $prompt[] = "4. Risk assessment based on expectations, project status, and communication patterns.";
        $prompt[] = "5. Specific recommendations for improving client satisfaction and retention.";
        $prompt[] = "6. Opportunities for upselling or expanding services based on their needs.";
        $prompt[] = "7. Any red flags or areas requiring immediate attention.";

        return implode("\n", $prompt);
    }

    /**
     * Calculate feedback summary statistics
     */
    private function calculateFeedbackSummary($feedbackDetails)
    {
        if ($feedbackDetails->isEmpty()) {
            return [
                'average_score' => 'N/A',
                'trend' => 'No feedback available'
            ];
        }

        // Group by feedback_id to calculate overall scores
        $feedbackScores = [];
        foreach ($feedbackDetails->groupBy('feedback_id') as $feedbackId => $details) {
            $totalWeightedScore = 0;
            $totalWeight = 0;
            
            foreach ($details as $detail) {
                $weightedScore = $detail->value * $detail->query_weight;
                $totalWeightedScore += $weightedScore;
                $totalWeight += $detail->query_weight;
            }
            
            if ($totalWeight > 0) {
                $feedbackScores[] = $totalWeightedScore / $totalWeight;
            }
        }

        $averageScore = count($feedbackScores) > 0 ? round(array_sum($feedbackScores) / count($feedbackScores), 2) : 0;

        // Determine trend (simplified - you could make this more sophisticated)
        $trend = 'Stable';
        if (count($feedbackScores) >= 2) {
            $recentScores = array_slice($feedbackScores, 0, 3);
            $olderScores = array_slice($feedbackScores, -3);
            
            $recentAvg = array_sum($recentScores) / count($recentScores);
            $olderAvg = array_sum($olderScores) / count($olderScores);
            
            if ($recentAvg > $olderAvg + 0.5) {
                $trend = 'Improving';
            } elseif ($recentAvg < $olderAvg - 0.5) {
                $trend = 'Declining';
            }
        }

        return [
            'average_score' => $averageScore,
            'trend' => $trend
        ];
    }

    /**
     * Calculate expectations summary statistics
     */
    private function calculateExpectationsSummary($expectations)
    {
        if ($expectations->isEmpty()) {
            return [
                'fulfilled_count' => 0,
                'fulfilled_percent' => 0,
                'pending_count' => 0,
                'overdue_count' => 0
            ];
        }

        $fulfilled = $expectations->where('status', 'fulfilled')->count();
        $pending = $expectations->where('status', 'pending')->count();
        $overdue = $expectations->where('status', 'overdue')->count();
        $total = $expectations->count();

        $fulfilledPercent = $total > 0 ? round(($fulfilled / $total) * 100, 1) : 0;

        return [
            'fulfilled_count' => $fulfilled,
            'fulfilled_percent' => $fulfilledPercent,
            'pending_count' => $pending,
            'overdue_count' => $overdue
        ];
    }
}
