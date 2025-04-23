<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    // Especificamos la tabla, si no sigue la convención plural en inglés
    protected $table = 'ospos_stock_locations';

    // Especificamos la clave primaria si no es 'id'
    protected $primaryKey = 'location_id';

    // Establecemos que no hay marca de tiempo en la tabla
    public $timestamps = false;

    // Si deseas que los registros estén protegidos contra asignaciones masivas
    protected $fillable = ['location_name', 'deleted'];
}
