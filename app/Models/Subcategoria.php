<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    protected $fillable = [
        'id_categoria', 'descripcion', 'orden', 'titulo',
        'activo', 'fecha_creacion', 'fecha_modificacion',
        'usuario_creacion', 'usuario_modificacion'
    ];
    protected $table = 'subcategoria';
    public $timestamps = false;

}