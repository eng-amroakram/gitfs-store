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
        'status',
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
