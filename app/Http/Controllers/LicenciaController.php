<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Licencia;
use Illuminate\Support\Facades\DB;

class LicenciaController extends Controller
{
    public function storeItem(Request $request){
        try {
            $obj = new Licencia;
            $obj->id_estatus = $request->get('id_estatus');
            $obj->id_tipo_articulo = $request->get('id_tipo_articulo');
            $obj->licencia = $request->get('licencia');
            $obj->descripcion = $request->get('descripcion');
            $obj->tipo = $request->get('tipo');
            $obj->version = $request->get('version');
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
            $obj = Licencia::find($id_item);
            $obj->id_estatus = $request->get('id_estatus');
            $obj->id_tipo_articulo = $request->get('id_tipo_articulo');
            $obj->licencia = $request->get('licencia');
            $obj->descripcion = $request->get('descripcion');
            $obj->tipo = $request->get('tipo');
            $obj->version = $request->get('version');
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
            $data = Licencia::where('activo', 1)
                        ->orderBy('licencia', 'ASC')                        
                        ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getItem($id_item){
        try {
            $data = Licencia::find($id_item);
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }    
    public function setEliminarItem(Request $request){
        try {
            $obj = Licencia::find($request->get('id'));
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
            $totales = Licencia::where('activo', 1)
                                    ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $data = DB::table('inv_licencias as l')
                            ->join('estatus as e', 'e.id', '=', 'l.id_estatus')
                            ->select('l.*', 'e.estatus')
                            ->where('l.activo', 1)
                            ->skip($index)
                            ->take(8)
                            ->orderBy('licencia', 'ASC')                        
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
            $conteo = DB::table('inv_licencias as l')
            ->join('estatus as e', 'e.id', '=', 'l.id_estatus')
            ->select('l.*', 'e.estatus')
            ->orWhere('l.licencia', 'LIKE', '%'.$valor.'%')
            ->orWhere('e.estatus', 'LIKE', '%'.$valor.'%')
            ->where('l.activo', 1)
            ->count();
            $query = DB::table('inv_licencias as l')
            ->join('estatus as e', 'e.id', '=', 'l.id_estatus')
            ->select('l.*', 'e.estatus')
            ->orWhere('l.licencia', 'LIKE', '%'.$valor.'%')
            ->orWhere('e.estatus', 'LIKE', '%'.$valor.'%')
            ->where('l.activo', 1)
            ->get();
            return response()->json([
                'data' => $query,
                'mensaje' => $conteo,
                'paginas' => 1,
                'ok' => true
            ], 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getEstatusLicencias(){
        try {
            $data = DB::table('estatus')
            ->select('id as id_estatus', 'estatus')
            ->where('activo', 1)
            ->whereIn('estatus', ['DISPONIBLE', 'ASIGNADO'])
            ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
}