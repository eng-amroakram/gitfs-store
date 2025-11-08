<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $saleItems = [
            [
                'code' => 'ITM0001',
                'name' => 'باقة ورد أحمر',
                'description' => 'باقة جميلة من الورود الحمراء مناسبة لجميع المناسبات.',
                'purchase_price' => 15.00,
                'sale_price' => 30.00,
                'quantity_total' => 100,
                'reserved_quantity' => 0,
                'available_quantity' => 100,
                'type' => 'sale',
                'low_stock_alert' => 20,
                'synced_at' => null,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // [
            //     'code' => 'ITM0002',
            //     'name' => 'علبة شوكولاتة فاخرة',
            //     'description' => 'علبة مختارة من الشوكولاتة الفاخرة.',
            //     'purchase_price' => 10.00,
            //     'sale_price' => 25.00,
            //     'quantity_total' => 50,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 50,
            //     'type' => 'sale',
            //     'low_stock_alert' => 10,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
            // [
            //     'code' => 'ITM0003',
            //     'name' => 'دب محشو',
            //     'description' => 'دب محشو ناعم مناسب للأطفال والكبار.',
            //     'purchase_price' => 8.00,
            //     'sale_price' => 20.00,
            //     'quantity_total' => 75,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 75,
            //     'type' => 'sale',
            //     'low_stock_alert' => 15,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
            // [
            //     'code' => 'ITM0004',
            //     'name' => 'بطاقة تهنئة',
            //     'description' => 'بطاقة تهنئة قابلة للتخصيص لجميع المناسبات.',
            //     'purchase_price' => 2.00,
            //     'sale_price' => 5.00,
            //     'quantity_total' => 200,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 200,
            //     'type' => 'sale',
            //     'low_stock_alert' => 30,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
            // [
            //     'code' => 'ITM0005',
            //     'name' => 'مزهرية زهور',
            //     'description' => 'مزهرية أنيقة لوضع الزهور المفضلة لديك.',
            //     'purchase_price' => 12.00,
            //     'sale_price' => 28.00,
            //     'quantity_total' => 40,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 40,
            //     'type' => 'sale',
            //     'low_stock_alert' => 5,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
        ];

        $rentalItems = [
            [
                'code' => 'ITM0006',
                'name' => 'فستان سهرة',
                'description' => 'فستان سهرة أنيق للإيجار لمناسباتك الخاصة.',
                'purchase_price' => 100.00,
                'sale_price' => 40.00,
                'quantity_total' => 10,
                'reserved_quantity' => 0,
                'available_quantity' => 10,
                'type' => 'rental',
                'low_stock_alert' => 2,
                'synced_at' => null,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // [
            //     'code' => 'ITM0007',
            //     'name' => 'بدلة رسمية',
            //     'description' => 'بدلة رسمية للإيجار تناسب جميع المناسبات الرسمية.',
            //     'purchase_price' => 150.00,
            //     'sale_price' => 60.00,
            //     'quantity_total' => 15,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 15,
            //     'type' => 'rental',
            //     'low_stock_alert' => 3,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
            // [
            //     'code' => 'ITM0008',
            //     'name' => 'طقم حفلات للأطفال',
            //     'description' => 'طقم حفلات ملون وممتع للأطفال للإيجار.',
            //     'purchase_price' => 50.00,
            //     'sale_price' => 20.00,
            //     'quantity_total' => 20,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 20,
            //     'type' => 'rental',
            //     'low_stock_alert' => 5,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
            // [
            //     'code' => 'ITM0009',
            //     'name' => 'أدوات تزيين الحفلات',
            //     'description' => 'مجموعة أدوات تزيين الحفلات للإيجار لجعل مناسبتك مميزة.',
            //     'purchase_price' => 80.00,
            //     'sale_price' => 30.00,
            //     'quantity_total' => 25,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 25,
            //     'type' => 'rental',
            //     'low_stock_alert' => 4,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
            // [
            //     'code' => 'ITM0010',
            //     'name' => 'كاميرا فورية',
            //     'description' => 'كاميرا فورية للإيجار لالتقاط لحظاتك الخاصة.',
            //     'purchase_price' => 120.00,
            //     'sale_price' => 50.00,
            //     'quantity_total' => 8,
            //     'reserved_quantity' => 0,
            //     'available_quantity' => 8,
            //     'type' => 'rental',
            //     'low_stock_alert' => 1,
            //     'synced_at' => now(),
            //     'created_by' => 1,
            //     'updated_by' => 1,
            // ],
        ];

        foreach ($saleItems as $item) {
            Item::firstOrCreate($item);
        }

        foreach ($rentalItems as $item) {
            Item::firstOrCreate($item);
        }
    }
}
