<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosItemQuantity extends Model
{
    protected $table = 'ospos_item_quantities'; // Nombre correcto de la tabla

    public $timestamps = false; // Desactivar timestamps si la tabla no los tiene

    protected $fillable = [
        'item_id',
        'location_id',
        'quantity'
    ];

    // Relación con la tabla de ubicaciones de stock
    public function location()
    {
        return $this->belongsTo(PosStockLocation::class, 'location_id', 'location_id');
    }
}
