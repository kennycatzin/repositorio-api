<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Equipo;
use Illuminate\Support\Facades\DB;

class EquipoController extends Controller
{
    public function storeItem(Request $request){
        try {
            $id_disponible = $this->getEstatusMix("DISPONIBLE");
            $obj = new Equipo;
            $obj->id_estatus = $id_disponible;
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
            $obj->id_licencia_office = $request->get('id_licencia_office');
            $obj->id_licencia_windows = $request->get('id_licencia_windows');
            if(!!$request->get('id_licencia_office')){
                $this->setLicenciaMovimiento($request->get('id_licencia_office'), "OFFICE", $id_item);
            }
            if(!!$request->get('id_licencia_windows')){
                $this->setLicenciaMovimiento($request->get('id_licencia_windows'), "WINDOWS", $id_item);
            }
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
    public function setLicenciaMovimiento($id_licencia, $tipo, $id_equipo){
        
            $licencia;
            $id_asignado = $this->getEstatusMix("ASIGNADO");
            $id_disponible = $this->getEstatusMix("DISPONIBLE");
            // query de la licencia por el idequipo en la tabla equipo           
            if($tipo == "OFFICE"){
                $licencia = DB::table('inv_equipo')
                    ->select('id_licencia_office as licencia')
                    ->where('id', $id_equipo)
                    ->first();
            }else if($tipo == "WINDOWS"){
                $licencia = DB::table('inv_equipo')
                    ->select('id_licencia_windows as licencia')
                    ->where('id', $id_equipo)
                    ->first();
            }    
            // primero dejar disponible por medio de equipo
            DB::update('update inv_licencias set id_estatus = ?
                where id = ?', 
                [$id_disponible, $licencia->licencia]);
            
            //asignar la nueva licencia id_licencia

            DB::update('update inv_licencias set id_estatus = ?
                where id = ?', 
                [$id_asignado, $id_licencia]);
                
       

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
            $obj->id_licencia_windows = 0;
            $this->setLicenciaMovimiento(0, "WINDOWS", $id_item);
            $obj->id_licencia_office = 0;
            $this->setLicenciaMovimiento(0, "OFFICE", $id_item);
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
            $data = DB::table('inv_equipo as e')
                    ->join('inv_tipo_equipo as t', 't.id', '=', 'e.id_tipo_equipo')
                    ->join('inv_marcas as m', 'm.id', '=', 'e.id_marca')
                    ->join('estatus as es', 'es.id', '=', 'e.id_estatus')
                    ->leftJoin('inv_licencias as lo', 'lo.id', '=', 'e.id_licencia_office')
                    ->leftJoin('inv_licencias as lw', 'lw.id', '=', 'e.id_licencia_windows')
                    ->select('e.id', 't.tipo_equipo', 'm.marca', 'e.id_tipo_equipo', 'e.id_marca',
                    'e.descripcion', 'e.numero_serie', 'e.id_estatus', 'es.estatus',
                    'e.id_licencia_office', 'e.id_licencia_windows', 'e.modelo', 'e.nombre_equipo', 'e.ram', 'e.procesador',
                    'lo.licencia as licencia_office', 'lw.licencia as licencia_windows')
                    ->where('e.activo', true)
                    ->skip($index)
                    ->take(8)
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
            $buscador = $request['busqueda'];
            $conteo = DB::table('inv_equipo as e')
            ->join('inv_tipo_equipo as t', 't.id', '=', 'e.id_tipo_equipo')
            ->join('inv_marcas as m', 'm.id', '=', 'e.id_marca')
            ->join('estatus as es', 'es.id', '=', 'e.id_estatus')
            ->leftJoin('inv_licencias as lo', 'lo.id', '=', 'e.id_licencia_office')
            ->leftJoin('inv_licencias as lw', 'lw.id', '=', 'e.id_licencia_windows')
            ->select('e.id', 't.tipo_equipo', 'm.marca', 'e.id_tipo_equipo', 'e.id_marca',
            'e.descripcion', 'e.numero_serie', 'e.id_estatus', 'es.estatus',
            'e.id_licencia_office', 'e.id_licencia_windows', 'e.modelo', 'e.nombre_equipo', 'e.ram', 'e.procesador',
            'lo.licencia as licencia_office', 'lw.licencia as licencia_windows')
            //->where('e.id_estatus', $id_estatus_disponible)
            ->where('e.activo', true)
            ->where(function ($query) use ($buscador) {
                $query->orWhere('e.descripcion', 'LIKE', '%'.$buscador.'%')
                ->orWhere('e.numero_serie', 'LIKE', '%'.$buscador.'%')
                ->orWhere('e.nombre_equipo', 'LIKE', '%'.$buscador.'%')
                ->orWhere('e.modelo', 'LIKE', '%'.$buscador.'%');
            })
            ->count();
            $query = DB::table('inv_equipo as e')
            ->join('inv_tipo_equipo as t', 't.id', '=', 'e.id_tipo_equipo')
            ->join('inv_marcas as m', 'm.id', '=', 'e.id_marca')
            ->join('estatus as es', 'es.id', '=', 'e.id_estatus')
            ->leftJoin('inv_licencias as lo', 'lo.id', '=', 'e.id_licencia_office')
            ->leftJoin('inv_licencias as lw', 'lw.id', '=', 'e.id_licencia_windows')
            ->select('e.id', 't.tipo_equipo', 'm.marca', 'e.id_tipo_equipo', 'e.id_marca',
            'e.descripcion', 'e.numero_serie', 'e.id_estatus', 'es.estatus',
            'e.id_licencia_office', 'e.id_licencia_windows', 'e.modelo', 'e.nombre_equipo', 'e.ram', 'e.procesador',
            'lo.licencia as licencia_office', 'lw.licencia as licencia_windows')
            //->where('e.id_estatus', $id_estatus_disponible)
            ->where('e.activo', true)
            ->where(function ($query) use ($buscador) {
                $query->orWhere('e.descripcion', 'LIKE', '%'.$buscador.'%')
                ->orWhere('e.numero_serie', 'LIKE', '%'.$buscador.'%')
                ->orWhere('t.tipo_equipo', 'LIKE', '%'.$buscador.'%')
                ->orWhere('e.nombre_equipo', 'LIKE', '%'.$buscador.'%')
                ->orWhere('e.modelo', 'LIKE', '%'.$buscador.'%');
            })
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
                ->leftJoin('inv_licencias as lo', 'lo.id', '=', 'e.id_licencia_office')
                ->leftJoin('inv_licencias as lw', 'lw.id', '=', 'e.id_licencia_windows')
                ->select('e.id', 't.tipo_equipo', 'm.marca', 'e.id_tipo_equipo', 'e.id_marca',
                'e.descripcion', 'e.numero_serie', 'e.id_estatus', 'es.estatus',
                'e.id_licencia_office', 'e.id_licencia_windows', 'e.modelo', 'e.nombre_equipo', 'e.ram', 'e.procesador',
                'lo.licencia as licencia_office', 'lw.licencia as licencia_windows')
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
    public function getHelpers(){
        $id_asignado = $this->getEstatusMix("ASIGNADO");
        $id_disponible = $this->getEstatusMix("DISPONIBLE");

        $tipo_equipos = DB::table('inv_tipo_equipo')
                        ->select('id as id_tipo_equipo', 'tipo_equipo')
                        ->where('activo', 1)
                        ->where('tipo', '<>', "COMPUTADORA")
                        ->get();
        
        $equipos_computadora= DB::table('inv_tipo_equipo')
                        ->select('id as id_tipo_equipo', 'tipo_equipo')
                        ->where('activo', 1)
                        ->where('tipo', "COMPUTADORA")
                        ->get();
        $lOffice = DB::table('inv_licencias')
                        ->select('id as id_licencia_office', 'licencia')
                        ->where('activo', 1)
                        ->where('tipo', "OFFICE")
                        ->where('id_estatus', $id_disponible)
                        ->get();

        $lWindows = DB::table('inv_licencias')
                        ->select('id as id_licencia_windows', 'licencia')
                        ->where('activo', 1)
                        ->where('tipo', "WINDOWS")
                        ->where('id_estatus', $id_disponible)
                        ->get();
        $marcas = DB::table('inv_marcas')
                    ->select('id as id_marca', 'marca')
                    ->where('activo', 1)
                    ->get();
        return response()->json([
            'tipo_equipos' => $tipo_equipos,
            'equipos_computadora' => $equipos_computadora,
            'marcas' => $marcas,
            'lOffice' => $lOffice,
            'lWindows' => $lWindows,
            'ok' => true
        ], 200);
    }
    public function getLicenciasDisponibles($id_licencia_o, $id_licencia_w){
        $id_asignado = $this->getEstatusMix("ASIGNADO");
        $id_disponible = $this->getEstatusMix("DISPONIBLE");
        $lOffice = DB::table('inv_licencias')
                        ->select('id as id_licencia_office', 'licencia')
                        ->where('activo', 1)
                        ->where('tipo', "OFFICE")
                        ->where('id_estatus', $id_disponible)
                        ->orWhere('id', $id_licencia_o)
                        ->get();

        $lWindows = DB::table('inv_licencias')
                        ->select('id as id_licencia_windows', 'licencia')
                        ->where('activo', 1)
                        ->where('tipo', "WINDOWS")
                        ->where('id_estatus', $id_disponible)
                        ->orWhere('id', $id_licencia_w)
                        ->get();
        
        return response()->json([
            'lOffice' => $lOffice,
            'lWindows' => $lWindows,
            'ok' => true
        ], 200);
    }
}