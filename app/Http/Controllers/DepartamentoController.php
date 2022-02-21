<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Departamento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
            $departamento->timestamps = false;
            $departamento->fecha_modificacion = $this->fechaActual();
            $departamento->usuario_modificacion = $request->get('usuario');
            $departamento->save();
            return $this->crearRespuesta(1, $departamento, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function bajaDepartamento(Request $request, $id_departamento){
        try {           
            $departamento = Departamento::find($id_departamento);
            $departamento->activo = false;
            $departamento->timestamps = false;
            $departamento->fecha_modificacion = $this->fechaActual();
            $departamento->usuario_modificacion = $request->get('usuario');
            $departamento->save();
            return $this->crearRespuesta(1,  null, 'Se ha eliminado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización '.$th->getMessage(), 300);
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
    public function busqueda(Request $request){
        try {
            $valor = $request['busqueda'];
            $query = Departamento::orWhere('departamento', 'LIKE', '%'.$valor.'%')->get();
            return $this->crearRespuesta(1, $query, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getDepartamentosPaginado($index){
        try {
            $totales = Departamento::where('activo', 1)
                                    ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $departamentos = Departamento::where('activo', 1)
                                    ->skip($index)
                                    ->take(5)
                                    ->orderBy('departamento', 'ASC')                        
                                    ->get();
            return response()->json([
                'data' => $departamentos,
                'mensaje' => $totales,
                'paginas' => $resultado,
                'ok' => true
            ], 200);
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
    public function basesDiariasEnviar(Request $request){
        try {
            $ayer = date( "d-m-Y", strtotime( "-1 day", strtotime( $this->fechaCruda() ) ) ); 
            $texto = "";
            if($request->get('tipo') == 1){
                $texto = "Se les notifica que las bases diarias se encuentran actualizadas al día de ".$ayer.".";
            }else if($request->get('tipo') == 2){
                $texto = "Se les notifica que las bases diarias se encuentran actualizadas al día de ".$ayer.", excepto: ".$request->get('sucursales')." se les notificará cuando estas se actualicen.";
            }else if($request->get('tipo') == 3){
                $texto = "Se les notifica que las bases diarias faltantes de: ".$request->get('sucursales')." se encuentran actualizadas al día de ".$ayer.".";
            }
            $data = array(
                'titulo' => "Bases diarias",
                'fecha_actual' => $this->fechaCruda(),
                'observaciones' =>  $texto
            );
            Mail::send('plantilla_informativa', $data, function($message)  {
                $message->to(['auditoria@tuereselequipo.com', 'tesoreria@tuereselequipo.com'], 'Bases diarias')
                        ->subject('Bases diarias');
                $message->from(env('MAIL_USERNAME'),'Informática STI');
            });
            return $this->crearRespuesta(1, null, 'Se ha enviado el correo bases diarias', 200);        } catch (\Throwable $th) {
        }catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function cambioPrecioEnviar(){
        try {
            $data = array(
                'titulo' => "Precio metal",
                'fecha_actual' => $this->fechaCruda(),
                'observaciones' =>  "Se les notifica que el precio del metal del día de hoy, se ha aplicado al sistema."
            );
            Mail::send('plantilla_informativa', $data, function($message)  {
                $message->to('sucursales@tuereselequipo.com', 'Precio metal')
                        ->cc(['dudasdesucursales@tuereselequipo.com'])
                        ->subject('Precio metal');
                $message->from(env('MAIL_USERNAME'),'Informática STI');
            });
            return $this->crearRespuesta(1, null, 'Se ha enviado el correo cambio precio', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}