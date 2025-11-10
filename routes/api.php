<?php

use App\Http\Controllers\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('panel')->group(function () {
    // 🟢 المرحلة 1: مزامنة عامة بدون توثيق (لأول مرة فقط)
    Route::get('bootstrap/users', [SyncController::class, 'publicUsersSync']);

    // 🔒 المرحلة 2: مزامنة مؤمنة
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('sync/{entity}', [SyncController::class, 'sync']);
        Route::post('sync/{entity}/confirm', [SyncController::class, 'confirm']);
        Route::post('sync-all', [SyncController::class, 'syncAll']);
    });

    Route::post('upload-database', [SyncController::class, 'uploadDatabase']);
});
