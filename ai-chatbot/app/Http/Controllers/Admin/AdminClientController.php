<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\ClientDomain;
use Illuminate\Support\Str;

class AdminClientController extends Controller
{
    public function index(){
        $clients = Client::latest()->paginate(20);
        return view('admin.clients.index', compact('clients'));
    }

    public function create(){ return view('admin.clients.create'); }

    public function store(Request $r){
        $r->validate([
            'name'=>'required','email'=>'required|email|unique:clients,email',
            'password'=>'required|min:6',
            'plan'=>'required|in:trial,basic,standard,premium',
            'dialog_limit'=>'required|integer|min:0',
            'prompts_limit'=>'required|integer|min:1',
            'prompt_max_length'=>'required|integer|min:100',
            'rate_limit'=>'required|integer|min:1',
        ]);
        $client = Client::create([
            'name'=>$r->name,
            'email'=>$r->email,
            'password'=>bcrypt($r->password),
            'api_token'=>Str::random(40),
            'plan'=>$r->plan,
            'dialog_limit'=>$r->dialog_limit,
            'prompts_limit'=>$r->prompts_limit,
            'prompt_max_length'=>$r->prompt_max_length,
            'rate_limit'=>$r->rate_limit,
            'is_active'=>$r->boolean('is_active', true),
        ]);
        foreach (array_filter(array_map('trim', explode(',', $r->domains ?? ''))) as $d) {
            $client->domains()->create(['domain'=>$d]);
        }
        return redirect()->route('admin.clients.index')->with('success','Клиент создан');
    }

    public function edit(Client $client){
        $domains = $client->domains()->pluck('domain')->implode(',');
        return view('admin.clients.edit', compact('client','domains'));
    }

    public function update(Request $r, Client $client){
        $r->validate([
            'name'=>'required',
            'plan'=>'required|in:trial,basic,standard,premium',
            'dialog_limit'=>'required|integer|min:0',
            'prompts_limit'=>'required|integer|min:1',
            'prompt_max_length'=>'required|integer|min:100',
            'rate_limit'=>'required|integer|min:1',
        ]);
        $client->update([
            'name'=>$r->name,
            'plan'=>$r->plan,
            'dialog_limit'=>$r->dialog_limit,
            'prompts_limit'=>$r->prompts_limit,
            'prompt_max_length'=>$r->prompt_max_length,
            'rate_limit'=>$r->rate_limit,
            'is_active'=>$r->boolean('is_active', true),
        ]);
        if ($r->filled('password')) $client->update(['password'=>bcrypt($r->password)]);
        // домены — перезапишем
        $client->domains()->delete();
        foreach (array_filter(array_map('trim', explode(',', $r->domains ?? ''))) as $d) {
            $client->domains()->create(['domain'=>$d]);
        }
        return back()->with('success','Сохранено');
    }

    public function destroy(Client $client){
        $client->delete();
        return back()->with('success','Удалён');
    }

    public function destroyDomain(Client $client, ClientDomain $domain){
        abort_unless($domain->client_id === $client->id, 403);
        $domain->delete();
        return back()->with('success','Домен удалён');
    }
}
