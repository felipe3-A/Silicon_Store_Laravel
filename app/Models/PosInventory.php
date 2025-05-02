<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosInventory extends Model
{
    protected $table = 'ospos_inventory';
    protected $primaryKey = 'trans_id';
    public $timestamps = false;

    protected $fillable = [
        'trans_items', 'trans_user', 'trans_date', 'trans_comment',
        'trans_location', 'trans_inventory'
    ];

    public function item()
    {
        return $this->belongsTo(PosItem::class, 'trans_items', 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(Person::class, 'trans_user', 'person_id');
    }

    public function location()
    {
        return $this->belongsTo(PosStockLocation::class, 'trans_location', 'location_id');
    }
}
