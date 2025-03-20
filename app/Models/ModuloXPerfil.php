<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuloXPerfil extends Model {
    use HasFactory;

    protected $table = 'modulos_x_perfil'; // Nombre de la tabla
    protected $fillable = ['idmodulo', 'idperfil', 'permiso']; // Campos permitidos para asignación masiva

    // Relación con la tabla Modulos
    public function modulo() {
        return $this->belongsTo(Modulo::class, 'idmodulo');
    }

    // Relación con la tabla Perfiles
    public function perfil() {
        return $this->belongsTo(Perfil::class, 'idperfil');
    }
}
