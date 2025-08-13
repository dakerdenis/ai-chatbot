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


        // 1. FAQ (точное совпадение, как и было)
        $staticFaq = config('demo_faq', []);
        if (isset($staticFaq[$userMessage])) {
            Log::info('demo.faq_hit', ['q' => $userMessage]);
            return response()->json(['answer' => $staticFaq[$userMessage]]);
        }

        // 2. Шорткат «тарифы/цены» по подстроке (язык авто)
        $prices = config('demo_prices');
        $patterns = $prices['patterns'] ?? [];
        $answers = $prices['answers'] ?? [];

        $guessLang = function (string $s): string {
            if (preg_match('/[əğıöşçüİ]/iu', $s))
                return 'az';
            if (preg_match('/[а-яё]/iu', $s))
                return 'ru';
            return 'en';
        };
        $lang = $guessLang($userMessage);

        // проверяем ключевые слова ВСЕХ языков, чтобы поймать кросс‑запросы
        $hit = false;
        foreach ($patterns as $lng => $list) {
            foreach ($list as $needle) {
                if ($needle !== '' && mb_stripos($userMessage, $needle) !== false) {
                    $lang = $lng; // зафиксируем язык по найденному паттерну
                    $hit = true;
                    break 2;
                }
            }
        }
        if ($hit && !empty($answers[$lang] ?? null)) {
            Log::info('demo.shortcut_prices', ['lang' => $lang, 'q' => $userMessage]);
            return response()->json(['answer' => $answers[$lang]]);
        }




        // 2) Формируем безопасный контекст
        $history = [];


        $cfg = config('demo_prompt');
        $system = (string) ($cfg['system'] ?? '');
        $facts = (array) ($cfg['facts'] ?? []);
        $gen = (array) ($cfg['gen'] ?? []);

        // 1) system + ВСЕ факты отдельными system-сообщениями
        $messages = [
            ['role' => 'system', 'content' => $system],
        ];
        foreach ($facts as $fact) {
            $fact = (string) $fact;
            if ($fact !== '') {
                $messages[] = ['role' => 'system', 'content' => $fact];
            }
        }

        // 2) история (до 3 Q/A)
        foreach ((array) $request->input('history', []) as $h) {
            $q = $sanitize((string) ($h['q'] ?? ''), 280);
            $a = $sanitize((string) ($h['a'] ?? ''), 280);
            if ($q !== '' && $a !== '') {
                $messages[] = ['role' => 'user', 'content' => $q];
                $messages[] = ['role' => 'assistant', 'content' => $a];
            }
        }

        // 3) текущий вопрос
        $messages[] = ['role' => 'user', 'content' => $sanitize($request->input('message'), 600)];

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
                'max_tokens' => (int) ($gen['max_tokens'] ?? 220), // ← больше места для сравнения тарифов
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
