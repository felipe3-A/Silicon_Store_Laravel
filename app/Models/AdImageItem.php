<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdImageItem extends Model
{
    use HasFactory;

    public $timestamps = false; // Desactiva timestamps

    protected $table = 'ospos_ad_image_items';

    protected $fillable = ['image_id', 'item_id']; // Solo los campos existentes
}
