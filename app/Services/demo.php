<?php

namespace App\Services;

use App\Models\ChatHistory;
use App\Traits\ApiResponse;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Throwable;

class ChatgptService
{
    use ApiResponse;

    protected $client;

    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->endpoint = 'https://api.openai.com/v1/chat/completions';
    }

    public function getChatResponse($userId, $prompt)
    {
        try {
            $systemPrompt = <<<SYSTEM
                            You are CRYPTAX, an AI search engine trained with publicly available information about cryptocurrency taxation in the Netherlands. You must not provide legal or financial advice.

                            Your responses must:
                            - Be factual, accurate, and based only on official sources:
                            - Belastingdienst.nl (Dutch Tax Authority)
                            - Rijksoverheid.nl (Government of the Netherlands)
                            - AFM.nl (Dutch Authority for the Financial Markets)
                            - DNB.nl (Dutch Central Bank)
                            - Divly | Cryptobelastinggids
                            - Always cite the source for each fact, e.g., "According to Belastingdienst.nl..."
                            - Use simple, non-legal English language for now (for testing), unless the user requests another language.
                            - Structure answers with bullet points where possible.
                            - Never make assumptions or give personal tax advice.
                            - Always include this disclaimer at the end:

                            "This answer is based on publicly available information from Dutch government sources. It does not constitute tax advice. For personal advice, consult a certified tax advisor."

                            Knowledge structure you must follow:
                            1. Introduction to crypto & taxation (what is crypto, why declare, role of Belastingdienst, only factual info)
                            2. Crypto and income tax (Box 3 – assets, valuation date, value determination, where to declare)
                            3. Business use of crypto (when is someone an entrepreneur, business income, VAT, record-keeping)
                            4. Filing crypto taxes (where to report, documenting transactions, using historical price data)
                            5. Source citations are mandatory for all facts.
                            6. Use English for now, unless another language is requested, but always base answers on Dutch law.

                            If the user asks for advice, remind them you cannot provide personal tax advice.
                            SYSTEM;

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post($this->endpoint, [
                'model' => 'gpt-4', // or 'gpt-3.5-turbo'
                'messages' => $messages,
                'temperature' => 0.4,
                'max_tokens' => 1000,
            ]);

            if ($response->failed()) {
                $errorBody = $response->body();
                Log::error('OpenAI API failed: ' . $errorBody);
                return [
                    'success' => false,
                    'response' => 'API request failed: ' . $errorBody, // More detail for debugging
                ];
            }

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? null;

            return [
                'success' => true,
                'response' => $content,
            ];
        } catch (Throwable $e) {
            Log::error('OpenAI API request failed: ' . $e->getMessage());
            return [
                'success' => false,
                'response' => 'API request failed: ' . $e->getMessage(), // More detail for debugging
            ];
        }
    }
}