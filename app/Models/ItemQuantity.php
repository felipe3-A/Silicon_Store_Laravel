<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemQuantity extends Model
{
    protected $table = 'ospos_item_quantities';
    public $timestamps = false;

    protected $fillable = ['item_id', 'location_id', 'quantity'];

    public function item()
    {
        return $this->belongsTo(PosItem::class, 'item_id');
    }
}
