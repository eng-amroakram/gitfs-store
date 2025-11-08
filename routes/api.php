<?php

use App\Http\Controllers\ItemMovementsSyncController;
use App\Http\Controllers\Panel\CustomersSyncController;
use App\Http\Controllers\Panel\ItemsSyncController;
use App\Http\Controllers\Panel\PaymentsSyncController;
use App\Http\Controllers\Panel\ReservationsSyncController;
use App\Http\Controllers\Panel\SalesSyncController;
use App\Http\Controllers\Panel\UsersSyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('panel')->group(function () {

    Route::get('sync-users', [UsersSyncController::class, 'syncUsers']);
    Route::post('users-sync-confirm', [UsersSyncController::class, 'confirmSync']);

    Route::get('sync-customers', [CustomersSyncController::class, 'syncCustomers']);
    Route::post('customers-sync-confirm', [CustomersSyncController::class, 'confirmSync']);

    Route::get('sync-items', [ItemsSyncController::class, 'syncItems']);
    Route::post('items-sync-confirm', [ItemsSyncController::class, 'confirmSync']);

    Route::get('sync-item-movements', [ItemMovementsSyncController::class, 'syncItemMovements']);
    Route::post('item-movements-sync-confirm', [ItemMovementsSyncController::class, 'confirmSync']);

    // Server sync endpoints
    Route::post('sync-items-to-server', [ItemsSyncController::class, 'syncItemsToServer']);
    Route::post('sync-item-movements-to-server', [ItemMovementsSyncController::class, 'syncItemMovementsToServer']);
    Route::post('sync-sales-to-server', [SalesSyncController::class, 'syncSalesToServer']);
    Route::post('sync-reservations-to-server', [ReservationsSyncController::class, 'syncReservationsToServer']);
    Route::post('sync-payments-to-server', [PaymentsSyncController::class, 'syncPaymentsToServer']);

    // route for uploading app.db from mobile app
    Route::post('upload-database', function (Request $request) {

        if ($request->hasFile('database')) {
            $file = $request->file('database');

            if (! $file->isValid()) {
                return response()->json(['message' => 'Uploaded file is not valid.'], 400);
            }

            $destination = storage_path('app');
            $targetPath = $destination . DIRECTORY_SEPARATOR . 'app.db';

            try {
                if (file_exists($targetPath)) {
                    // remove existing file to ensure replacement
                    @unlink($targetPath);
                }

                $file->move($destination, 'app.db');

                return response()->json(['message' => 'Database uploaded and replaced successfully.']);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to upload database.', 'error' => $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'No database file found.'], 400);
    });
});
