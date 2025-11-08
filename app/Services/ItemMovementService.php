<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemMovementService
{
    public $model = ItemMovement::class;

    public function __construct()
    {
        $this->model = new ItemMovement();
    }

    public function model($id)
    {
        return ItemMovement::with([
            'item',
            'createdBy',
            'updatedBy'
        ])->find($id);
    }

    public function allUnsynced($columns = ['*'])
    {
        return ItemMovement::with([
            'item',
            'createdBy',
            'updatedBy'
        ])->whereNull('synced_at')->select($columns)->get();
    }

    public function all($filters = [], $columns = ['*'])
    {
        return ItemMovement::with([
            'item',
            'createdBy',
            'updatedBy'
        ])->filters($filters)->select($columns)->get();
    }

    public function data($filters, $sort_field, $sort_direction, $paginate = 10)
    {
        return ItemMovement::data()
            ->filters($filters)
            ->reorder($sort_field, $sort_direction)
            ->paginate($paginate);
    }

    public function changeAccountStatus($id)
    {
        return ItemMovement::changeAccountStatus($id);
    }

    public function delete($id)
    {
        return ItemMovement::deleteModel($id);
    }

    public function store($data)
    {
        return DB::transaction(function () use ($data) {

            $item = Item::findOrFail($data['item_id']);

            // تحقق أساسي من الكمية
            if ($data['quantity'] <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => __('Quantity must be greater than zero.'),
                ]);
            }

            // تحقق إذا كانت الحركة خروج out
            if ($data['movement_type'] === 'out') {
                if ($item->quantity < $data['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => __('Quantity available is not sufficient. Available: :q', ['q' => $item->quantity]),
                    ]);
                }
                $item->quantity -= $data['quantity'];
            }

            // تحقق إذا كانت الحركة دخول in
            if ($data['movement_type'] === 'in') {
                $item->quantity += $data['quantity'];
            }

            $item->save();

            return ItemMovement::create($data);
        });
    }

    public function update($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $movement = ItemMovement::findOrFail($id);

            $item = Item::findOrFail($movement->item_id);

            // أرجع الكمية القديمة قبل تعديل الحركة
            if ($movement->movement_type === 'out') {
                $item->quantity += $movement->quantity;
            } else {
                $item->quantity -= $movement->quantity;
            }

            // تحقق الكمية الجديدة
            if ($data['quantity'] <= 0) {
                throw ValidationException::withMessages([
                    'quantity' => __('Quantity must be greater than zero.'),
                ]);
            }

            if ($data['movement_type'] === 'out') {
                if ($item->quantity < $data['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => __('Quantity available is not sufficient after update. Available: :q', ['q' => $item->quantity]),
                    ]);
                }
                $item->quantity -= $data['quantity'];
            } else {
                $item->quantity += $data['quantity'];
            }

            $item->save();
            $movement->update($data);

            return $movement;
        });
    }

    public function confirmSync($ids)
    {
        return ItemMovement::whereIn('id', $ids)->update(['synced_at' => now()]);
    }
}
