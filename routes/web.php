<?php

use App\Http\Controllers\TranslateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TranslateController::class, 'index'])->name('home');
Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'env' => app()->environment(),
        'debug' => config('app.debug'),
        'db' => config('database.default'),
        'time' => now()
    ]);
});
Route::post('/translate', [TranslateController::class, 'translate'])->name('translate');
Route::get('/history', [TranslateController::class, 'history'])->name('history');
Route::get('/download/{id}', [TranslateController::class, 'downloadAudio'])->name('download.audio');
Route::post('/cleanup', [TranslateController::class, 'cleanup'])->name('cleanup');
