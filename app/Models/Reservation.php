<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'reservation_number',
        'customer_id',
        'user_id',
        'start_date',
        'end_date',
        'discount',
        'deposit',
        'total',
        'remaining',
        'status', // active, completed, cancelled
        'description',
        'notes',
        'synced_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'synced_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    // مدة الحجز بالأيام
    public function duration(): int
    {
        return $this->start_date && $this->end_date
            ? $this->end_date->diffInDays($this->start_date) + 1
            : 0;
    }

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'reservation_number',
            'customer_id',
            'user_id',
            'start_date',
            'end_date',
            'discount',
            'deposit',
            'total',
            'remaining',
            'status',
            'description',
            'notes',
            'synced_at',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by'
        ]);
    }

    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $filters = array_merge([
            'search' => null,
            'customer_id' => null,
            'user_id' => null,
            'status' => [],
            'date_from' => null,
            'date_to' => null,
        ], $filters);

        if ($filters['search']) {
            $builder->where(function ($query) use ($filters) {
                $query->where('deposit', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('status', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['customer_id']) {
            $builder->where('customer_id', $filters['customer_id']);
        }

        if ($filters['user_id']) {
            $builder->where('user_id', $filters['user_id']);
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
        $reservation = $builder->create($data);

        return $reservation;
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], $id)
    {
        $reservation = $builder->where('id', $id)->first();

        if ($reservation) {
            $reservation->update($data);
            return true;
        }

        return false;
    }

    public function scopeDeleteModel(Builder $builder, int $id)
    {
        $reservation = $builder->findOrFail($id);
        return $reservation->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // عناصر الحجز
    public function items()
    {
        return $this->hasMany(ReservationItem::class);
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

    public function scopeGenerateReservationNumber(Builder $builder)
    {
        // نجيب آخر حجز حسب رقم الحجز حتى لو محذوف
        $lastReservation = $builder->withTrashed()
            ->whereNotNull('reservation_number')
            ->orderBy('reservation_number', 'desc')
            ->first();

        if (!$lastReservation) {
            return 'RES0001';
        }

        $lastReservationNumber = $lastReservation->reservation_number;

        // نتأكد أنه يطابق النمط RES0001
        if (!preg_match('/RES(\d+)/', $lastReservationNumber, $matches)) {
            return 'RES0001';
        }

        // نزيد الرقم بواحد ونضيف الصيغة
        $number = (int) $matches[1] + 1;
        return 'RES' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
