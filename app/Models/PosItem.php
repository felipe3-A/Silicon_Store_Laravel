<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PosItem extends Model
{
    protected $table = 'ospos_items'; // Asegúrate de que este nombre coincida con la tabla en tu BD

    protected $primaryKey = 'item_id'; // Definir clave primaria si no es "id"

    public $timestamps = false; // Desactivar timestamps si la tabla no tiene created_at y updated_at

    protected $fillable = [
        'name',
        'category',
        'supplier_id',
        'item_number',
        'description',
        'cost_price',
        'unit_price',
        'reorder_level',
        'receving_quantity',
        'pic_filename',
        'alow_alt_description',
        'is_serialized',
        'stock_type',
        'item_type',
        'deleted',
        'tax_category_id',
        'qty_per_pack',
        'pack_name',
        'low_sell_item_id',
        'hsn_code'
    ];

    /**
     * Relación con la cantidad de stock por ubicación.
     * Un producto puede tener múltiples cantidades en diferentes ubicaciones.
     */
    public function stockQuantities(): HasMany
    {
        return $this->hasMany(PosItemQuantity::class, 'item_id', 'item_id');
    }

    public function cartItems() {
        return $this->hasMany(StoreCartItem::class, 'item_id', 'item_id');
    }
    // En el modelo PosItem

public function stockLocations()
{
    return $this->belongsToMany(StockLocation::class, 'stock_item_location', 'item_id', 'location_id')
                ->withPivot('quantity');
}

}
