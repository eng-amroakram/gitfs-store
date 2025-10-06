<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'invoice_number',
        'supplier_id',
        'user_id',
        'total',
        'synced_at',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'id',
        'supplier_id',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'invoice_number',
            'supplier_id',
            'user_id',
            'total',
            'synced_at',
            'created_at',
            'updated_at',
        ]);
    }

    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $filters = array_merge([
            'search' => null,
            'supplier_id' => null,
            'user_id' => null,
            'date_from' => null,
            'date_to' => null,
        ], $filters);

        if ($filters['search']) {
            $builder->where(function ($query) use ($filters) {
                $query->where('invoice_number', 'like', '%' . $filters['search'] . '%')
                    ->orWhereHas('supplier', function ($q) use ($filters) {
                        $q->where('name', 'like', '%' . $filters['search'] . '%');
                    });
            });
        }

        if ($filters['supplier_id']) {
            $builder->where('supplier_id', $filters['supplier_id']);
        }

        if ($filters['user_id']) {
            $builder->where('user_id', $filters['user_id']);
        }

        if ($filters['date_from']) {
            $builder->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $builder->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $builder;
    }

    public function scopeStore(Builder $builder, array $data = [])
    {
        return $builder->create($data);
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], int $id)
    {
        $purchase = $builder->findOrFail($id);
        $purchase->update($data);

        return $purchase;
    }

    public function scopeDeleteModel(Builder $builder, int $id)
    {
        $purchase = $builder->findOrFail($id);
        return $purchase->delete();
    }

    public function scopeGenerateInvoiceNumber(Builder $builder)
    {
        $lastPurchase = $builder->withTrashed()
            ->whereNotNull('invoice_number')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if (!$lastPurchase) {
            return 'INV0001';
        }

        $lastInvoiceNumber = $lastPurchase->invoice_number ?? null;

        if (!$lastInvoiceNumber || !preg_match('/INV(\d+)/', $lastInvoiceNumber, $matches)) {
            return 'INV0001';
        }

        $number = (int) $matches[1] + 1;
        return 'INV' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // عناصر الفاتورة
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
