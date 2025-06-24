<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ClientAIRepository;
use OpenAI\Laravel\Facades\OpenAI;

class ClientAIAnalysis extends Controller
{
    protected $repo;

    public function __construct(ClientAIRepository $repo)
    {
        //authenticated
        $this->middleware(middleware: 'auth');

        $this->middleware(middleware: 'clientsMiddlewareIndex')->only(methods: [
            'index',
            'update',
            'store',
        ]);

        $this->middleware(middleware: 'clientsMiddlewareEdit')->only([
            'edit',
            'update',
            'updateDescription',
            'changeAccountOwner',
            'changeAccountOwnerUpdate',
        ]);

        $this->middleware('clientsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('clientsMiddlewareDestroy')->only(['destroy']);

        $this->middleware('clientsMiddlewareShow')->only([
            'show',
            'details',
            'updateDescription',
            'emailCompose',
            'emailSend',
            'togglePinning',
        ]);

        $this->repo = $repo;
    }

    public function index($clientId)
    {
        return view('pages.clientai.index', compact('clientId'));
    }

    public function analyze(Request $request, $clientId)
    {
        $prompt = $this->repo->generateComprehensiveClientPrompt($clientId);

        try {
            $messages = [
                [
                    'role' => 'system',
                    'content' => 'You are an expert assistant for client analysis. Answer clearly and helpfully.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ];
    
            $aiAnswer = $this->askOpenAI($messages);
    
            // Store history in session
            session(["client_ai_data_{$clientId}" => [
                $messages[0], // system
                $messages[1], // user
                ['role' => 'assistant', 'content' => $aiAnswer]
            ]]);

            return response()->json(['success' => true, 'result' => $aiAnswer ?? 'No response from AI.']);
            
        } catch (\OpenAI\Exceptions\RateLimitException $e) {
            return response()->json(['success' => false, 'message' => 'Rate limit exceeded.']);
        } catch (\OpenAI\Exceptions\AuthenticationException $e) {
            return response()->json(['success' => false, 'message' => 'Authentication failed.']);
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            return response()->json(['success' => false, 'message' => 'AI error: ' . $e->getMessage()]);
        } catch (\OpenAI\Exceptions\TransporterException $e) {
            return response()->json(['success' => false, 'message' => 'Connection error.']);
        }
    }

    public function ask(Request $request, $clientId)
    {
        $userQuestion = $request->input('question');
        $messages = session("client_ai_data_{$clientId}", []);

        // Add user question
        $messages[] = ['role' => 'user', 'content' => $userQuestion];

        $aiAnswer = $this->askOpenAI($messages);

        // Add AI answer to history
        $messages[] = ['role' => 'assistant', 'content' => $aiAnswer];
        session(["client_ai_data_{$clientId}" => $messages]);

        return response()->json(['answer' => $aiAnswer]);
    
    }

    private function askOpenAI($messages)
    {
        $response = OpenAI::chat()->create([
            'model' => config('openai.model'),
            'messages' => $messages
        ]);
        return $response['choices'][0]['message']['content'];
    }
}
