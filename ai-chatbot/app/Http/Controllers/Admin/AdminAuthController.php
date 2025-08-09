<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin(){ return view('admin.login'); }

    public function login(Request $r){
        $r->validate(['email'=>'required|email','password'=>'required']);
        $admin = Admin::where('email',$r->email)->first();
        if(!$admin || !Hash::check($r->password, $admin->password)){
            return back()->withErrors(['email'=>'Неверные данные']);
        }
        session(['admin_id'=>$admin->id]);
        return redirect()->route('admin.clients.index');
    }

    public function logout(){
        session()->forget('admin_id');
        return redirect()->route('admin.login');
    }
}
