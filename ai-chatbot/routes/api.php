<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicChatController;

Route::middleware(['throttle:client-chat','auth.client'])
    ->post('/public-chat', [PublicChatController::class, 'handle']);
Route::get('/ping', fn() => response()->json(['ok'=>true]));

