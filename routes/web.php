<?php

use App\Livewire\Employees\Cashier;
use App\Livewire\Panel\Auth\Login;
use App\Livewire\Panel\Customers\CreateCustomer;
use App\Livewire\Panel\Customers\CustomersList;
use App\Livewire\Panel\Customers\EditCustomer;
use App\Livewire\Panel\Customers\ShowCustomer;
use App\Livewire\Panel\Dashboard;
use App\Livewire\Panel\ItemMovements\CreateItemMovement;
use App\Livewire\Panel\ItemMovements\EditItemMovement;
use App\Livewire\Panel\ItemMovements\ItemMovementsList;
use App\Livewire\Panel\ItemMovements\ShowItemMovement;
use App\Livewire\Panel\Items\CreateItem;
use App\Livewire\Panel\Items\EditItem;
use App\Livewire\Panel\Items\ItemsList;
use App\Livewire\Panel\Items\ShowItem;
use App\Livewire\Panel\Payments\CreatePayment;
use App\Livewire\Panel\Payments\EditPayment;
use App\Livewire\Panel\Payments\PaymentsList;
use App\Livewire\Panel\Payments\ShowPayment;
use App\Livewire\Panel\Purchases\CreatePurchase;
use App\Livewire\Panel\Purchases\EditPurchase;
use App\Livewire\Panel\Purchases\PurchasesList;
use App\Livewire\Panel\Purchases\ShowPurchase;
use App\Livewire\Panel\Reservations\CreateReservation;
use App\Livewire\Panel\Reservations\EditReservation;
use App\Livewire\Panel\Reservations\ReservationsList;
use App\Livewire\Panel\Reservations\ShowReservation;
use App\Livewire\Panel\Roles\CreateRole;
use App\Livewire\Panel\Roles\EditRole;
use App\Livewire\Panel\Roles\RolesList;
use App\Livewire\Panel\Sales\CreateSale;
use App\Livewire\Panel\Sales\EditSale;
use App\Livewire\Panel\Sales\SalesList;
use App\Livewire\Panel\Sales\ShowSale;
use App\Livewire\Panel\Suppliers\SuppliersList;
use App\Livewire\Panel\Suppliers\CreateSupplier;
use App\Livewire\Panel\Suppliers\EditSupplier;
use App\Livewire\Panel\Suppliers\ShowSupplier;
use App\Livewire\Panel\Users\CreateUser;
use App\Livewire\Panel\Users\EditUser;
use App\Livewire\Panel\Users\ManageUserPermissions;
use App\Livewire\Panel\Users\UsersList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('admin.panel.dashboard');
});

Route::prefix('auth/')->middleware(['guest', 'web'])->group(function () {
    Route::get('login', Login::class)->name('login');
});


Route::prefix('admin/')->as('admin.')->middleware(['auth', 'web'])->group(function () {
    Route::prefix('panel/')->as('panel.')->group(function () {

        Route::get('dashboard', Dashboard::class)->name('dashboard')->can('view-dashboard');
        Route::get('cashier', Cashier::class)->name('cashier')->can('view-cashier');

        Route::prefix('users/')->as('users.')->group(function () {
            Route::get('/', UsersList::class)->name('list')->can('view-users');
            Route::get('create', CreateUser::class)->name('create')->can('create-user');
            Route::get('edit', EditUser::class)->name('edit')->can('edit-user');
            Route::get('manage-user-permissions', ManageUserPermissions::class)->name('manage-permissions')->can('manage-user-permissions');
        });

        // Route::prefix('roles/')->as('roles.')->group(function () {
        //     Route::get('/', RolesList::class)->name('list');
        //     Route::get('create', CreateRole::class)->name('create');
        //     Route::get('edit', EditRole::class)->name('edit');
        // });

        Route::prefix('customers/')->as('customers.')->group(function () {
            Route::get('/', CustomersList::class)->name('list')->can('view-customers');
            Route::get('create', CreateCustomer::class)->name('create')->can('create-customer');
            Route::get('edit', EditCustomer::class)->name('edit')->can('edit-customer');
            Route::get('show', ShowCustomer::class)->name('show')->can('show-customer');
        });

        Route::prefix('suppliers/')->as('suppliers.')->group(function () {
            Route::get('/', SuppliersList::class)->name('list')->can('view-suppliers');
            Route::get('create', CreateSupplier::class)->name('create')->can('create-supplier');
            Route::get('edit', EditSupplier::class)->name('edit')->can('edit-supplier');
            Route::get('show', ShowSupplier::class)->name('show')->can('show-supplier');
        });

        Route::prefix('items/')->as('items.')->group(function () {
            Route::get('/', ItemsList::class)->name('list')->can('view-items');
            Route::get('create', CreateItem::class)->name('create')->can('create-item');
            Route::get('edit', EditItem::class)->name('edit')->can('edit-item');
            Route::get('show', ShowItem::class)->name('show')->can('view-item');
        });

        Route::prefix('item-movements/')->as('item-movements.')->group(function () {
            Route::get('/', ItemMovementsList::class)->name('list')->can('view-item-movements');
            Route::get('create', CreateItemMovement::class)->name('create')->can('create-item-movement');
            Route::get('edit', EditItemMovement::class)->name('edit')->can('edit-item-movement');
            Route::get('show', ShowItemMovement::class)->name('show')->can('view-item-movement');
        });

        Route::prefix('sales/')->as('sales.')->group(function () {
            Route::get('/', SalesList::class)->name('list')->can('view-sales');
            Route::get('create', CreateSale::class)->name('create')->can('create-sale');
            Route::get('edit', EditSale::class)->name('edit')->can('edit-sale');
            Route::get('show', ShowSale::class)->name('show')->can('view-sale');
        });

        Route::prefix('purchases/')->as('purchases.')->group(function () {
            Route::get('/', PurchasesList::class)->name('list')->can('view-purchases');
            Route::get('create', CreatePurchase::class)->name('create')->can('create-purchase');
            Route::get('edit', EditPurchase::class)->name('edit')->can('edit-purchase');
            Route::get('show', ShowPurchase::class)->name('show')->can('view-purchase');
        });

        Route::prefix('reservations/')->as('reservations.')->group(function () {
            Route::get('/', ReservationsList::class)->name('list')->can('view-reservations');
            Route::get('create', CreateReservation::class)->name('create')->can('create-reservation');
            Route::get('edit', EditReservation::class)->name('edit')->can('edit-reservation');
            Route::get('show', ShowReservation::class)->name('show')->can('view-reservation');
        });

        Route::prefix('payments/')->as('payments.')->group(function () {
            Route::get('/', PaymentsList::class)->name('list')->can('view-payments');
            Route::get('create', CreatePayment::class)->name('create')->can('create-payment');
            Route::get('edit', EditPayment::class)->name('edit')->can('edit-payment');
            Route::get('show', ShowPayment::class)->name('show')->can('view-payment');
        });

        // Logout Route
        Route::get('logout', function () {
            Auth::logout();
            return redirect()->route('login');
        })->name('logout');
    });
});
