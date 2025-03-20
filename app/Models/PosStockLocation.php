<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PosStockLocation extends Model
{
    protected $table = 'ospos_stock_locations'; // Nombre correcto de la tabla

    protected $primaryKey = 'location_id';

    public $timestamps = false;

    protected $fillable = [
        'location_name',
        'deleted'
    ];

    /**
     * Relación con las cantidades de stock.
     * Una ubicación puede tener múltiples productos en stock.
     */
    public function itemQuantities(): HasMany
    {
        return $this->hasMany(PosItemQuantity::class, 'location_id', 'location_id');
    }
}
