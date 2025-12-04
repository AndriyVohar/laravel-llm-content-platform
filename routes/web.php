<?php

use App\Http\Controllers\AIChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Home');
});

Route::post('/ai-chat', [AIChatController::class, 'send']);
