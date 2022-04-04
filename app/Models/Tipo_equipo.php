<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipo_equipo extends Model
{
    protected $fillable = [
        'tipo', 'tipo_articulo', 'descripcion',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'inv_tipo_equipo';
    public $timestamps = false;

}