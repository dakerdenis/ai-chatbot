<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class AuthenticateClient
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-API-TOKEN');
        if (!$token) return response()->json(['error'=>'API token missing'], 401);

        $client = Client::where('api_token', $token)->first();
        if (!$client || !$client->is_active) return response()->json(['error'=>'Unauthorized client'], 403);

        // Проверка домена (на старте можно закомментировать, если мешает)
        $origin = $request->headers->get('origin') ?? $request->headers->get('referer');
        if ($origin) {
            $host = parse_url($origin, PHP_URL_HOST);
            if (!$host || !$client->domains()->where('domain', $host)->exists()) {
                return response()->json(['error'=>'Unauthorized domain'], 403);
            }
        }

        $request->merge(['client'=>$client]);

        DB::table('client_usage_logs')->insert([
            'client_id'=>$client->id,'endpoint'=>$request->path(),'method'=>$request->method(),
            'payload'=>json_encode($request->except(['password','api_token']), JSON_PARTIAL_OUTPUT_ON_ERROR),
            'ip_address'=>$request->ip(),'created_at'=>now(),
        ]);

        return $next($request);
    }
}
