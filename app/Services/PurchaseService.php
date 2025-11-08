<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\Item;
use App\Models\ItemMovement;
use Illuminate\Support\Facades\DB;

class PurchaseService extends BaseStockService
{
    public $model = Purchase::class;

    public function __construct()
    {
        $this->model = new Purchase();
    }

    public function model($id)
    {
        return Purchase::find($id);
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return Purchase::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function delete($id)
    {
        $purchase = $this->model($id);
        if (!$purchase) return false;

        return DB::transaction(function () use ($purchase) {
            // خصم المخزون للبنود القديمة
            foreach ($purchase->items as $item) {
                $this->decreaseStock($item->item_id, $item->quantity, 'purchase_delete');
            }
            $purchase->delete();
            return true;
        });
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {

            // إنشاء فاتورة الشراء
            $data['synced_at'] = now();
            $purchase = Purchase::store($data);

            // إضافة البنود
            $purchase->items()->createMany($data['items']);

            // تحديث المخزون وزيادة الكمية لكل بند
            foreach ($data['items'] as $item) {
                $this->increaseStock($item['item_id'], $item['quantity'], 'purchase');
            }

            return $purchase;
        });
    }

    public function update($data, $id)
    {
        $purchase = $this->model($id);
        if (!$purchase) return false;

        return DB::transaction(function () use ($purchase, $data) {

            // خصم المخزون للبنود القديمة
            foreach ($purchase->items as $oldItem) {
                $this->decreaseStock($oldItem->item_id, $oldItem->quantity, 'purchase_edit_cancel');
            }

            // تحديث الفاتورة
            Purchase::updateModel($data, $purchase->id);

            // حذف البنود القديمة وإضافة البنود الجديدة
            $purchase->items()->delete();
            $purchase->items()->createMany($data['items']);

            // زيادة المخزون للبنود الجديدة
            foreach ($data['items'] as $item) {
                $this->increaseStock($item['item_id'], $item['quantity'], 'purchase_edit');
            }

            return $purchase;
        });
    }

    public function generateInvoiceNumber()
    {
        return Purchase::generateInvoiceNumber();
    }
}
