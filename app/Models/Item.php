<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
        'purchase_price',
        'sale_price',
        'quantity_total',
        'reserved_quantity',
        'available_quantity',
        'type',
        'low_stock_alert',
        'synced_at',
        'created_by',
        'updated_by',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'code',
            'name',
            'description',
            'purchase_price',
            'sale_price',
            'quantity_total',
            'reserved_quantity',
            'available_quantity',
            'type',
            'low_stock_alert',
            'synced_at',
            'created_at',
            'updated_at',
        ]);
    }

    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $filters = array_merge([
            'search' => null,
            'type' => null,
        ], $filters);

        if ($filters['search']) {
            $builder->where(function ($query) use ($filters) {
                $query->where('code', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['type']) {
            $builder->where('type', $filters['type']);
        }

        return $builder;
    }

    public function scopeStore(Builder $builder, array $data = [])
    {
        $item = $builder->create($data);
        return $item ? true : false;
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], $id)
    {
        $item = $builder->where('id', $id)->first();
        if (!$item) {
            return false;
        }
        return $item->update($data);
    }

    public function scopeGenerateCode($builder)
    {
        $lastItem = $builder
            ->withTrashed()
            ->whereNotNull('code')
            ->orderBy('code', 'desc')
            ->first();

        if (!$lastItem) {
            return 'ITM0001';
        }

        $lastCode = $lastItem->code ?? null;

        if (!$lastCode || !preg_match('/ITM(\d+)/', $lastCode, $matches)) {
            return 'ITM0001';
        }

        $number = (int) $matches[1] + 1;
        return 'ITM' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    // 🔹 العلاقة مع مبيعات الأصناف
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // 🔹 العلاقة مع مشتريات الأصناف
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // 🔹 العلاقة مع حجز الأصناف
    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class);
    }

    // 🔹 العلاقة مع حركات المخزون
    public function movements()
    {
        return $this->hasMany(ItemMovement::class);
    }

    // 🔹 فحص المخزون (هل تحت الحد الأدنى؟)
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_alert;
    }

    // 🔹 حساب الربح المتوقع لكل قطعة
    public function profitPerUnit(): float
    {
        return (float) ($this->sale_price - $this->purchase_price);
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
}
