<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $fillable = [
        'tipo', 'descripcion', 'nombre_corto',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'tipo';
}