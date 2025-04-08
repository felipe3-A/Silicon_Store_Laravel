<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataInfo extends Model
{
    use HasFactory;

    protected $table = 'data_info';
    protected $primaryKey = 'iddata_info';

    protected $fillable = [
        'data',
        'info',
        'icono',
    ];
}
