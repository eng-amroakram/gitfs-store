<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'أحمد محمد',
                'phone' => '0501234567',
                'email' => 'ahmed@example.com',
            ],
            [
                'name' => 'سارة علي',
                'phone' => '0502345678',
                'email' => 'sara@example.com',
            ],
            [
                'name' => 'خالد يوسف',
                'phone' => '0503456789',
                'email' => 'khaled@example.com',
            ],
            [
                'name' => 'ليلى حسن',
                'phone' => '0504567890',
                'email' => 'layla@example.com',
            ],
            [
                'name' => 'مريم عبد الله',
                'phone' => '0505678901',
                'email' => 'maryam@example.com',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate($customer);
        }
    }
}
