<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'email',
        'sale_balance',
        'reservation_balance',
        'total_balance',
        'synced_at',
        'created_by',
        'updated_by'
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'name',
            'phone',
            'email',
            'sale_balance',
            'reservation_balance',
            'total_balance',
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
        ], $filters);

        if ($filters['search']) {
            $builder->where(function ($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('phone', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $builder;
    }

    public function scopeStore(Builder $builder, array $data = [])
    {
        $user = $builder->create($data);
        return $user ? true : false;
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], $id)
    {
        $customer = $builder->where('id', $id)->first();
        if ($customer) {
            return $customer->update($data) ? true : false;
        }
        return false;
    }

    public function scopeDeleteModel(Builder $builder, $id)
    {
        $customer = $builder->where('id', $id)->first();
        if ($customer) {
            return $customer->delete() ? true : false;
        }
        return false;
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // 🔹 العلاقات
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
