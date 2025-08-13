<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OpenAiDiagController extends Controller
{
    public function ping(Request $request)
    {
$key = (string) config('openai.api_key');   // <-- вместо env('OPENAI_API_KEY')
if ($key === '') {
    return response()->json(['ok'=>false,'where'=>'config','err'=>'openai.api_key missing'], 500);
}

        try {
            $client = new Client([
                'base_uri'        => 'https://api.openai.com/',
                'timeout'         => 12.0,
                'connect_timeout' => 4.0,
                // Если на хостинге проблемы с корневыми сертификатами, можно временно снять verify.
                // Лучше оставить true, но если будет cURL error 60 — поставь false на 1 проверку:
                'verify'          => true,
            ]);

            $res = $client->get('v1/models', [
                'headers' => [
                    'Authorization' => 'Bearer '.$key,
                ],
            ]);

            $status = $res->getStatusCode();
            Log::info('diag.openai', ['status'=>$status]);

            return response()->json([
                'ok'     => $status === 200,
                'status' => $status,
            ]);
        } catch (\Throwable $e) {
            Log::error('diag.openai_error', ['msg'=>$e->getMessage(), 'type'=>get_class($e)]);
            return response()->json([
                'ok'   => false,
                'type' => get_class($e),
                'err'  => $e->getMessage(),
            ], 500);
        }
    }
}
