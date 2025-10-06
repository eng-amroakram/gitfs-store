<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait and assign a UUID when creating the model.
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Boot created_by on only create model
    protected static function bootCreatedBy()
    {
        static::creating(function ($model) {
            if (Auth::check() && empty($model->created_by)) {
                $model->created_by = Auth::id();
            }
        });
    }

    // Boot updated_by on create and update model
    protected static function bootUpdatedBy()
    {
        static::saving(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }
}
