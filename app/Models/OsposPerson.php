<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OsposPerson extends Model
{
    use HasFactory;

    protected $table = 'ospos_people';
    protected $primaryKey = 'person_id';
    public $timestamps = false; // Habilita timestamps si la tabla los tiene

    protected $fillable = [
        'first_name', 'last_name', 'gender', 'phone_number',
        'email', 'address_1', 'address_2', 'city', 'state',
        'zip', 'country', 'comments'
    ];
}
