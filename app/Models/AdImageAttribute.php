<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdImageAttribute extends Model
{
    use HasFactory;

    public $timestamps = false; // Desactiva timestamps si no están en la BD

    protected $table = 'ospos_ad_image_attributes';

    protected $fillable = ['image_id', 'definition_id'];
}
