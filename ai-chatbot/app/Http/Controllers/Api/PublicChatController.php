<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class PublicChatController extends Controller
{
    public function handle(Request $request)
    {
        // auth.client middleware уже проставил $request->get('client')
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'array|max:3',
            'history.*.q' => 'required|string|max:2000',
            'history.*.a' => 'required|string|max:2000',
        ]);

        $client = $request->get('client');

        // квота диалогов
        if ($client->dialog_used >= $client->dialog_limit) {
            return response()->json(['error'=>'DIALOG_LIMIT_REACHED'], 429);
        }

        // берём промты клиента (не больше лимита)
        $prompts = DB::table('client_prompts')
            ->where('client_id',$client->id)
            ->latest()
            ->limit($client->prompts_limit)
            ->pluck('content')
            ->toArray();

        // собираем сообщения
        $messages = [
            [
                'role'=>'system',
                'content'=>
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
        foreach ($prompts as $p) {
            $messages[] = ['role'=>'system','content'=>$p];
        }
        // 3 последних QA из history
        foreach ($request->input('history', []) as $h) {
            $messages[] = ['role'=>'user', 'content'=>$h['q']];
            $messages[] = ['role'=>'assistant', 'content'=>$h['a']];
        }
        // текущий вопрос
        $messages[] = ['role'=>'user','content'=>$request->input('message')];

        try {
            $resp = OpenAI::chat()->create([
                'model'   => env('OPENAI_MODEL','gpt-4o-mini'),
                'messages'=> $messages,
            ]);
            $answer = trim($resp->choices[0]->message->content ?? '...');
        } catch (\Throwable $e) {
            return response()->json(['error'=>'AI_EXCEPTION','details'=>$e->getMessage()], 500);
        }

        // учёт квоты
        $client->increment('dialog_used');
        $client->update(['last_active_at'=>now()]);

        return response()->json([
            'answer'      => $answer,
            'dialog_used' => $client->dialog_used,
            'limit'       => $client->dialog_limit,
        ]);
    }
}
