<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AIController extends Controller
{
    // Rate limiting (3 requests per minute)
    protected $maxAttempts = 3;
    protected $decayMinutes = 1;

    public function index()
    {
        return view('ai');
    }

    public function suggest(Request $request)
    {
        $validated = $request->validate([
            'subject ' => 'nullable',
        ]);

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . env('OPENAI_API_KEY'),
            "Content-Type" => "application/json"
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $request->subject]
            ]
        ]);

        $responseMessage = $response['choices'][0]['message']['content'] ?? 'No reply from AI.';

        return response()->json([
        'userMessage' => $request->subject,
        'aiMessage' => $responseMessage,
    ]);
    }
}
