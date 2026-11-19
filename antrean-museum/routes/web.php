<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueueController;

Route::get('/antrean', [QueueController::class, 'index'])->name('queue.index');
Route::get('/antrean/admin', [QueueController::class, 'admin'])->name('queue.admin');
Route::get('/antrean/kiosk', [QueueController::class, 'kiosk'])->name('queue.kiosk');
Route::get('/antrean/data', [QueueController::class, 'getData'])->name('queue.data');
Route::post('/antrean/store', [QueueController::class, 'store'])->name('queue.store');
Route::post('/antrean/call-next', [QueueController::class, 'callNext'])->name('queue.call');
Route::delete('/antrean/delete/{id}', [QueueController::class, 'destroy'])->name('queue.destroy');
Route::delete('/antrean/delete-all', [QueueController::class, 'destroyAll'])->name('queue.destroyAll');
Route::get('/antrean/admin/history', [QueueController::class, 'history'])->name('queue.history');