<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChattingService
{
    protected string $apiKey;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->endpoint = 'https://openrouter.ai/api/v1/chat/completions';
    }

    public function suggest(string $input, bool $isInitialRequest = true): string
    {
        try {
            // Modify system prompt so quizzes follow strict format
$systemPrompt = <<<SYSTEM
    You are an academic tutor that follows these strict behavior rules:

    1. INITIAL QUESTIONS MODE:
    - If the user provides a GENERAL subject (e.g., "Geometry"), reply with:
        1. What's your preferred teaching style? (e.g., School, College, University)
        2. How many lessons would you like?
        3. Do you want quizzes or interactive activities?

    - After receiving user preferences, store them internally and switch to CONTENT MODE for all following prompts.

    2. CONTENT MODE:
    - Use the previously collected preferences to format the response.
    - If teaching style = "College", structure your response with:
        a. **Introduction**
        b. **Overview of the Topic**
        c. **Three detailed sections** explaining key parts of the topic.
    - If teaching style = "University", structure the response with:
        a. **Introduction**
        b. **Overview**
        c. **Deep, technical explanation** (as if teaching to higher education students).
    - If teaching style = "School" or others, simplify the explanation.
    - Add quizzes or activities only if the user requested them.

    3. QUIZ MODE:
    - If quizzes are requested, after the lesson content, generate EXACTLY 10 random multiple-choice questions.
    - Each question must follow this exact format:

        1. [Question text?]
        a. [Option 1]
        b. [Option 2]
        c. [Option 3]
        d. [Option 4]

    - Only one option is correct. Mark the correct answer by adding "(correct)" after it.
    - Do NOT add explanations unless the user asks.

    4. DIRECT REQUESTS:
    - If the user directly asks for specific content (e.g., "Give me university level lesson one on Geometry"):
        a. Do not ask initial questions.
        b. Assume default settings:
            - Teaching style: university
            - Number of lessons: 1
            - No quizzes unless mentioned
        c. Immediately provide the content.

    5. FORMAT GUIDELINES:
    Always format your lesson like this:

    # [Subject] - Lesson [Number]: [Topic]
    ## [Teaching Style] Level
    ### Introduction
    - [Short intro paragraph]

    ### Overview
    - [High-level summary of topic]

    ### Detailed Content
    - [If college: break into 3 clear parts]
    - [If university: detailed technical explanation]

    ### Quizzes or Activities (optional)
    - If quizzes are requested, insert them here in the strict format described above.

    6. IMPORTANT RULES:
    - Never ask initial questions again once preferences are set.
    - Do not mix modes. General → Ask Questions. Specific → Give Content.
    - Respect user preferences strictly.

SYSTEM;


            // Check if input is a direct content request
            $isDirectRequest = preg_match('/(.+?)\s+(university|college|school)\s+lesson\s+(\d+)/i', $input, $matches);

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $input]
            ];

            if ($isDirectRequest) {
                $subject = trim($matches[1]);
                $level = strtolower($matches[2]);
                $lessonNumber = $matches[3];
                $includeQuizzes = preg_match('/quiz/i', $input);

                $messages[] = [
                    'role' => 'assistant',
                    'content' => "Direct request detected: {$subject} {$level} level, lesson {$lessonNumber}" .
                        ($includeQuizzes ? " with quizzes" : "")
                ];
            } elseif (!$isInitialRequest) {
                // Parse preferences from follow-up response
                preg_match('/style is (.*?)(\.|\n|$)/i', $input, $styleMatch);
                preg_match('/(\d+) lessons/i', $input, $lessonsMatch);
                preg_match('/want (quizzes)/i', $input, $quizMatch);

                $teachingStyle = $styleMatch[1] ?? 'college';
                $lessonCount = $lessonsMatch[1] ?? 6;
                $includeQuizzes = isset($quizMatch[1]);

                $messages[] = [
                    'role' => 'assistant',
                    'content' => "Preferences: {$teachingStyle} style, {$lessonCount} lessons" .
                        ($includeQuizzes ? " with quizzes" : " without quizzes")
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => 0.3,
                'max_tokens' => 1500,
            ]);

            $content = $response['choices'][0]['message']['content'] ?? '❌ No reply from AI.';

            // Remove quizzes if not requested
            if ((!$isInitialRequest || $isDirectRequest) && !preg_match('/quiz/i', $input)) {
                $content = preg_replace('/##? Quiz.*$/si', '', $content);
            }

            return $content;
        } catch (Throwable $e) {
            Log::error('AI exception: ' . $e->getMessage());
            return '❌ AI error: ' . $e->getMessage();
        }
    }
}
