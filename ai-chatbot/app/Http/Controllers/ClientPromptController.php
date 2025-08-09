<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class ClientPromptController extends Controller
{
    public function index(){
        $client = request()->get('client');
        $prompts = $client->prompts()->latest()->get();
        return view('client.prompts', compact('client','prompts'));
    }

    public function store(Request $r){
        $client = request()->get('client');
        $r->validate([
            'title'=>'required|string|max:100',
            'content'=>'required|string|max:'.$client->prompt_max_length,
        ]);
        if ($client->prompts()->count() >= $client->prompts_limit) {
            return back()->withErrors(['limit'=>'Достигнут лимит промтов по тарифу']);
        }
        $client->prompts()->create($r->only('title','content'));
        return back()->with('success','Промт добавлен');
    }

    public function update(Request $r, \App\Models\ClientPrompt $prompt){
        $client = request()->get('client');
        abort_unless($prompt->client_id === $client->id, 403);
    
        $r->validate([
            'content' => 'required|string|max:'.$client->prompt_max_length,
        ]);
    
        $prompt->update([
            'content' => $r->input('content'),
        ]);
    
        return back()->with('success','Промт обновлён');
    }
    

    public function destroy(\App\Models\ClientPrompt $prompt){
        $client = request()->get('client');
        abort_unless($prompt->client_id === $client->id, 403);
        $prompt->delete();
        return back()->with('success','Промт удалён');
    }

    public function compress(Request $r){
        $r->validate(['text'=>'required|string|max:2000']);
        try{
            $res = OpenAI::chat()->create([
                'model'=>env('OPENAI_MODEL','gpt-4o-mini'),
                'messages'=>[
                    ['role'=>'system','content'=>'Сократи и переформулируй, сохраняя смысл.'],
                    ['role'=>'user','content'=>$r->input('text')],
                ],
            ]);
            return response()->json(['success'=>true,'result'=>trim($res->choices[0]->message->content)]);
        }catch(\Throwable $e){
            return response()->json(['success'=>false,'error'=>$e->getMessage()],500);
        }
    }
}
