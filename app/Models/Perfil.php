<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model {
    use HasFactory;

    protected $table = 'perfil'; // Nombre de la tabla en la BD
    protected $primaryKey = 'idperfil'; // Definir la clave primaria personalizada
    public $timestamps = true; // Mantener timestamps

    protected $fillable = ['perfil']; // Campos que se pueden asignar masivamente
}

