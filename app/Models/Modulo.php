<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    // Especificar la tabla (opcional si sigue la convención de nombres)
    protected $table = 'modulos';

    // Especificar los campos que se pueden asignar masivamente
    protected $fillable = [
        'id_modulo_padre',
        'modulo',
        'url_modulo',
        'icono',
        'orden'
    ];

    // Relación con el módulo padre (auto-relación)
    public function moduloPadre()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo_padre');
    }

    // Relación con los módulos hijos
    public function subModulos()
    {
        return $this->hasMany(Modulo::class, 'id_modulo_padre');
    }
}
