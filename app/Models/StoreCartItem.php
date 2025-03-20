<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCartItem extends Model {
    use HasFactory;

    protected $fillable = ['cart_id', 'item_id', 'quantity', 'subtotal'];

    public function cart() {
        return $this->belongsTo(StoreShoppingCart::class, 'cart_id');
    }

    public function product() {
        return $this->belongsTo(PosItem::class, 'item_id');
    }
}

