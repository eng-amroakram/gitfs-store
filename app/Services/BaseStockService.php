<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemMovement;

class BaseStockService
{
    /**
     * زيادة المخزون (مثلاً: شراء جديد أو تعديل شراء)
     */
    protected function increaseStock($itemId, $quantity, $reason = 'adjustment')
    {
        $item = Item::findOrFail($itemId);

        // تحديث الكميات
        if (in_array($reason, ['purchase', 'purchase_edit', 'sale_delete', 'sale_edit_cancel'])) {
            $item->quantity_total += $quantity;
        }

        $item->available_quantity = $item->quantity_total - $item->reserved_quantity;
        $item->synced_at = null; // تعيين synced_at إلى null عند زيادة المخزون
        $item->save();

        // سجل الحركة
        ItemMovement::create([
            'item_id'       => $item->id,
            'quantity'      => $quantity,
            'movement_type' => 'in',
            'reason'        => $reason,
        ]);

        return $item;
    }

    /**
     * خصم المخزون (مثلاً: بيع أو حذف شراء)
     */
    protected function decreaseStock($itemId, $quantity, $reason = 'adjustment')
    {
        $item = Item::findOrFail($itemId);

        if ($item->available_quantity < $quantity) {
            throw new \Exception("Not enough available stock for item {$item->name}");
        }

        // خصم الكمية الكلية فقط في الحالات المناسبة
        if (in_array($reason, ['sale', 'sale_edit', 'purchase_delete', 'purchase_edit_cancel'])) {
            $item->quantity_total -= $quantity;
        }

        $item->available_quantity = $item->quantity_total - $item->reserved_quantity;
        $item->synced_at = null; // تعيين synced_at إلى null عند خصم المخزون
        $item->save();

        // سجل الحركة
        ItemMovement::create([
            'item_id'       => $item->id,
            'quantity'      => $quantity,
            'movement_type' => 'out',
            'reason'        => $reason,
        ]);

        return $item;
    }

    /**
     * حجز كمية من المخزون (مثلاً: عند عمل حجز لطلب)
     */
    protected function reserveStock($itemId, $quantity, $reason = 'reservation')
    {
        $item = Item::findOrFail($itemId);

        if ($item->available_quantity < $quantity) {
            throw new \Exception("Not enough stock to reserve for item {$item->name}");
        }

        $item->reserved_quantity += $quantity;
        $item->available_quantity = $item->quantity_total - $item->reserved_quantity;
        $item->synced_at = null; // تعيين synced_at إلى null عند حجز المخزون
        $item->save();

        ItemMovement::create([
            'item_id'       => $item->id,
            'quantity'      => $quantity,
            'movement_type' => 'reserved',
            'reason'        => $reason,
        ]);

        return $item;
    }

    /**
     * إلغاء حجز المخزون (مثلاً: عند حذف أو تعديل الحجز)
     */
    protected function releaseStock($itemId, $quantity, $reason = 'reservation_cancel')
    {
        $item = Item::findOrFail($itemId);

        $item->reserved_quantity -= $quantity;
        if ($item->reserved_quantity < 0) {
            $item->reserved_quantity = 0;
        }

        $item->available_quantity = $item->quantity_total - $item->reserved_quantity;
        $item->synced_at = null; // تعيين synced_at إلى null عند إلغاء حجز المخزون
        $item->save();

        ItemMovement::create([
            'item_id'       => $item->id,
            'quantity'      => $quantity,
            'movement_type' => 'released',
            'reason'        => $reason,
        ]);

        return $item;
    }
}
