<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemService
{
    public $model = Item::class;

    public function __construct()
    {
        $this->model = new Item();
    }

    public function model($id)
    {
        return Item::with([
            'movements',
            'saleItems.sale.customer',
            'purchaseItems.purchase.supplier'
        ])->find($id);
    }

    public function all(array $filters = [], $columns = ['*'])
    {
        return Item::query()->select($columns)->filters($filters)->get();
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return Item::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function changeAccountStatus($id)
    {
        return Item::changeAccountStatus($id);
    }

    public function delete($id)
    {
        return Item::deleteModel($id);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {

            // ✅ التحقق من الأسعار
            if ($data['purchase_price'] < 0) {
                throw ValidationException::withMessages([
                    'purchase_price' => __('Quantity must be greater than zero.'),
                ]);
            }

            if ($data['sale_price'] < 0) {
                throw ValidationException::withMessages([
                    'sale_price' => __('Sale price must be greater than zero.'),
                ]);
            }

            // إذا سعر البيع < سعر الشراء
            if (($data['sale_price'] < $data['purchase_price']) && $data['type'] === 'sale') {
                throw ValidationException::withMessages([
                    'sale_price' => __('Sale price cannot be less than purchase price.'),
                ]);
            }

            // ✅ التحقق من الكمية
            if ($data['quantity'] < 0) {
                throw ValidationException::withMessages([
                    'quantity' => __('Quantity must be greater than zero.'),
                ]);
            }

            // ✅ تحقق من الكود
            if (Item::where('code', $data['code'])->exists()) {
                throw ValidationException::withMessages([
                    'code' => __('Item code is already in use, please enter a different code.'),
                ]);
            }

            // Set initial quantity to quantity_total
            $data['quantity_total'] = $data['quantity'];
            $data['reserved_quantity'] = 0;
            $data['available_quantity'] = $data['quantity'];

            return Item::create($data);
        });
    }

    public function update($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $item = Item::findOrFail($id);

            // ✅ التحقق من الأسعار
            if ($data['purchase_price'] < 0) {
                throw ValidationException::withMessages([
                    'purchase_price' => __('Purchase price must be greater than zero.'),
                ]);
            }

            if ($data['sale_price'] < 0) {
                throw ValidationException::withMessages([
                    'sale_price' => __('Sale price must be greater than zero.'),
                ]);
            }

            // إذا سعر البيع < سعر الشراء
            if (($data['sale_price'] < $data['purchase_price']) && $data['type'] === 'sale') {
                throw ValidationException::withMessages([
                    'sale_price' => __('Sale price cannot be less than purchase price.'),
                ]);
            }

            // ✅ التحقق من الكمية
            if ($data['quantity'] < 0) {
                throw ValidationException::withMessages([
                    'quantity' => __('Quantity must be greater than zero.'),
                ]);
            }

            // ✅ تحقق من الكود (مع استثناء نفس الصنف)
            if (Item::where('code', $data['code'])
                ->where('id', '!=', $id)
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'code' => __('Item code is already in use, please enter a different code.'),
                ]);
            }

            // Set initial quantity to quantity_total
            $data['quantity_total'] = $data['quantity'];
            $data['reserved_quantity'] = 0;
            $data['available_quantity'] = $data['quantity'];

            $item->update($data);
            return $item;
        });
    }

    public function generateItemCode()
    {
        return Item::generateCode();
    }
}
