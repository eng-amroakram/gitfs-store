<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'email',
        'address',
        'synced_at',
        'created_by',
        'updated_by',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'name',
            'phone',
            'email',
            'address',
            'synced_at',
            'created_at',
            'updated_at',
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
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('address', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $builder;
    }

    public function scopeStore(Builder $builder, array $data = [])
    {
        $supplier = $builder->create($data);
        return $supplier ? true : false;
    }

    public function scopeUpdateModel(Builder $builder, array $data = [], $id)
    {
        $supplier = $builder->where('id', $id)->first();
        if (!$supplier) {
            return false;
        }
        return $supplier->update($data);
    }

    public function scopeDeleteModel(Builder $builder, $id)
    {
        $supplier = $builder->where('id', $id)->first();
        if (!$supplier) {
            return false;
        }
        return $supplier->delete();
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
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
