<?php

namespace App\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleService extends BaseStockService
{
    public $model = Sale::class;

    public function __construct()
    {
        $this->model = new Sale();
    }

    public function model($id)
    {
        return Sale::find($id);
    }

    public function all($filters = [], $columns = ['*'])
    {
        return Sale::query()->filters($filters)->select($columns)->get();
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return Sale::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function delete($id)
    {
        $sale = $this->model($id);
        if (!$sale) return false;

        return DB::transaction(function () use ($sale) {
            // إعادة المخزون
            foreach ($sale->items as $item) {
                $this->increaseStock($item->item_id, $item->quantity, 'sale_delete');
            }
            $sale->delete();
            return true;
        });
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {
            $data['synced_at'] = now();
            $sale = Sale::store($data);

            // إدخال البنود
            foreach ($data['items'] as $key => $item) {
                $data['items'][$key]['synced_at'] = now();
            }

            $sale->items()->createMany($data['items']);

            // خصم المخزون
            foreach ($data['items'] as $item) {
                $this->decreaseStock($item['item_id'], $item['quantity'], 'sale');
            }

            return $sale;
        });
    }

    public function update($data, $id)
    {
        $sale = $this->model($id);
        if (!$sale) return false;

        return DB::transaction(function () use ($sale, $data) {

            // إعادة المخزون للبنود القديمة
            foreach ($sale->items as $oldItem) {
                $this->increaseStock($oldItem->item_id, $oldItem->quantity, 'sale_edit_cancel');
            }

            // تحديث الفاتورة
            Sale::updateModel($data, $sale->id);

            // إعادة إدخال البنود
            $sale->items()->delete();
            $sale->items()->createMany($data['items']);

            // خصم المخزون للبنود الجديدة
            foreach ($data['items'] as $item) {
                $this->decreaseStock($item['item_id'], $item['quantity'], 'sale_edit');
            }

            return $sale;
        });
    }

    public function generateInvoiceNumber()
    {
        return Sale::generateInvoiceNumber();
    }
}
