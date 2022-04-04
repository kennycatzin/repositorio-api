<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Articulo;

class ArticuloController extends Controller
{
    public function storeItem(Request $request){
        try {
            $obj = new Articulo;
            $obj->id_estatus = 1;
            $obj->id_marca = $request->get('id_marca');
            $obj->id_tipo_articulo = $request->get('id_tipo_articulo');
            $obj->descripcion = $request->get('descripcion');
            $obj->gama = $request->get('gama');
            $obj->estado = $request->get('estado');
            $obj->numero_serie = $request->get('numero_serie');
            $obj->activo = true;
            $obj->timestamps = false;
            $obj->fecha_creacion = $this->fechaActual();
            $obj->fecha_modificacion = $this->fechaActual();
            $obj->usuario_creacion = $request->get('usuario');
            $obj->usuario_modificacion = $request->get('usuario');
            $obj->save();
            return $this->crearRespuesta(1, $obj, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateItem(Request $request, $id_item){
        try {
            $obj = Articulo::find($id_item);
            $obj->id_estatus = $request->get('id_estatus');
            $obj->id_marca = $request->get('id_marca');
            $obj->id_tipo_articulo = $request->get('id_tipo_articulo');
            $obj->descripcion = $request->get('descripcion');
            $obj->gama = $request->get('gama');
            $obj->estado = $request->get('estado');
            $obj->numero_serie = $request->get('numero_serie');
            $obj->timestamps = false;
            $obj->fecha_modificacion = $this->fechaActual();
            $obj->usuario_modificacion = $request->get('usuario');
            $obj->save();
            return $this->crearRespuesta(1, $obj, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getAllItems(){
        try {
            $data = Articulo::where('activo', 1)
                        ->orderBy('descripcion', 'ASC')                        
                        ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getItem($id_item){
        try {
            $data = Articulo::find($id_item);
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }    
    public function setEliminarItem(Request $request, $id_item){
        try {
            $obj = Articulo::find($id_item);
            $obj->activo = false;
            $obj->timestamps = false;
            $obj->fecha_modificacion = $this->fechaActual();
            $obj->usuario_modificacion = $request->get('usuario');
            $obj->save();
            return $this->crearRespuesta(1, $obj, 'Se ha eliminado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }    
    public function getItemsPaginado($index){
        try {
            $totales = Articulo::where('activo', 1)
                                    ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $data = Articulo::where('activo', 1)
                                    ->skip($index)
                                    ->take(5)
                                    ->orderBy('descripcion', 'ASC')                        
                                    ->get();
            return response()->json([
                'data' => $data,
                'mensaje' => $totales,
                'paginas' => $resultado,
                'ok' => true
            ], 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function busqueda(Request $request){
        try {
            $valor = $request['busqueda'];
            $query = Articulo::orWhere('descripcion', 'LIKE', '%'.$valor.'%')->get();
            return $this->crearRespuesta(1, $query, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}