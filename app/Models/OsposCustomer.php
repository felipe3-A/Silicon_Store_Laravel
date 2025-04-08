<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OsposCustomer extends Model
{
    use HasFactory;

    // Definir la tabla asociada
    protected $table = 'ospos_customers';

    // Clave primaria de la tabla
    protected $primaryKey = 'person_id';

    // Deshabilitar las marcas de tiempo si no tienes las columnas created_at y updated_at
    public $timestamps = false;

    // Definir qué campos son asignables masivamente
    protected $fillable = [
        'company_name',
        'account_number',
        'taxable',
        'tax_id',
        'sales_tax_code_id',
        'discount',
        'discount_type',
        'package_id',
        'points',
        'deleted',
        'date',
        'employee_id',
        'consent'
    ];

    // Relación con la tabla `ospos_customers_packages` (si es necesario)
    public function package()
    {
        return $this->belongsTo(OsposCustomerPackage::class, 'package_id');
    }

    // Relación con la tabla `ospos_tax_codes` (si es necesario)
    public function salesTaxCode()
    {
        return $this->belongsTo(OsposTaxCode::class, 'sales_tax_code_id');
    }
}

