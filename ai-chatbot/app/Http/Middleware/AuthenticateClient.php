<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Client;

class AuthenticateClient
{
public function handle(Request $request, \Closure $next)
{
    $token = (string) $request->header('X-API-TOKEN', '');
    if ($token === '') {
        Log::warning('auth.client: missing token', ['ua'=>$request->userAgent()]);
        return response()->json(['error'=>'Unauthorized client'], 401);
    }

    /** @var \App\Models\Client|null $client */
    $client = \App\Models\Client::where('api_token', $token)->first();
    if (!$client) {
        Log::warning('auth.client: invalid token', ['tail'=>substr($token,-6)]);
        return response()->json(['error'=>'Unauthorized client'], 401);
    }

    if (!$client->is_active) {
        Log::warning('auth.client: inactive', ['client_id'=>$client->id]);
        return response()->json(['error'=>'Client inactive'], 403);
    }

    // достаём домен из заголовка, иначе Origin/Referer
    $host = (string) $request->header('X-CLIENT-SITE', '');
    if ($host === '') {
        $origin  = parse_url((string) $request->headers->get('Origin'),  PHP_URL_HOST) ?: '';
        $referer = parse_url((string) $request->headers->get('Referer'), PHP_URL_HOST) ?: '';
        $host = $origin ?: $referer;
    }
    $host = $this->normalize($host);

    // список разрешённых доменов; пустой = разрешить все
    $allowed = $client->domains()->pluck('domain')->map(fn($d)=>$this->normalize($d))->filter()->values()->all();

    if (!empty($allowed) && !in_array($host, $allowed, true)) {
        Log::warning('auth.client: domain not allowed', ['client_id'=>$client->id, 'host'=>$host, 'allowed'=>$allowed]);
        return response()->json(['error'=>'Domain not allowed'], 403);
    }

    $request->attributes->set('client', $client);
    return $next($request);
}

private function normalize(?string $h): string
{
    $h = strtolower(trim((string)$h));
    $h = preg_replace('/^https?:\/\//','',$h);
    $h = preg_replace('/^www\./','',$h);
    $h = preg_replace('/:\d+$/','',$h);
    return $h;
}

}
