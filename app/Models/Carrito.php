<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    protected $table = 'store_shopping_carts'; // o 'carritos' si tu tabla se llama así
    protected $fillable = ['person_id'];
    public $timestamps = true; // o false si no usas created_at / updated_at
}

