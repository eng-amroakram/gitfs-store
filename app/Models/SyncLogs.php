<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SyncLogs extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'syncable_id',
        'syncable_type',
        'synced_at',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'user_id',
            'syncable_id',
            'syncable_type',
            'synced_at',
            'created_at',
            'updated_at',
        ]);
    }

    public function syncable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilters($builder, array $filters = [])
    {
        return $builder;
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
