<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicChatController;
use App\Http\Controllers\Api\DemoChatController;

// Виджет: требует X-API-TOKEN, лимит 10/мин
Route::middleware(['throttle:client-chat','auth.client'])
    ->post('/public-chat', [PublicChatController::class, 'handle']);

// Лендинг: без токена, лимит 10/мин
Route::middleware(['throttle:demo-chat'])
    ->post('/demo-chat', [\App\Http\Controllers\Api\DemoChatController::class, 'handle']);


Route::get('/ping', fn() => response()->json(['ok'=>true]));
