<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        // 'sale_id',
        'uuid',
        'paymentable_id',
        'paymentable_type',
        'customer_id',
        'payment_reference',
        'amount',
        'method', // cash, card, bank_transfer, palpay, jawwalPay, other
        'notes',
        'synced_at',
        'created_by',
        'updated_by',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            // 'sale_id',
            'paymentable_id',
            'paymentable_type',
            'customer_id',
            'payment_reference',
            'amount',
            'method',
            'notes',
            'created_by',
            'updated_by',
            'synced_at',
            'created_at',
            'updated_at',
        ]);
    }

    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $filters = array_merge([
            'search' => null,
            'method' => null,
            'date_from' => null,
            'date_to' => null,
        ], $filters);

        if ($filters['search']) {
            $builder->where(function ($query) use ($filters) {
                $query->where('amount', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('method', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['method']) {
            $builder->where('method', $filters['method']);
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
        $payment = $builder->create($data);

        return $payment;
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], $id)
    {
        $payment = $builder->where('id', $id)->first();

        if ($payment) {
            $payment->update($data);
            return true;
        }

        return false;
    }

    // public function sale()
    // {
    //     return $this->belongsTo(Sale::class);
    // }

    public function scopeGeneratePaymentReference(Builder $builder)
    {
        // نجيب آخر دفعة حسب رقم الدفعة حتى لو محذوفة
        $lastPayment = $builder->withTrashed()
            ->whereNotNull('payment_reference')
            ->orderBy('payment_reference', 'desc')
            ->first();

        if (!$lastPayment) {
            return 'PAY0001';
        }

        // نفصل بين الحروف والأرقام
        preg_match('/([a-zA-Z]+)([0-9]+)/', $lastPayment->payment_reference, $matches);
        $prefix = $matches[1]; // الحروف
        $number = (int)$matches[2]; // الأرقام

        // نزود الرقم بواحد
        $newNumber = $number + 1;

        // نرجع الرقم الجديد مع الحفاظ على نفس عدد الأرقام (مثلاً PAY0001 -> PAY0002)
        return $prefix . str_pad($newNumber, strlen($matches[2]), '0', STR_PAD_LEFT);
    }

    public function paymentable()
    {
        return $this->morphTo();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
