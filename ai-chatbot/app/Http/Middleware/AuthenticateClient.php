<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Client;

class AuthenticateClient
{
    public function handle(Request $request, Closure $next)
    {
        $token = (string) $request->header('X-API-TOKEN');
        $host  = $request->getHost(); // например 127.0.0.1
        $host  = preg_replace('/:\d+$/', '', $host); // на всякий

        if (!$token) {
            Log::warning('auth.client: missing token', ['host'=>$host, 'ua'=>$request->userAgent()]);
            return response()->json(['error'=>'Unauthorized client'], 401);
        }

        /** @var Client|null $client */
        $client = Client::where('api_token', $token)->first();

        if (!$client) {
            Log::warning('auth.client: invalid token', ['host'=>$host, 'token_tail'=>substr($token, -6)]);
            return response()->json(['error'=>'Unauthorized client'], 401);
        }

        if (!$client->is_active) {
            Log::warning('auth.client: inactive client', ['client_id'=>$client->id]);
            return response()->json(['error'=>'Client inactive'], 403);
        }

        // проверка домена (client_domains.domain == $host)
        $allowed = $client->domains()->where('domain', $host)->exists();
        if (!$allowed) {
            Log::warning('auth.client: domain not allowed', [
                'client_id'=>$client->id, 'host'=>$host
            ]);
            return response()->json(['error'=>'Domain not allowed'], 403);
        }

        // всё ок — прокидываем клиента дальше
        $request->attributes->set('client', $client);
        Log::info('auth.client: ok', ['client_id'=>$client->id, 'host'=>$host]);

        return $next($request);
    }
}
