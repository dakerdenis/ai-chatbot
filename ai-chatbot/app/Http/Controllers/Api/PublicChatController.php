<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenAI\Factory; // <-- добавили

class PublicChatController extends Controller
{
    public function handle(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'array|max:3',
            'history.*.q' => 'required|string|max:2000',
            'history.*.a' => 'required|string|max:2000',
        ]);

        /** @var \App\Models\Client $client */
        $client = $request->get('client');

        if ($client->dialog_used >= $client->dialog_limit) {
            return response()->json(['error' => 'DIALOG_LIMIT_REACHED'], 429);
        }

        $prompts = DB::table('client_prompts')
            ->where('client_id', $client->id)
            ->latest()
            ->limit($client->prompts_limit)
            ->pluck('content')
            ->toArray();

        $messages = [[
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
        ]];

        foreach ($prompts as $p) {
            $messages[] = ['role'=>'system','content'=>$p];
        }
        foreach ($request->input('history', []) as $h) {
            $messages[] = ['role'=>'user', 'content'=>$h['q']];
            $messages[] = ['role'=>'assistant', 'content'=>$h['a']];
        }
        $messages[] = ['role'=>'user','content'=>$request->input('message')];

        try {
            $oa = (new Factory())
                ->withApiKey((string) env('OPENAI_API_KEY'))
                ->make();

            $resp = $oa->chat()->create([
                'model'    => env('OPENAI_MODEL', 'gpt-4o-mini'),
                'messages' => $messages,
            ]);

            $answer = trim($resp->choices[0]->message->content ?? '...');

            // логируем только токены — помогает экономить
            $u = $resp->usage ?? null;
            Log::info('widget.tokens', [
                'client_id'  => $client->id,
                'prompt'     => $u->promptTokens     ?? null,
                'completion' => $u->completionTokens ?? null,
                'total'      => $u->totalTokens      ?? null,
            ]);

        } catch (\Throwable $e) {
    Log::error('ai.error', ['type'=>get_class($e), 'msg'=>$e->getMessage()]);
    return response()->json(['error' => 'AI_EXCEPTION'], 500);
}

        $client->increment('dialog_used');
        $client->update(['last_active_at'=>now()]);

        return response()->json([
            'answer'      => $answer,
            'dialog_used' => $client->dialog_used,
            'limit'       => $client->dialog_limit,
        ]);
    }
}
