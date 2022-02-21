<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $fillable = [
        'id_subcategoria', 'nombre', 'descripcion', 'resumen', 'id_departamento', 'consecutivo',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'archivo';
    public $timestamps = false;

}