<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSale extends Model
{
    protected $table = 'ospos_sales';
    protected $primaryKey = 'sale_id';
    public $timestamps = false;

    protected $fillable = [
        'sale_time', 'customer_id', 'employee_id', 'comment', 'invoice_number',
        'quote_number', 'sale_status', 'dinner_table_id', 'work_order_number', 'sale_type'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(PosSalePayment::class, 'sale_id', 'sale_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PosSaleItem::class, 'sale_id', 'sale_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'employee_id', 'person_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(OsposCustomer::class, 'customer_id', 'person_id');
    }
}
