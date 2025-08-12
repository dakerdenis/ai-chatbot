<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client; // проверь, что модель именно тут

class WidgetController extends Controller
{
    /**
     * Рендер iframe-страницы виджета по api_token.
     */
public function show(Request $request, string $token)
{
    $token  = trim(urldecode($token));
    $client = \App\Models\Client::where('api_token', $token)->first();

    if (!$client) {
        return response()->view('widget-invalid', [], 404);
    }

    return view('widget', ['client' => $client]);
}

}
