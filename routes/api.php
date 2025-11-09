<?php

use App\Http\Controllers\ItemMovementsSyncController;
use App\Http\Controllers\Panel\ItemsSyncController;
use App\Http\Controllers\Panel\PaymentsSyncController;
use App\Http\Controllers\Panel\ReservationsSyncController;
use App\Http\Controllers\Panel\SalesSyncController;
use App\Http\Controllers\Panel\SyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('panel')->group(function () {
    Route::get('sync/{entity}', [SyncController::class, 'sync']);
    Route::post('sync/{entity}/confirm', [SyncController::class, 'confirm']);
    Route::post('sync-all', [SyncController::class, 'syncAll']);
    Route::post('upload-database', [SyncController::class, 'uploadDatabase']);
});
