<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'item_id',
        'quantity',
        'price',
        'subtotal',
        'synced_at',
    ];

    protected $hidden = [
        'id',
        'purchase_id',
        'item_id',
        'created_at',
        'updated_at',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // الفاتورة
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
