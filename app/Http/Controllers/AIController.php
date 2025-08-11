<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\ChattingService;

class AIController extends Controller
{
    // Rate limiting (3 requests per minute)
    protected $maxAttempts = 3;
    protected $decayMinutes = 1;

    public function index()
    {
        return view('ai');
    }

    public function suggest(Request $request, ChattingService $chatService)
    {
        $validated = $request->validate([
            'subject' => 'nullable|string',
        ]);

        $aiMessage = $chatService->suggest($validated['subject']);

        return response()->json([
            'userMessage' => $validated['subject'],
            'aiMessage' => $aiMessage,
        ]);
    }
}
