<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Subcategoria;

class SubcategoriaController extends Controller
{
    public function storeSubcategoria(Request $request){
        try {
            $subcategoria = new Subcategoria;
            $subcategoria->id_categoria = $request->get('orden');
            $subcategoria->descripcion = $request->get('descripcion');
            $subcategoria->orden = $request->get('orden');
            $subcategoria->activo = true;
            $subcategoria->timestamps = false;
            $subcategoria->fecha_creacion = $this->fechaActual();
            $subcategoria->fecha_modificacion = $this->fechaActual();
            $subcategoria->usuario_creacion = $request->get('usuario');
            $subcategoria->usuario_modificacion = $request->get('usuario');
            $subcategoria->save();
            return $this->crearRespuesta(1, $subcategoria, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateSubcategoria(Request $request, $id_subcategoria){
        try {
            $subcategoria = Subcategoria::find($id_subcategoria);
            $subcategoria->descripcion = $request->get('descripcion');
            $subcategoria->orden = $request->get('orden');
            $subcategoria->activo = $request->get('activo');;
            $subcategoria->timestamps = false;
            $subcategoria->fecha_modificacion = $this->fechaActual();
            $subcategoria->usuario_modificacion = $request->get('usuario');
            $subcategoria->save();
            return $this->crearRespuesta(1, $subcategoria, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getSubcategorias(){
        try {
            $subcategorias = Subcategoria::where('activo', 1)
                                    ->orderBy('orden', 'ASC')                        
                                    ->get();
            return $this->crearRespuesta(1, $subcategorias, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getSubcategoria($id_subcategoria){
        try {
            $subcategoria = Subcategoria::find($id_subcategoria);
            return $this->crearRespuesta(1, $subcategoria, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}