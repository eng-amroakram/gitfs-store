<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'item_id',
        'quantity',
        'price',
        'subtotal',
        'synced_at',
    ];

    protected $hidden = [
        'id',
        'sale_id',
        'item_id',
        'created_at',
        'updated_at',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // الفاتورة
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
