<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Tipo;

class TipoController extends Controller
{
    public function storeTipo(Request $request){
        try {
            $tipo = new Tipo;
            $tipo->tipo = $request->get('tipo');
            $tipo->descripcion = $request->get('descripcion');
            $tipo->activo = true;
            $tipo->timestamps = false;
            $tipo->fecha_creacion = $this->fechaActual();
            $tipo->fecha_modificacion = $this->fechaActual();
            $tipo->usuario_creacion = $request->get('usuario');
            $tipo->usuario_modificacion = $request->get('usuario');
            $tipo->save();
            return $this->crearRespuesta(1, $tipo, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateTipo(Request $request, $id_tipo){
        try {
            $tipo = Tipo::find($id_tipo);
            $tipo->tipo = $request->get('tipo');
            $tipo->descripcion = $request->get('descripcion');
            $tipo->activo = $request->get('activo');;
            $tipo->timestamps = false;
            $tipo->fecha_modificacion = $this->fechaActual();
            $tipo->usuario_modificacion = $request->get('usuario');
            $tipo->save();
            return $this->crearRespuesta(1, $tipo, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getTipos(){
        try {
            $tipos = Tipo::where('activo', 1)
                                    ->orderBy('tipo', 'ASC')                        
                                    ->get();
            return $this->crearRespuesta(1, $tipos, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getTipo($id_tipo){
        try {
            $tipo = Tipo::find($id_tipo);
            return $this->crearRespuesta(1, $tipo, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }

}