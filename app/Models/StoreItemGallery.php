<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreItemGallery extends Model
{
    protected $fillable = ['item_id', 'images'];
    protected $casts = [
        'images' => 'array',
    ];

    public function item()
    {
        return $this->belongsTo(PosItem::class, 'item_id');
    }
}
