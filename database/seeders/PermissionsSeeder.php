<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'view-financials', 'group' => 'financials', 'label' => 'View Financials'],
            ['name' => 'view-reports', 'group' => 'reports', 'label' => 'View Reports'],
            ['name' => 'view-dashboard', 'group' => 'dashboard', 'label' => 'View Dashboard'],
            ['name' => 'view-cashier', 'group' => 'cashier', 'label' => 'View Cashier'],
            ['name' => 'view-users', 'group' => 'users', 'label' => 'View Users'],
            ['name' => 'create-user', 'group' => 'users', 'label' => 'Create User'],
            ['name' => 'edit-user', 'group' => 'users', 'label' => 'Edit User'],
            ['name' => 'manage-user-permissions', 'group' => 'users', 'label' => 'Manage User Permissions'],

            // ['name' => 'view-roles', 'group' => 'roles', 'label' => 'View Roles'],
            // ['name' => 'create-role', 'group' => 'roles', 'label' => 'Create Role'],
            // ['name' => 'edit-role', 'group' => 'roles', 'label' => 'Edit Role'],

            ['name' => 'view-customers', 'group' => 'customers', 'label' => 'View Customers'],
            ['name' => 'create-customer', 'group' => 'customers', 'label' => 'Create Customer'],
            ['name' => 'edit-customer', 'group' => 'customers', 'label' => 'Edit Customer'],
            ['name' => 'show-customer', 'group' => 'customers', 'label' => 'Show Customer'],

            ['name' => 'view-suppliers', 'group' => 'suppliers', 'label' => 'View Suppliers'],
            ['name' => 'create-supplier', 'group' => 'suppliers', 'label' => 'Create Supplier'],
            ['name' => 'edit-supplier', 'group' => 'suppliers', 'label' => 'Edit Supplier'],
            ['name' => 'show-supplier', 'group' => 'suppliers', 'label' => 'Show Supplier'],

            ['name' => 'view-items', 'group' => 'items', 'label' => 'View Items'],
            ['name' => 'create-item', 'group' => 'items', 'label' => 'Create Item'],
            ['name' => 'edit-item', 'group' => 'items', 'label' => 'Edit Item'],
            ['name' => 'view-item', 'group' => 'items', 'label' => 'Show Item'],

            ['name' => 'view-item-movements', 'group' => 'item-movements', 'label' => 'View Item Movements'],
            ['name' => 'create-item-movement', 'group' => 'item-movements', 'label' => 'Create Item Movement'],
            ['name' => 'edit-item-movement', 'group' => 'item-movements', 'label' => 'Edit Item Movement'],
            ['name' => 'view-item-movement', 'group' => 'item-movements', 'label' => 'Show Item Movement'],

            ['name' => 'view-sales', 'group' => 'sales', 'label' => 'View Sales'],
            ['name' => 'create-sale', 'group' => 'sales', 'label' => 'Create Sale'],
            ['name' => 'edit-sale', 'group' => 'sales', 'label' => 'Edit Sale'],
            ['name' => 'view-sale', 'group' => 'sales', 'label' => 'Show Sale'],

            ['name' => 'view-purchases', 'group' => 'purchases', 'label' => 'View Purchases'],
            ['name' => 'create-purchase', 'group' => 'purchases', 'label' => 'Create Purchase'],
            ['name' => 'edit-purchase', 'group' => 'purchases', 'label' => 'Edit Purchase'],
            ['name' => 'view-purchase', 'group' => 'purchases', 'label' => 'Show Purchase'],

            ['name' => 'view-reservations', 'group' => 'reservations', 'label' => 'View Reservations'],
            ['name' => 'create-reservation', 'group' => 'reservations', 'label' => 'Create Reservation'],
            ['name' => 'edit-reservation', 'group' => 'reservations', 'label' => 'Edit Reservation'],
            ['name' => 'view-reservation', 'group' => 'reservations', 'label' => 'Show Reservation'],

            ['name' => 'view-payments', 'group' => 'payments', 'label' => 'View Payments'],
            ['name' => 'create-payment', 'group' => 'payments', 'label' => 'Create Payment'],
            ['name' => 'edit-payment', 'group' => 'payments', 'label' => 'Edit Payment'],
            ['name' => 'view-payment', 'group' => 'payments', 'label' => 'Show Payment'],

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}
