<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Estatus;

class EstatusController extends Controller
{
    public function storeEstatus(Request $request){
        try {
            $estatus = new Estatus;
            $estatus->estatus = $request->get('estatus');
            $estatus->descripcion = $request->get('descripcion');
            $estatus->tipo = $request->get('tipo');
            $estatus->activo = true;
            $estatus->timestamps = false;
            $estatus->fecha_creacion = $this->fechaActual();
            $estatus->fecha_modificacion = $this->fechaActual();
            $estatus->usuario_creacion = $request->get('usuario');
            $estatus->usuario_modificacion = $request->get('usuario');
            $estatus->save();
            return $this->crearRespuesta(1, $estatus, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateEstatus(Request $request, $id_estatus){
        try {
            $estatus = Estatus::find($id_estatus);
            $estatus->estatus = $request->get('estatus');
            $estatus->descripcion = $request->get('descripcion');
            $estatus->tipo = $request->get('tipo');
            $estatus->activo = $request->get('activo');;
            $estatus->timestamps = false;
            $estatus->fecha_modificacion = $this->fechaActual();
            $estatus->usuario_modificacion = $request->get('usuario');
            $estatus->save();
            return $this->crearRespuesta(1, $estatus, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getEstatusAll(){
        try {
            $estatusAll = Estatus::where('activo', 1)
                                    ->orderBy('estatus', 'ASC')                        
                                    ->get();
            return $this->crearRespuesta(1, $estatusAll, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getEstatus($id_estatus){
        try {
            $estatus = Estatus::find($id_estatus);
            return $this->crearRespuesta(1, $estatus, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }    
    public function setActivoFalsoEstatus(Request $request, $id_estatus){
        try {
            $estatus = Estatus::find($id_estatus);
            $estatus->activo = false;
            $estatus->timestamps = false;
            $estatus->fecha_modificacion = $this->fechaActual();
            $estatus->usuario_modificacion = $request->get('usuario');
            $estatus->save();
            return $this->crearRespuesta(1, $estatus, 'Se ha eliminado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
}