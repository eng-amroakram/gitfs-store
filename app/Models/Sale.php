<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        "uuid",
        "invoice_number",
        "customer_id",
        "user_id",
        "total",
        "discount",
        "grand_total",
        "status",
        "description",
        "paid",
        "remaining",
        "notes",
        "synced_at",
        "created_by",
        "updated_by",
    ];

    protected $hidden = [
        'id',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'invoice_number',
            'customer_id',
            'user_id',
            'total',
            'discount',
            'grand_total',
            'status',
            'description',
            'paid',
            'remaining',
            'notes',
            'synced_at',
            'created_at',
            'updated_at',
        ]);
    }

    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $filters = array_merge([
            'search' => null,
            'status' => [],
            'date_from' => null,
            'date_to' => null,
        ], $filters);

        if ($filters['search']) {
            $builder->where(function ($query) use ($filters) {
                $query->where('invoice_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['status']) {
            $builder->whereIn('status', $filters['status']);
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
        $sale = $builder->create($data);
        return $sale ? $sale : false;
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], $id)
    {
        $sale = $builder->where('id', $id)->first();
        if ($sale) {
            return $sale->update($data) ? true : false;
        }
        return false;
    }

    public function scopeGenerateInvoiceNumber(Builder $builder)
    {
        // نجيب آخر فاتورة حسب رقم الفاتورة حتى لو محذوفة
        $lastSale = $builder->withTrashed()
            ->whereNotNull('invoice_number')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if (!$lastSale) {
            return 'INV0001';
        }

        $lastInvoiceNumber = $lastSale->invoice_number;

        // نتأكد أنه يطابق النمط INV0001
        if (!preg_match('/INV(\d+)/', $lastInvoiceNumber, $matches)) {
            return 'INV0001';
        }

        // نزيد الرقم بواحد ونضيف الصيغة
        $number = (int) $matches[1] + 1;
        return 'INV' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    // عناصر الفاتورة
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    // حساب المبلغ المدفوع حتى الآن
    public function paidAmount(): float
    {
        return $this->payments()->sum('amount');
    }

    // حالة الفاتورة
    public function isPaid(): bool
    {
        return $this->paidAmount() >= $this->grand_total;
    }
}
