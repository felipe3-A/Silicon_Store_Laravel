<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSalePayment extends Model
{
    protected $table = 'ospos_sales_payments';
    protected $primaryKey = 'payment_id';
    public $timestamps = false;

    protected $fillable = [
        'sale_id', 'payment_type', 'payment_amount', 'cash_refund',
        'cash_adjustment', 'employee_id', 'payment_time', 'reference_code'
    ];

    public function sale()
    {
        return $this->belongsTo(PosSale::class, 'sale_id', 'sale_id');
    }

    public function employee()
    {
        return $this->belongsTo(Person::class, 'employee_id', 'person_id');
    }
}
