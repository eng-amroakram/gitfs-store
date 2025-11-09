<?php

use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('panel')->group(function () {
    Route::get('sync/{entity}', [SyncController::class, 'sync']);
    Route::post('sync/{entity}/confirm', [SyncController::class, 'confirm']);
    Route::post('sync-all', [SyncController::class, 'syncAll']);
    Route::post('upload-database', [SyncController::class, 'uploadDatabase']);
});
