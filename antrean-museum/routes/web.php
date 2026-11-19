<?php

use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QueueController::class, 'index'])->name('home');
Route::get('/antrean', [QueueController::class, 'index'])->name('queue.index');
Route::get('/antrean/admin', [QueueController::class, 'admin'])->name('queue.admin');
Route::post('/antrean/admin/ads', [QueueController::class, 'storeAd'])->name('queue.ads.store');
Route::patch('/antrean/admin/ads/{ad}/toggle', [QueueController::class, 'toggleAd'])->name('queue.ads.toggle');
Route::delete('/antrean/admin/ads/{ad}', [QueueController::class, 'destroyAd'])->name('queue.ads.destroy');
Route::get('/antrean/ads', [QueueController::class, 'adsData'])->name('queue.ads.data');
Route::get('/antrean/kiosk', [QueueController::class, 'kiosk'])->name('queue.kiosk');
Route::get('/antrean/data', [QueueController::class, 'getData'])->name('queue.data');
Route::post('/antrean/store', [QueueController::class, 'store'])->name('queue.store');
Route::post('/antrean/call-next', [QueueController::class, 'callNext'])->name('queue.call');
Route::delete('/antrean/delete/{id}', [QueueController::class, 'destroy'])->name('queue.destroy');
Route::delete('/antrean/delete-all', [QueueController::class, 'destroyAll'])->name('queue.destroyAll');
Route::get('/antrean/admin/history', [QueueController::class, 'history'])->name('queue.history');
Route::get('/antrean/admin/history/export', [QueueController::class, 'exportHistory'])->name('queue.history.export');
