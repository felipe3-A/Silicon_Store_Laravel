<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreShoppingCart extends Model {
    use HasFactory;

    protected $fillable = ['person_id', 'total'];

    public function user() {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function items() {
        return $this->hasMany(StoreCartItem::class, 'cart_id');
    }
}
