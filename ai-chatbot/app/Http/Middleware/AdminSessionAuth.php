<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminSessionAuth {
    public function handle(Request $request, Closure $next){
        $id = session('admin_id');
        if (!$id) return redirect()->route('admin.login')->withErrors(['session'=>'Войдите как админ']);
        $admin = Admin::find($id);
        if (!$admin) { session()->forget('admin_id'); return redirect()->route('admin.login'); }
        $request->merge(['admin'=>$admin]);
        return $next($request);
    }
}
