<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OsposCustomerPackage extends Model
{
    // Definir el nombre de la tabla si no sigue la convención de Laravel
    protected $table = 'ospos_customers_packages';

    // Definir la clave primaria
    protected $primaryKey = 'package_id';

    // Desactivar la gestión automática de marcas de tiempo (si no se usan las columnas created_at y updated_at)
    public $timestamps = false;

    // Asegurarnos de que Laravel pueda llenar estos campos de manera masiva
    protected $fillable = [
        'package_name', 'points_percent', 'deleted'
    ];

    // Relación inversa (Un package puede tener muchos clientes)
    public function customers()
    {
        return $this->hasMany(OsposCustomer::class, 'package_id');
    }
}
