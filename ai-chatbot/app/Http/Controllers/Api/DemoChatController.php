<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class DemoChatController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('OPENAI key check', ['key' => substr((string)config('openai.api_key'), 0, 10) . '...']);

        Log::info('DemoChat: incoming request', [
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
            'payload' => $request->all(),
        ]);

        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'array|max:3',
            'history.*.q' => 'required|string|max:2000',
            'history.*.a' => 'required|string|max:2000',
        ]);

        $demoPrompts = [
            "SITE: D.A.I. — demo AI assistant for a software studio.",
            "SERVICES: Chatbot integration, AI assistants for websites, prompt design, usage analytics.",
            "CONTACTS: hello@daker.example, Mon–Fri 10:00–18:00.",
            "SCOPE: Only answer about our services and how to integrate the widget. If asked about other topics, say it's out of scope.",
        ];

        $messages = [
            [
                'role' => 'system',
                'content' =>
"ROLE: You are a concise website chatbot for the client.
RULES:
- Answer in the user's language.
- Keep answers extremely short (1–5 short sentences, ~60–80 words max).
- Use ONLY the client-provided prompts below as knowledge.
- If the question is off-topic or not covered, briefly say it's outside the site scope and suggest what's relevant.
- Do not invent facts, URLs or contacts not present in prompts.
OUTPUT: Plain text, no markdown, no lists unless necessary."
            ],
        ];

        foreach ($demoPrompts as $p) {
            $messages[] = ['role' => 'system', 'content' => $p];
        }

        foreach ($request->input('history', []) as $h) {
            $messages[] = ['role' => 'user', 'content' => $h['q']];
            $messages[] = ['role' => 'assistant', 'content' => $h['a']];
        }

        $messages[] = ['role' => 'user', 'content' => $request->input('message')];

        Log::debug('DemoChat: prepared messages for OpenAI', $messages);

        try {
            $resp = OpenAI::chat()->create([
                'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages'=> $messages,
            ]);

            $answer = trim($resp->choices[0]->message->content ?? '...');
            Log::info('DemoChat: got response from OpenAI', ['answer' => $answer]);

        } catch (\Throwable $e) {
            Log::error('DemoChat: AI_EXCEPTION', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'AI_EXCEPTION',
                'details' => $e->getMessage(),
            ], 500);
        }

        return response()->json(['answer' => $answer]);
    }
}
