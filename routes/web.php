<?php

use App\Http\Controllers\TranslateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TranslateController::class, 'index'])->name('home');
Route::post('/translate', [TranslateController::class, 'translate'])->name('translate')->withoutMiddleware('web');
Route::get('/history', [TranslateController::class, 'history'])->name('history');
Route::get('/download/{id}', [TranslateController::class, 'downloadAudio'])->name('download.audio');
Route::post('/cleanup', [TranslateController::class, 'cleanup'])->name('cleanup')->withoutMiddleware('web');
