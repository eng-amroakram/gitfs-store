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

    public function allUnsynced($columns = ['*'])
    {
        return Item::whereNull('synced_at')->select($columns)->get();
    }

    public function all(array $filters = [], $columns = ['*'])
    {
        return Item::filters($filters)->select($columns)->get();
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
            return Item::create($data);
        });
    }

    public function update($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $item = Item::findOrFail($id);
            $item->update($data);
            return $item;
        });
    }

    public function generateItemCode()
    {
        return Item::generateCode();
    }

    public function confirmSync($ids)
    {
        return Item::whereIn('uuid', $ids)->update(['synced_at' => now()]);
    }
}
