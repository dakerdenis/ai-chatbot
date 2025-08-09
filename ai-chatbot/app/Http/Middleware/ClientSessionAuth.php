<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Client;

class ClientSessionAuth {
    public function handle(Request $request, Closure $next){
        $id = session('client_id');
        if (!$id) return redirect()->route('client.login')->withErrors(['session'=>'Войдите снова']);
        $client = Client::find($id);
        if (!$client || !$client->is_active) {
            session()->forget(['client_id','client_ip']);
            return redirect()->route('client.login')->withErrors(['session'=>'Доступ запрещён']);
        }
        $client->last_active_at = now(); $client->save();
        $request->merge(['client'=>$client]);
        return $next($request);
    }
}
