<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OsposTaxCode extends Model
{
    // Definir el nombre de la tabla si no sigue la convención de Laravel
    protected $table = 'ospos_tax_codes';

    // Definir la clave primaria
    protected $primaryKey = 'tax_code_id';

    // Desactivar la gestión automática de marcas de tiempo (si no se usan las columnas created_at y updated_at)
    public $timestamps = false;

    // Asegurarnos de que Laravel pueda llenar estos campos de manera masiva
    protected $fillable = [
        'tax_code', 'tax_code_name', 'city', 'state', 'deleted'
    ];

    // Relación inversa (Un tax code puede tener muchos clientes)
    public function customers()
    {
        return $this->hasMany(OsposCustomer::class, 'sales_tax_code_id');
    }
}
