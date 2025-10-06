<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default admin user
        $user = User::firstOrCreate([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '0599916672',
        ], [
            'name' => 'Admin User',
            'role' => 'admin',
            'status' => 'active',
            'password' => Hash::make('password'), // password
        ]);

        $cashier = User::firstOrCreate([
            'username' => 'cashier_hassan',
            'email' => 'cashier_hassan@example.com',
            'phone' => '0599916673',
        ], [
            'name' => 'Cashier Hassan',
            'role' => 'cashier',
            'status' => 'active',
            'password' => Hash::make('123456789'), // password
        ]);

        // Assign all permissions directly to the user
        $permissions = Permission::all();
        $user->syncPermissions($permissions);
        $cashier->syncPermissions(['view-dashboard', 'view-cashier']);
    }
}
