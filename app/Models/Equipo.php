<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $fillable = [
        'id_estatus', 'id_marca', 'id_licencia_office', 'id_licencia_windows','id_tipo_equipo', 
        'descripcion', 'gama', 'estado','numero_serie', 'modelo', 'nombre_equipo', 'ram', 'procesador',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'inv_equipo';
    public $timestamps = false;

}