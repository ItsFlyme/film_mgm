<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\StaffAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QueueController::class, 'index'])->name('home');
Route::get('/antrean', [QueueController::class, 'index'])->name('queue.index');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::get('/antrean/data', [QueueController::class, 'monitorData'])->name('queue.data');
Route::get('/antrean/ads', [QueueController::class, 'adsData'])->name('queue.ads.data');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/antrean/kiosk', [QueueController::class, 'kiosk'])->name('queue.kiosk');
    Route::post('/antrean/store', [QueueController::class, 'store'])->name('queue.store');

    Route::get('/antrean/admin', [QueueController::class, 'admin'])->name('queue.admin');
    Route::get('/antrean/admin/data', [QueueController::class, 'getData'])->name('queue.admin.data');
    Route::post('/antrean/admin/ads', [QueueController::class, 'storeAd'])->name('queue.ads.store');
    Route::patch('/antrean/admin/ads/{ad}/toggle', [QueueController::class, 'toggleAd'])->name('queue.ads.toggle');
    Route::delete('/antrean/admin/ads/{ad}', [QueueController::class, 'destroyAd'])->name('queue.ads.destroy');
    Route::post('/antrean/call-next', [QueueController::class, 'callNext'])->name('queue.call');
    Route::delete('/antrean/delete/{id}', [QueueController::class, 'destroy'])->name('queue.destroy');
    Route::delete('/antrean/delete-all', [QueueController::class, 'destroyAll'])->name('queue.destroyAll');
    Route::get('/antrean/admin/history', [QueueController::class, 'history'])->name('queue.history');
    Route::get('/antrean/admin/history/export', [QueueController::class, 'exportHistory'])->name('queue.history.export');

    Route::middleware('can:manage-accounts')->prefix('/antrean/admin/accounts')->name('queue.accounts.')->group(function () {
        Route::get('/', [StaffAccountController::class, 'index'])->name('index');
        Route::post('/', [StaffAccountController::class, 'store'])->name('store');
        Route::patch('/{user}', [StaffAccountController::class, 'update'])->name('update');
        Route::delete('/{user}', [StaffAccountController::class, 'destroy'])->name('destroy');
    });
});
