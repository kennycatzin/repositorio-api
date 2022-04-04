<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $fillable = [
        'id_estatus', 'id_marca', 'id_tipo_articulo', 'descripcion', 'gama', 'numero_serie', 'estado',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'inv_articulos';
    public $timestamps = false;

}