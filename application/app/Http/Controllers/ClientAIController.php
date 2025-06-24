<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ClientAIRepository;
use OpenAI\Client as OpenAIClient;

class ClientAIController extends Controller
{
    protected $aiRepository;

    public function __construct(ClientAIRepository $aiRepository)
    {
        $this->aiRepository = $aiRepository;
    }

    public function index(Request $request)
    {
        // Show the chat UI (implement your Blade view)
        return view('client_ai.chat');
    }

    public function analyze(Request $request)
    {
        $clientId = $request->input('client_id');
        $clientPrompt = $this->aiRepository->generateComprehensiveClientPrompt($clientId);

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an expert assistant for client analysis. Answer clearly and helpfully.'
            ],
            [
                'role' => 'user',
                'content' => $clientPrompt
            ]
        ];

        $aiAnswer = $this->askOpenAI($messages);

        // Store history in session
        session(['ai_chat_history' => [
            $messages[0], // system
            $messages[1], // user
            ['role' => 'assistant', 'content' => $aiAnswer]
        ]]);

        return response()->json(['answer' => $aiAnswer]);
    }

    public function ask(Request $request)
    {
        $userQuestion = $request->input('question');
        $messages = session('ai_chat_history', []);

        // Add user question
        $messages[] = ['role' => 'user', 'content' => $userQuestion];

        $aiAnswer = $this->askOpenAI($messages);

        // Add AI answer to history
        $messages[] = ['role' => 'assistant', 'content' => $aiAnswer];
        session(['ai_chat_history' => $messages]);

        return response()->json(['answer' => $aiAnswer]);
    }

    private function askOpenAI($messages)
    {
        $openai = new OpenAIClient('YOUR_OPENAI_API_KEY');
        $response = $openai->chat()->create([
            'model' => 'gpt-3.5-turbo', // or 'gpt-4'
            'messages' => $messages,
        ]);
        return $response['choices'][0]['message']['content'];
    }
} 