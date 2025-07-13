<?php

// Test script to verify AI integration
require_once 'application/bootstrap/app.php';

use App\Http\Controllers\Clients;
use App\Http\Controllers\Projects;
use App\Repositories\ClientAIRepository;

echo "Testing AI Integration...\n";

// Test 1: Check if OpenAI configuration is loaded
echo "1. Checking OpenAI configuration...\n";
$config = config('openai');
if ($config && isset($config['api_key'])) {
    echo "✓ OpenAI configuration found\n";
    echo "   Model: " . ($config['model'] ?? 'gpt-4o-mini') . "\n";
} else {
    echo "✗ OpenAI configuration missing\n";
}

// Test 2: Check if ClientAIRepository methods exist
echo "\n2. Checking ClientAIRepository methods...\n";
$clientAIrepo = new ClientAIRepository();

$methods = [
    'generateFeedbackAnalysisPrompt',
    'generateExpectationsAnalysisPrompt', 
    'generateProjectsAnalysisPrompt',
    'generateCommentsAnalysisPrompt',
    'generateComprehensiveClientPrompt'
];

foreach ($methods as $method) {
    if (method_exists($clientAIrepo, $method)) {
        echo "✓ Method {$method} exists\n";
    } else {
        echo "✗ Method {$method} missing\n";
    }
}

// Test 3: Check if controller methods exist
echo "\n3. Checking controller methods...\n";
$clientMethods = [
    'generateAIFeedbackAnalysis',
    'generateAIExpectationsAnalysis',
    'generateAIProjectsAnalysis', 
    'generateAICommentsAnalysis'
];

foreach ($clientMethods as $method) {
    if (method_exists('App\Http\Controllers\Clients', $method)) {
        echo "✓ Client method {$method} exists\n";
    } else {
        echo "✗ Client method {$method} missing\n";
    }
}

$projectMethods = [
    'generateAITasksAnalysis',
    'generateAITeamAnalysis',
    'generateAIBillingAnalysis'
];

foreach ($projectMethods as $method) {
    if (method_exists('App\Http\Controllers\Projects', $method)) {
        echo "✓ Project method {$method} exists\n";
    } else {
        echo "✗ Project method {$method} missing\n";
    }
}

// Test 4: Check routes
echo "\n4. Checking routes...\n";
$routes = [
    'clients.generate.ai.feedback',
    'clients.generate.ai.expectations', 
    'clients.generate.ai.projects',
    'clients.generate.ai.comments',
    'projects.generate.ai.tasks',
    'projects.generate.ai.team',
    'projects.generate.ai.billing'
];

foreach ($routes as $route) {
    try {
        $url = route($route, ['client' => 1, 'project' => 1]);
        echo "✓ Route {$route} exists\n";
    } catch (Exception $e) {
        echo "✗ Route {$route} missing\n";
    }
}

echo "\nAI Integration Test Complete!\n";
echo "\nTo test the actual AI functionality:\n";
echo "1. Set your OPENAI_API_KEY in .env file\n";
echo "2. Visit a client or project page\n";
echo "3. Click the AI Analysis button\n";
echo "4. Check if real AI analysis is generated\n"; 