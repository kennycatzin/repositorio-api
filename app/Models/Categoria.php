<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = [
        'titulo', 'descripcion', 'orden',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'categoria';
}