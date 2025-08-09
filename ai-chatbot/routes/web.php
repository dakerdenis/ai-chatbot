<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\ClientPromptController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminClientController;


Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/widget.js', [LandingController::class, 'widget'])->name('widget.js');


// клиент
Route::middleware('guest')->group(function () {
    Route::get('/client/login', [ClientAuthController::class, 'showLogin'])->name('client.login');
    Route::post('/client/login', [ClientAuthController::class, 'login']);
});
Route::post('/client/logout', [ClientAuthController::class, 'logout'])->name('client.logout');

Route::middleware('client.auth')->group(function () {
    Route::get('/client/dashboard', [ClientAuthController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/client/prompts', [ClientPromptController::class, 'index'])->name('client.prompts.index');
    Route::post('/client/prompts', [ClientPromptController::class, 'store'])->name('client.prompts.store');
    Route::put('/client/prompts/{prompt}', [ClientPromptController::class, 'update'])->name('client.prompts.update');
    Route::delete('/client/prompts/{prompt}', [ClientPromptController::class, 'destroy'])->name('client.prompts.destroy');
    Route::post('/client/prompts/compress', [ClientPromptController::class, 'compress'])->name('client.prompts.compress');
});

// админ
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login']);
});
Route::middleware('admin.auth')->prefix('admin')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/clients', [AdminClientController::class, 'index'])->name('admin.clients.index');
    Route::get('/clients/create', [AdminClientController::class, 'create'])->name('admin.clients.create');
    Route::post('/clients', [AdminClientController::class, 'store'])->name('admin.clients.store');
    Route::get('/clients/{client}/edit', [AdminClientController::class, 'edit'])->name('admin.clients.edit');
    Route::put('/clients/{client}', [AdminClientController::class, 'update'])->name('admin.clients.update');
    Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('admin.clients.destroy');
    Route::delete('/clients/{client}/domains/{domain}', [AdminClientController::class, 'destroyDomain'])->name('admin.clients.domains.destroy');
});
