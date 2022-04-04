<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Tipo_equipo;

class TipoEquipoController extends Controller
{
    public function storeItem(Request $request){
        try {
            $obj = new Tipo_equipo;
            $obj->tipo = $request->get('tipo');
            $obj->tipo_equipo = $request->get('tipo_equipo');
            $obj->descripcion = $request->get('descripcion');
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
            $obj = Tipo_equipo::find($id_item);
            $obj->tipo = $request->get('tipo');
            $obj->tipo_equipo = $request->get('tipo_equipo');
            $obj->descripcion = $request->get('descripcion');
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
            $data = Tipo_equipo::where('activo', 1)
                        ->orderBy('tipo_equipo', 'ASC')                        
                        ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getItem($id_item){
        try {
            $data = Tipo_equipo::find($id_item);
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }    
    public function setEliminarItem(Request $request, $id_item){
        try {
            $obj = Tipo_equipo::find($id_item);
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
            $totales = Tipo_equipo::where('activo', 1)
                                    ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $data = Tipo_equipo::where('activo', 1)
                                    ->skip($index)
                                    ->take(5)
                                    ->orderBy('tipo_equipo', 'ASC')                        
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
            $query = Tipo_equipo::orWhere('tipo_equipo', 'LIKE', '%'.$valor.'%')->get();
            return $this->crearRespuesta(1, $query, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getTipoEquipoAdmin(){
        try {
            $data = Tipo_equipo::where('activo', 1)
            ->select('id', 'tipo_equipo')
            ->orderBy('tipo_equipo', 'ASC')
            ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}