<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Factory;

class DemoChatController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $demoPrompts = [
            "SITE: D.A.I. — demo AI assistant for a software studio.",
            "SERVICES: Chatbot integration, AI assistants for websites, prompt design, usage analytics.",
            "CONTACTS: contact@daker.az, Mon–Fri 10:00–18:00, +994507506901.",
            "HOW_IT_WORKS: We connect our chatbot widget to your site via simple code. You write prompts, we handle the AI processing and analytics. Works 24/7.",
        ];

        // Более свободный системный prompt
        $messages = [[
            'role' => 'system',
            'content' =>
"ROLE: Short, friendly D.A.I. chatbot. Reply in user’s lang, ≤4 short sentences. Base: service info, contacts, how it works. Off-topic → short decline. Polite humor if insulted."
        ]];

        foreach ($demoPrompts as $p) {
            $messages[] = ['role' => 'system', 'content' => $p];
        }

        $userMessage = trim((string) $request->input('message'));
        if (mb_strlen($userMessage) > 500) {
            $userMessage = mb_substr($userMessage, 0, 500);
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        try {
            $client = (new Factory())
                ->withApiKey(env('OPENAI_API_KEY'))
                ->make();

            $resp = $client->chat()->create([
                // более дешёвая модель
                'model'       => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
                'messages'    => $messages,
                'max_tokens'  => 90,  // чуть больше для полноты ответа
                'temperature' => 0.9, // больше свободы и немного креатива
                'top_p'       => 1.0,
                'user'        => 'demo:' . $request->ip(),
            ]);

            $answer = trim($resp->choices[0]->message->content ?? '...');
            $usage  = $resp->usage ?? null;

            Log::info('demo.tokens', [
                'prompt'     => $usage->promptTokens    ?? null,
                'completion' => $usage->completionTokens ?? null,
                'total'      => $usage->totalTokens      ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('demo.ai_error', ['error' => $e->getMessage()]);
            return response()->json([
                'error'   => 'AI_EXCEPTION',
                'details' => $e->getMessage(),
            ], 500);
        }

        return response()->json(['answer' => $answer]);
    }
}
