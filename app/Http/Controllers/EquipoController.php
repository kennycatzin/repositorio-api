<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Equipo;
use Illuminate\Support\Facades\DB;

class EquipoController extends Controller
{
    public function storeItem(Request $request){
        try {
            $obj = new Equipo;
            $obj->id_estatus = 1;
            $obj->id_marca = $request->get('id_marca');
            $obj->id_licencia_office = $request->get('id_licencia_office');
            $obj->id_licencia_windows = $request->get('id_licencia_windows');
            $obj->id_tipo_equipo = $request->get('id_tipo_equipo');
            $obj->descripcion = $request->get('descripcion');
            $obj->gama = $request->get('gama');
            $obj->estado = $request->get('estado');
            $obj->numero_serie = $request->get('numero_serie');
            $obj->modelo = $request->get('modelo');
            $obj->nombre_equipo = $request->get('nombre_equipo');
            $obj->ram = $request->get('ram');
            $obj->procesador = $request->get('procesador');
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
            $obj = Equipo::find($id_item);
            $obj->id_estatus = $request->get('id_estatus');
            $obj->id_marca = $request->get('id_marca');
            $obj->id_licencia_office = $request->get('id_licencia_office');
            $obj->id_licencia_windows = $request->get('id_licencia_windows');
            $obj->id_tipo_equipo = $request->get('id_tipo_equipo');
            $obj->descripcion = $request->get('descripcion');
            $obj->gama = $request->get('gama');
            $obj->estado = $request->get('estado');
            $obj->numero_serie = $request->get('numero_serie');
            $obj->modelo = $request->get('modelo');
            $obj->nombre_equipo = $request->get('nombre_equipo');
            $obj->ram = $request->get('ram');
            $obj->procesador = $request->get('procesador');
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
            $data = Equipo::where('activo', 1)
                        ->orderBy('descripcion', 'ASC')                        
                        ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getItem($id_item){
        try {
            $data = Equipo::find($id_item);
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }    
    public function setEliminarItem(Request $request, $id_item){
        try {
            $obj = Equipo::find($id_item);
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
            $totales = Equipo::where('activo', 1)
                                    ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $data = Equipo::where('activo', 1)
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
            $query = Equipo::orWhere('descripcion', 'LIKE', '%'.$valor.'%')->get();
            return $this->crearRespuesta(1, $query, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getListaEquiposAdmin(Request $request){
        try {
            $tipo = $request->get('tipo');
            $buscador = $request->get('buscador');
            $id_tipo_equipo = $request->get('id_tipo_equipo');
            $id_estatus_disponible = $this->getEstatusMix("DISPONIBLE");
            $data;
            if($tipo == "TIPO"){
                $data = DB::table('inv_equipo as e')
                ->join('inv_tipo_equipo as t', 't.id', '=', 'e.id_tipo_equipo')
                ->join('inv_marcas as m', 'm.id', '=', 'e.id_marca')
                ->select('e.id as id_equipo', 't.tipo_equipo', 'm.marca', 
                'e.descripcion', 'e.numero_serie')
                ->where('e.id_estatus', $id_estatus_disponible)
                ->where('e.id_tipo_equipo', $id_tipo_equipo)
                ->where('e.activo', true)
                ->get();
            }else{
                $data = DB::table('inv_equipo as e')
                ->join('inv_tipo_equipo as t', 't.id', '=', 'e.id_tipo_equipo')
                ->join('inv_marcas as m', 'm.id', '=', 'e.id_marca')
                ->join('estatus as es', 'es.id', '=', 'e.id_estatus')
                ->select('e.id as id_equipo', 't.tipo_equipo', 'm.marca', 
                'e.descripcion', 'e.numero_serie', 'e.id_estatus', 'es.estatus')
                ->where('e.id_estatus', $id_estatus_disponible)
                ->where('e.activo', true)
                ->where(function ($query) use ($buscador) {
                    $query->orWhere('e.descripcion', 'LIKE', '%'.$buscador.'%')
                    ->orWhere('e.numero_serie', 'LIKE', '%'.$buscador.'%')
                    ->orWhere('e.nombre_equipo', 'LIKE', '%'.$buscador.'%')
                    ->orWhere('e.modelo', 'LIKE', '%'.$buscador.'%');
                })
                ->get();
            }
            
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}