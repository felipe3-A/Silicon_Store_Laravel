<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model {
    use HasFactory;

    protected $table = 'ospos_people'; // Especificamos la tabla real
    protected $primaryKey = 'person_id';

    protected $fillable = ['first_name', 'last_name', 'email', 'phone_number'];

    public function cart() {
        return $this->hasOne(StoreShoppingCart::class, 'person_id');
    }
}
