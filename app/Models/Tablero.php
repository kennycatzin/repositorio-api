<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tablero extends Model
{
    protected $fillable = [
        'titulo', 'descripcion', 'url', 'imagen', 'tipo',
        'orden', 'fecha_inicio', 'fecha_final',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'tablero';
}