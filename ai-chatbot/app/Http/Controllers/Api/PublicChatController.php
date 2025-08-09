<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class PublicChatController extends Controller
{
    public function handle(Request $request)
    {
       // $mw = app(\App\Http\Middleware\AuthenticateClient::class);
       // $resp = $mw->handle($request, fn($r)=>null);
       // if ($resp !== null) return $resp;

        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'array|max:3',
            'history.*.q' => 'required|string|max:2000',
            'history.*.a' => 'required|string|max:2000',
        ]);

        $client = $request->get('client');
        if ($client->dialog_used >= $client->dialog_limit) {
            return response()->json(['error'=>'DIALOG_LIMIT_REACHED'], 429);
        }

        $prompts = DB::table('client_prompts')
            ->where('client_id',$client->id)
            ->latest()->limit($client->prompts_limit)
            ->pluck('content')->toArray();

        $messages = [
            ['role'=>'system','content'=>
                'Ты чат-бот сайта клиента. Отвечай кратко (до ~100 слов) на языке пользователя. ' .
                'Используй ТОЛЬКО информацию из клиентских промтов ниже. Если вопрос не по теме — сообщи об этом.' ],
        ];
        foreach ($prompts as $p) $messages[] = ['role'=>'system','content'=>$p];
        foreach ($request->input('history', []) as $h) {
            $messages[] = ['role'=>'user','content'=>$h['q']];
            $messages[] = ['role'=>'assistant','content'=>$h['a']];
        }
        $messages[] = ['role'=>'user','content'=>$request->input('message')];

        try {
            Log::info('OpenAI request start', ['model' => env('OPENAI_MODEL'), 'messages_count' => count($messages)]);
            $ai = OpenAI::chat()->create([
                'model'=>env('OPENAI_MODEL','gpt-4o-mini'),
                'messages'=>$messages,
            ]);
            $answer = trim($ai->choices[0]->message->content ?? '...');
            Log::info('OpenAI response ok', ['answer_len' => strlen($answer)]);
        } catch (\Throwable $e) {
            Log::error('OpenAI failed', ['msg'=>$e->getMessage(), 'code'=>$e->getCode(), 'trace'=>$e->getTraceAsString()]);
            return response()->json(['error'=>'AI_EXCEPTION','details'=>$e->getMessage()], 500);
        }
        
        $client->increment('dialog_used');
        $client->update(['last_active_at'=>now()]);

        return response()->json([
            'answer'=>$answer,
            'dialog_used'=>$client->dialog_used,
            'limit'=>$client->dialog_limit,
        ]);
    }
}
