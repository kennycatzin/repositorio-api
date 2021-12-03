<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Departamento;
use Illuminate\Support\Facades\DB;

class DepartamentoController extends Controller
{
    public function storeDepartamento(Request $request){
        try {
            $departamento = new Departamento;
            $departamento->departamento = $request->get('departamento');
            $departamento->nombre_corto = $request->get('nombre_corto');
            $departamento->descripcion = $request->get('descripcion');
            $departamento->correo = $request->get('correo');
            $departamento->activo = true;
            $departamento->timestamps = false;
            $departamento->fecha_creacion = $this->fechaActual();
            $departamento->fecha_modificacion = $this->fechaActual();
            $departamento->usuario_creacion = $request->get('usuario');
            $departamento->usuario_modificacion = $request->get('usuario');
            $departamento->save();
            return $this->crearRespuesta(1, $departamento, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateDepartamento(Request $request, $id_departamento){
        try {
            $departamento = Departamento::find($id_departamento);
            $departamento->departamento = $request->get('departamento');
            $departamento->nombre_corto = $request->get('nombre_corto');
            $departamento->descripcion = $request->get('descripcion');
            $departamento->correo = $request->get('correo');
            $departamento->activo = $request->get('activo');
            $departamento->timestamps = false;
            $departamento->fecha_modificacion = $this->fechaActual();
            $departamento->usuario_modificacion = $request->get('usuario');
            $departamento->save();
            return $this->crearRespuesta(1, $departamento, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getDepartamentos(){
        try {
            $departamentos = Departamento::where('activo', 1)
                                    ->orderBy('departamento', 'ASC')                        
                                    ->get();
            return $this->crearRespuesta(1, $departamentos, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getDepartamento($id_departamento){
        try {
            $departamento = Departamento::find($id_departamento);
            return $this->crearRespuesta(1, $departamento, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}