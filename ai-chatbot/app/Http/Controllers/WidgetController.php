<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class WidgetController extends Controller
{
    public function show(Request $request, string $token)
    {
        $token  = trim(urldecode($token));
        $site   = (string) $request->query('site', '');

        $client = Client::where('api_token', $token)->first();

        if (!$client) {
            // вместо дефолтного «тёмного» 404 отдаём свою страницу
            return response()->view('widget-invalid', [], 404);
        }

        return view('widget', [
            'client' => $client,
            'site'   => $site, // <- пробрасываем
        ]);
    }
}
