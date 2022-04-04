<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    protected $fillable = [
        'id_estatus', 'id_tipo_articulo', 'licencia', 'descripcion', 'tipo', 'version',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'inv_licencias';
    public $timestamps = false;

}