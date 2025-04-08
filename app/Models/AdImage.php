<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdImage extends Model
{
    use HasFactory;

    protected $table = 'ospos_ad_images'; // Especificamos el nombre correcto de la tabla

    protected $fillable = ['image_url', 'description'];

    public function items()
    {
        return $this->hasMany(AdImageItem::class, 'id');
    }

    public function attributes()
    {
        return $this->hasMany(AdImageAttribute::class, 'id');
    }
}
