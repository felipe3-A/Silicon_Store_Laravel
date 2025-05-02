<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSaleItem extends Model
{
    protected $table = 'ospos_sales_items';
    public $timestamps = false;
    public $incrementing = false;

    protected $primaryKey = null;

    protected $fillable = [
        'sale_id', 'item_id', 'description', 'serialnumber', 'line',
        'quantity_purchased', 'item_cost_price', 'item_unit_price',
        'discount', 'discount_type', 'item_location', 'print_option'
    ];

    public function item()
    {
        return $this->belongsTo(PosItem::class, 'item_id', 'item_id');
    }

    public function sale()
    {
        return $this->belongsTo(PosSale::class, 'sale_id', 'sale_id');
    }

    public function location()
    {
        return $this->belongsTo(PosStockLocation::class, 'item_location', 'location_id');
    }
}
