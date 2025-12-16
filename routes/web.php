<?php

use App\Http\Controllers\AIChatController;
use App\Http\Controllers\BiographyController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('biographies.index');
});

// Біографії
Route::get('/biographies', [BiographyController::class, 'index'])->name('biographies.index');
Route::get('/biographies/{biography}', [BiographyController::class, 'show'])->name('biographies.show');
Route::post('/biographies', [BiographyController::class, 'store'])->name('biographies.store');
Route::delete('/biographies/{biography}', [BiographyController::class, 'destroy'])->name('biographies.destroy');

// Новини
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');
Route::post('/news', [NewsController::class, 'store'])->name('news.store');
Route::delete('/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');

// Чат
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
Route::post('/chat/clear', [ChatController::class, 'clear'])->name('chat.clear');

// Старий маршрут (можна видалити якщо не потрібен)
Route::post('/ai-chat', [AIChatController::class, 'send']);
