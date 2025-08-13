<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use OpenAI\Factory;

class DemoChatController extends Controller
{
    public function handle(Request $request)
    {
        // Стало (вариант А — строгая проверка тела):
        if (!$request->isJson()) {
            return response()->json(['error' => 'Content-Type must be application/json'], 415);
        }

        // Доп. анти‑спам (поверх throttle:demo-chat в routes/api.php)
        $rlKey = 'demo:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rlKey, 30)) { // 30 запросов/час
            return response()->json(['error' => 'Too many requests'], 429);
        }
        RateLimiter::hit($rlKey, 3600);

        // Базовая валидация
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'array|max:3',
            'history.*.q' => 'required|string|max:2000',
            'history.*.a' => 'required|string|max:2000',
        ]);

        // Нормализация строки (без HTML, юникод норм., обрезка)
        $sanitize = function (string $s, int $limit = 600): string {
            $s = strip_tags($s);
            $s = preg_replace('/\s+/u', ' ', $s ?? '');
            $s = trim($s);
            // Laravel Str::limit безопасно режет по графемам
            return Str::limit($s, $limit, '…');
        };

        $userMessage = mb_strtolower($sanitize($request->input('message'), 600));

        // 1) FAQ‑шорткат (дешево, без обращения к LLM)
        $staticFaq = config('demo_faq', []);
        if (isset($staticFaq[$userMessage])) {
            // Только факт использования FAQ, без текста
            Log::info('demo.faq_hit');
            return response()->json(['answer' => $staticFaq[$userMessage]]);
        }

        // 2) Формируем безопасный контекст
        $history = [];
        foreach ((array) $request->input('history', []) as $h) {
            $q = $sanitize((string) ($h['q'] ?? ''), 280);
            $a = $sanitize((string) ($h['a'] ?? ''), 280);
            if ($q !== '' && $a !== '') {
                $history[] = ['role' => 'user', 'content' => $q];
                $history[] = ['role' => 'assistant', 'content' => $a];
            }
        }
        $cfg = config('demo_prompt');
        $system = (string) ($cfg['system'] ?? '');
        $facts = (array) ($cfg['facts'] ?? []);
        $gen = (array) ($cfg['gen'] ?? []);

        $messages = array_merge(
            [
                ['role' => 'system', 'content' => $system],
                ['role' => 'system', 'content' => (string) ($facts['services'] ?? '')],
                ['role' => 'system', 'content' => (string) ($facts['contacts'] ?? '')],
            ],
            $history,
            [['role' => 'user', 'content' => $sanitize($request->input('message'), 600)]]
        );

        try {
            // HTTP‑клиент с таймаутами
            $client = (new \OpenAI\Factory())
                ->withApiKey((string) config('openai.api_key')) // <-- было env()
                ->withHttpClient(new \GuzzleHttp\Client([
                    'timeout' => 12.0,
                    'connect_timeout' => 4.0,
                ]))
                ->make();

            $resp = $client->chat()->create([
                'model' => (string) ($gen['model'] ?? config('openai.model')),
                'messages' => $messages,
                'max_tokens' => (int) ($gen['max_tokens'] ?? 150),
                'temperature' => (float) ($gen['temperature'] ?? 0.4),
                'user' => 'demo:' . $request->ip(),
            ]);


            $answer = trim($resp->choices[0]->message->content ?? '...');
            $usage = $resp->usage ?? null;

            // Логи только по токенам
            Log::info('demo.tokens', [
                'prompt' => $usage->promptTokens ?? null,
                'completion' => $usage->completionTokens ?? null,
                'total' => $usage->totalTokens ?? null,
            ]);

            return response()->json(['answer' => $answer]);
        } catch (\Throwable $e) {
            Log::error('ai.error', ['type' => get_class($e), 'msg' => $e->getMessage()]);
            return response()->json(['error' => 'AI_EXCEPTION'], 500);
        }
    }
}
