<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    protected $fillable = [
        'reservation_id',
        'item_id',
        'quantity',
        'price',
        'subtotal',
        'synced_at',
    ];

    protected $hidden = [
        'id',
        'reservation_id',
        'item_id',
        'created_at',
        'updated_at',
        'synced_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // الحجز
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // الصنف
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
