<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ClientAuthController extends Controller
{
    public function showLogin(){ return view('client.login'); }

    public function login(Request $r){
        $r->validate(['email'=>'required|email','password'=>'required']);
        $client = Client::where('email',$r->email)->first();
        if(!$client || !Hash::check($r->password, $client->password)){
            return back()->withErrors(['email'=>'Неверные данные']);
        }
        session(['client_id'=>$client->id, 'client_ip'=>$r->ip()]);
        return redirect()->route('client.dashboard');
    }

    public function logout(){
        session()->forget(['client_id','client_ip']);
        return redirect()->route('client.login');
    }

    public function dashboard(){
        $client = \App\Models\Client::find(session('client_id'));
        return view('client.dashboard', compact('client'));
    }
}
