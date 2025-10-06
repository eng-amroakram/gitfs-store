<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemMovement extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid',
        'item_id',
        'quantity',
        'movement_type', // in, out, reserved, released
        'reason',
        'synced_at',
        'created_by',
        'updated_by',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'item_id',
            'quantity',
            'movement_type',
            'reason',
            'synced_at',
            'created_at',
            'updated_at',
            'deleted_at',
            'created_by',
            'updated_by',
        ]);
    }

    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $filters = array_merge([
            'search' => null,
            'movement_type' => null,
            'reason' => null,
        ], $filters);

        if ($filters['search']) {
            $builder->where(function ($query) use ($filters) {
                $query->where('reason', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['movement_type']) {
            $builder->where('movement_type', $filters['movement_type']);
        }

        if ($filters['reason']) {
            $builder->where('reason', $filters['reason']);
        }
        return $builder;
    }

    public function scopeStore(Builder $builder, array $data = [])
    {
        $item_movement = $builder->create($data);
        return $item_movement ? true : false;
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], $id)
    {
        $item_movement = $builder->where('id', $id)->first();
        if ($item_movement) {
            $item_movement->update($data);
            return true;
        }
        return false;
    }

    // 🔹 الحركة مرتبطة بصنف
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 🔹 العلاقة مع المستخدمين (المنشئ والمعدل)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // إنشاء حركة خروج عند بيع صنف
    public static function recordSale(Item $item, int $quantity, string $reason = 'sale')
    {
        $movement = self::create([
            'item_id' => $item->id,
            'quantity' => $quantity,
            'movement_type' => 'out',
            'reason' => $reason,
        ]);

        // تحديث كمية الصنف
        $item->decrement('quantity', $quantity);

        return $movement;
    }

    // إنشاء حركة دخول عند شراء صنف أو إرجاع
    public static function recordPurchase(Item $item, int $quantity, string $reason = 'purchase')
    {
        $movement = self::create([
            'item_id' => $item->id,
            'quantity' => $quantity,
            'movement_type' => 'in',
            'reason' => $reason,
        ]);

        // تحديث كمية الصنف
        $item->increment('quantity', $quantity);

        return $movement;
    }

    // حركة خاصة بالحجوزات (خصم/إرجاع مؤقت)
    public static function recordReservation(Item $item, int $quantity, string $status = 'out')
    {
        $movement_type = $status === 'out' ? 'out' : 'in';

        $movement = self::create([
            'item_id' => $item->id,
            'quantity' => $quantity,
            'movement_type' => $movement_type,
            'reason' => 'reservation',
        ]);

        // تعديل كمية المخزون حسب نوع الحركة
        if ($movement_type === 'out') {
            $item->decrement('quantity', $quantity);
        } else {
            $item->increment('quantity', $quantity);
        }

        return $movement;
    }
}
