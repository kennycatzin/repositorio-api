<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Marca;

class MarcaController extends Controller
{
    public function storeMarca(Request $request){
        try {
            $marca = new Marca;
            $marca->marca = $request->get('marca');
            $marca->descripcion = $request->get('descripcion');
            $marca->gama = $request->get('gama');
            $marca->activo = true;
            $marca->timestamps = false;
            $marca->fecha_creacion = $this->fechaActual();
            $marca->fecha_modificacion = $this->fechaActual();
            $marca->usuario_creacion = $request->get('usuario');
            $marca->usuario_modificacion = $request->get('usuario');
            $marca->save();
            return $this->crearRespuesta(1, $marca, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateMarca(Request $request, $id_marca){
        try {
            $marca = Marca::find($id_marca);
            $marca->marca = $request->get('marca');
            $marca->descripcion = $request->get('descripcion');
            $marca->gama = $request->get('gama');
            $marca->timestamps = false;
            $marca->fecha_modificacion = $this->fechaActual();
            $marca->usuario_modificacion = $request->get('usuario');
            $marca->save();
            return $this->crearRespuesta(1, $marca, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getMarcasAll(){
        try {
            $data = Marca::where('activo', 1)
                        ->orderBy('marca', 'ASC')                        
                        ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getMarca($id_marca){
        try {
            $data = Marca::find($id_marca);
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }    
    public function setEliminarMarca(Request $request){
        try {
            $marca = Marca::find($request->get('id_marca'));
            $marca->activo = false;
            $marca->timestamps = false;
            $marca->fecha_modificacion = $this->fechaActual();
            $marca->usuario_modificacion = $request->get('usuario');
            $marca->save();
            return $this->crearRespuesta(1, $marca, 'Se ha eliminado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }    
    public function getMarcasPaginado($index){
        try {
            $totales = Marca::where('activo', 1)
                                    ->count();
            $resultado = $totales / 5;
            $resultado = ceil($resultado);
            $data = Marca::where('activo', 1)
                                    ->skip($index)
                                    ->take(5)
                                    ->orderBy('marca', 'ASC')                        
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
            $query = Marca::orWhere('marca', 'LIKE', '%'.$valor.'%')->get();
            return $this->crearRespuesta(1, $query, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}