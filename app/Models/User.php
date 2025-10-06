<?php

namespace App\Models;

use App\Helpers\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles, HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'username',
        'email',
        'phone',
        'role', // admin, cashier, purchaser, inventory_manager, owner
        'status',
        'password',
        'last_login_at',
        'synced_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function scopeData($builder)
    {
        return $builder->select([
            'id',
            'uuid',
            'name',
            'username',
            'email',
            'phone',
            'role',
            'status',
            'last_login_at',
            'synced_at',
            'created_at',
            'updated_at',
        ]);
    }

    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $filters = array_merge([
            'search' => null,
            'status' => null,
            'role' => null,
        ], $filters);

        if ($filters['search']) {
            $builder->where(function (Builder $builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('username', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['status']) {
            $builder->where('status', $filters['status']);
        }

        if ($filters['role']) {
            $builder->where('role', $filters['role']);
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
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user = $builder->find($id);
        if (!$user) {
            return false;
        }
        return $user->update($data);
    }

    public function scopeDeleteModel(Builder $builder, int $id)
    {
        $user = $builder->find($id);
        if (!$user) {
            return false;
        }
        return $user->delete();
    }

    public function scopeChangeAccountStatus(Builder $builder, int $id)
    {
        $user = $builder->find($id);
        if (!$user) {
            return false;
        }
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        return $user->save();
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
