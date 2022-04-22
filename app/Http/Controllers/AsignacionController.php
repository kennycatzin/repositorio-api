<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AsignacionController extends Controller
{
    public function storeConfiguracion(Request $request){
        try {
            $json = json_encode($request->input());
            $ojso = json_decode($json, true);
            $usuario = $request->get('usuario');
            $equipos = $ojso["equipos"];
            $id_estatus = $this->getEstatusMix("ACTIVO");
            $id_estatus_disponible = $this->getEstatusMix("ASIGNADO");
            $tipo = $request->get('tipo');
            $id_usuario = 0;
            if($tipo == "SUCURSAL"){
                $sucursal = DB::table('inv_sucursal')
                ->select('id_encargado')
                ->where('id', $request->get('id_funcion'))
                ->where('activo', 1)
                ->first();
                $id_usuario = $sucursal->id_encargado;
            }else{
                $id_usuario = $request->get('id_funcion');
            }

            foreach($equipos as $equipo){
                $temp_data = DB::table('inv_asignacion')
                            ->select('*')
                            ->where('id_equipo', $equipo["id_equipo"])
                            ->where('id_usuario', $id_usuario)
                            ->count();
                $val_equipo = DB::table('inv_asignacion')
                            ->select('*')
                            ->where('id_equipo', $equipo["id_equipo"])
                            ->where('id_usuario', $id_usuario)
                            ->first();
                if($temp_data == 0){
                    DB::insert('insert into inv_asignacion      
                    (id_usuario, id_equipo, id_estatus, activo, fecha_creacion, fecha_modificacion, 
                    usuario_creacion, usuario_modificacion) values (?, ?, ?, ?, ?, ?, ?, ?)', 
                    [$id_usuario, $equipo["id_equipo"], $id_estatus, 1, $this->fechaActual(),
                    $this->fechaActual(), $usuario, $usuario]);
                }else{
                    if($val_equipo->activo == 0){
                        DB::update('update inv_asignacion 
                        set id_estatus = ?, activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
                        where id_equipo = ? and id_usuario = ?', 
                        [$id_estatus, 1, $this->fechaActual(), $usuario, $equipo["id_equipo"], $id_usuario]);
                    }
                }
                DB::update('update inv_equipo 
                set id_estatus = ?, activo = 1, fecha_modificacion = ?, usuario_modificacion = ? 
                where id = ?', 
                [$id_estatus_disponible, $this->fechaActual(), $usuario, $equipo["id_equipo"]]);
            }
            return $this->crearRespuesta(1, null, 'Se ha configurado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function getAsignaciones($index){
        try {
            $id_estatus_baja = $this->getEstatusMix("BAJA");
            $totales = DB::table('inv_asignacion as a')
                        ->join('users as u', 'u.id', '=', 'a.id_usuario')
                        ->join('estatus as e', 'e.id', '=', 'a.id_estatus')
                        ->leftJoin('inv_sucursal as s', 's.id_encargado', '=', 'a.id_usuario')
                        ->select('a.id', 'u.name', 'e.estatus', DB::raw('COUNT(a.id_equipo) as equipos'), 's.sucursal')
                        ->where('a.activo', 1)
                        ->where('a.id_estatus', '<>', $id_estatus_baja)
                        ->groupBy('a.id_usuario')                  
                        ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $data = DB::table('inv_asignacion as a')
                    ->join('users as u', 'u.id', '=', 'a.id_usuario')
                    ->join('estatus as e', 'e.id', '=', 'a.id_estatus')
                    ->leftJoin('inv_sucursal as s', 's.id_encargado', '=', 'a.id_usuario')
                    ->select('a.id', 'u.name', 'e.estatus', DB::raw('COUNT(a.id_equipo) as equipos'), 's.sucursal')
                    ->where('a.activo', 1)
                    ->where('a.id_estatus', '<>', $id_estatus_baja)
                    ->groupBy('a.id_usuario')
                    ->skip($index)
                    ->take(8)
                    ->get();
            return response()->json([
                'data' => $data,
                'totales' => $totales,
                'paginas' => $resultado,
                'ok' => true
            ], 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function setAsignacionEliminar(Request $request){
        try {
            $usuario = $request->get('usuario');
            $id_asignacion = $request->get('id_asignacion');
            $id_estatus_baja = $this->getEstatusMix("BAJA");
            $id_estatus_disponible = $this->getEstatusMix("DISPONIBLE");
            $id_equipo =  $request->get("id_equipo");

            DB::update('update inv_asignacion 
            set id_estatus = ?, activo = 0, fecha_modificacion = ?, usuario_modificacion = ? 
            where id = ?', 
            [$id_estatus_baja, $this->fechaActual(), $usuario, $id_asignacion]);

            DB::update('update inv_equipo 
            set id_estatus = ?, activo = 1, fecha_modificacion = ?, usuario_modificacion = ? 
            where id = ?', 
            [$id_estatus_disponible, $this->fechaActual(), $usuario, $id_equipo]);


            return $this->crearRespuesta(1, null, 'Se ha eliminado de la configuración.', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function eliminarAsignacionEstatus(Request $request){
        try {
            // $usuario = $request->get('usuario');
            // $id_asignacion = $request->get('id_asignacion');

            // DB::update('update inv_asignacion 
            // set id_estatus = ?, activo = 0, fecha_modificacion = ?, usuario_modificacion = ? 
            // where id = ?', 
            // [$this->fechaActual(), $usuario, $id_asignacion]);
            // return $this->crearRespuesta(1, null, 'Se ha eliminado de la configuración.', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function storeSolicitud(Request $request){
        try {
            $json = json_encode($request->input());
            $ojso = json_decode($json, true);
            $id_usuario = $request->get('id_usuario');
            $usuario = $request->get('usuario');
            $equipos = $ojso["equipos"];
            $cantidad = $ojso["cantidad"];
            $nombre_traslado = $ojso["nombre_traslado"];
            $departamento_traslado = $ojso["departamento_traslado"];
            $id_estatus_activo = $this->getEstatusMix("ACTIVO");
            $id_estatus_traslado = $this->getEstatusMix("TRASLADO");

            DB::insert('insert into inv_enc_solicitud 
            (id_estatus, folio, nombre_traslado, departamento_traslado, numero_equipos, fecha, activo, fecha_creacion,
            fecha_modificacion, usuario_creacion, usuario_modificacion) 
            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id_estatus_activo, 'folio', $nombre_traslado, $departamento_traslado, $cantidad, 
            $this->fechaCruda(), 1, $this->fechaActual(), $this->fechaActual(), $usuario, $usuario]);
            $id_encabezado = DB::getPdo()->lastInsertId();
            foreach($equipos as $equipo){
                DB::insert('insert into inv_det_solicitud
                (id_enc_solicitud, id_asignacion, id_estatus, observaciones, obs_tecnico,
                activo, fecha_creacion, fecha_modificacion, usuario_creacion, usuario_modificacion) 
                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                [$id_encabezado, $equipo["id_asignacion"], $id_estatus_activo, 1, 
                $this->fechaActual(), $this->fechaActual(), $usuario, $usuario]);

                DB::update('update inv_asignacion 
                set id_estatus = ?, activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
                where id_equipo = ? and id_usuario = ?', 
                [$id_estatus_traslado, 1, $this->fechaActual(), $usuario, $equipo["id_equipo"], $id_usuario]);
            }
            return $this->crearRespuesta(1, null, 'Se ha configurado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function atenderDetalleSolicitud(Request $request){
        try {
            $id_estatus = $request->get('id_estatus');
            $id_detalle = $request->get('id_detalle');
            $observacion = $request->get('observacion');

            $id_estatus_activo = $this->getEstatusMix("ACTIVO");
            $id_estatus_atendido = $this->getEstatusMix("ATENDIDO");
            $usuario = $request->get('usuario');
            $estatus = $this->getEstatusByID($id_estatus); 
            $bandera = 0;

            DB::update('update inv_det_solicitud 
            set id_estatus = ?, obs_tecnico = ?, fecha_modificacion = ?, usuario_modificacion = ?
            where id = ?', 
            [$id_estatus, $observacion, $this->fechaActual(), $usuario, $id_detalle]);

            $id_asignacion = DB::table('inv_det_solicitud as ds')
                            ->join('inv_asignacion as a', 'a.id', '=', 'ds.id_asignacion')
                            ->join('inv_equipo as e', 'e.id', '=', 'a.id_equipo')
                            ->select('ds.id_asignacion', 'a.id_equipo')
                            ->where('ds.id', $id_detalle)
                            ->first();
            if($estatus == "BAJA"){
                $id_estatus_baja = $this->getEstatusMix("BAJA");

                DB::update('update inv_asignacion 
                set id_estatus = ?, activo = 0, fecha_modificacion = ?, usuario_modificacion = ? 
                where id = ?', 
                [$id_estatus_baja, $this->fechaActual(), $usuario, $id_asignacion->id_asignacion]);



                DB::update('update inv_equipo 
                set id_estatus = ?, activo = 1, fecha_modificacion = ?, usuario_modificacion = ? 
                where id = ?', 
                [$id_estatus_baja, $this->fechaActual(), $usuario,  $id_asignacion->id_equipo]);
                
            }else if($estatus == "TRASPASO"){
                $id_estatus_baja = $this->getEstatusMix("BAJA");
                $id_estatus_disponible = $this->getEstatusMix("DISPONIBLE");
                DB::update('update inv_asignacion 
                set id_estatus = ?, activo = 0, fecha_modificacion = ?, usuario_modificacion = ? 
                where id = ?', 
                [$id_estatus_baja, $this->fechaActual(), $usuario, $id_asignacion->id_asignacion]);

                DB::update('update inv_equipo 
                set id_estatus = ?, activo = 1, fecha_modificacion = ?, usuario_modificacion = ? 
                where id = ?', 
                [$id_estatus_disponible, $this->fechaActual(), $usuario,  $id_asignacion->id_equipo]);

            }else if($estatus == "DEVOLUCIÓN"){
                DB::update('update inv_asignacion 
                set id_estatus = ?, activo = 1, fecha_modificacion = ?, usuario_modificacion = ? 
                where id = ?', 
                [$id_estatus_activo, $this->fechaActual(), $usuario, $id_asignacion->id_asignacion]);
            }

            $encabezado = DB::table('inv_det_solicitud')
                        ->select('id_enc_solicitud')
                        ->where('id', $id_detalle)
                        ->first();
            $activos = DB::table('inv_det_solicitud')
                        ->select('*')
                        ->where('id_enc_solicitud', $encabezado->id_enc_solicitud)
                        ->where('id_estatus',  $id_estatus_activo)
                        ->count();
            if($activos == 0){
                $bandera = 1;
               
                DB::update('update inv_enc_solicitud 
                set id_estatus = ?, fecha_modificacion = ?, usuario_modificacion = ?
                where id = ?', 
                [$id_estatus_atendido, $this->fechaActual(), $usuario, $encabezado->id_enc_solicitud]);
                $this->imprimir($encabezado->id_enc_solicitud);
            }
            return $this->crearRespuesta(1, $bandera, 'Se ha creado la solicitud', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la solicitud '.$th->getMessage(), 300);
        }        
    }
    public function imprimir($id_solicitud){
        //try {
            $data = DB::table('inv_enc_solicitud as s')
                    ->join('inv_det_solicitud as ds', 's.id', '=', 'ds.id_enc_solicitud')
                    ->join('inv_asignacion as a', 'ds.id_asignacion', '=', 'a.id')
                    ->join('inv_equipo as eq', 'eq.id', '=', 'a.id_equipo')
                    ->join('inv_marcas as m', 'eq.id_marca', '=', 'm.id')
                    ->join('inv_tipo_equipo as te', 'te.id', '=', 'eq.id_tipo_equipo')
                    ->join('estatus as es', 'es.id', '=', 'ds.id_estatus')
                    ->join('estatus as ee', 'ee.id', '=', 'eq.id_estatus')
                    ->select('s.folio', 's.nombre_usuario', 's.nombre_traslado', 's.departamento_traslado', 's.observaciones',
                            'ds.id', 'ds.id_enc_solicitud', 'ds.observaciones', 'es.estatus as esta_detalle', 'ee.estatus as esta_equipo',
                            'm.marca', 'te.tipo_equipo', 'eq.numero_serie', 'eq.modelo', 'ds.obs_tecnico')
                    ->where('ds.activo', 1)
                    ->where('ds.id_enc_solicitud', $id_solicitud)
                    ->orderBy('id', 'ASC')                        
                    ->get(); 
                    $arrdata = json_decode(json_encode($data), true);
                    $data =  [
                        'quantity'      => '1' ,
                        'description'   => 'some ramdom text',
                        'price'   => '500',
                        'data'     => $arrdata
                    ];
                    // $data = json_decode(json_encode($data), true);

                    $pdf = PDF::loadView('plantilla_solicitud', compact('data'));
            // $data = array(
            //     'tipo_equipo' => 'leyenda'
            // );//json_decode(json_encode($data), true);   
            return $pdf->stream('invoice.pdf');
        // } catch (\Throwable $th) {
        //     return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        // }
        
   }
    public function busqueda(Request $request){
        try {
            $id_estatus_baja = $this->getEstatusMix("BAJA");
            $valor = $request['busqueda'];
            $query = Articulo::orWhere('descripcion', 'LIKE', '%'.$valor.'%')->get();
            $data = DB::table('inv_asignacion as a')
                    ->join('users as u', 'u.id', '=', 'a.id_usuario')
                    ->join('estatus as e', 'e.id', '=', 'a.id_estatus')
                    ->leftJoin('inv_sucursal as s', 's.id_encargado', '=', 'a.id_usuario')
                    ->select('a.id', 'u.name', 'e.estatus', DB::raw('COUNT(a.id_equipo) as equipos'), 's.sucursal')
                    ->where('a.activo', 1)
                    ->where('a.id_estatus', '<>', $id_estatus_baja)
                    ->groupBy('a.id_usuario')
                    ->orWhere('u.name', 'LIKE', '%'.$valor.'%')
                    ->get();
            return $this->crearRespuesta(1, $query, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getDetEquipos(Request $request){
        try {
            $id_estatus_activo = $this->getEstatusMix("ACTIVO");
            $tipo = $request->get('tipo');
            $id_funcion = 0;
            if($tipo == "SUCURSAL"){
                $sucursal = DB::table('inv_sucursal')
                ->select('id_encargado')
                ->where('id', $request->get('id_funcion'))
                ->where('activo', 1)
                ->first();
                $id_funcion = $sucursal->id_encargado;
            }else{
                $id_funcion = $request->get('id_funcion');
            }
            $data = DB::table('inv_asignacion as a')
            ->join('inv_equipo as e', 'e.id', '=', 'a.id_equipo')
            ->join('inv_tipo_equipo as t', 't.id', '=', 'e.id_tipo_equipo')
            ->join('inv_marcas as m', 'm.id', '=', 'e.id_marca')
            ->select('a.id as id_asignacion', 'a.id_equipo', 't.tipo_equipo', 'm.marca', 
            'e.descripcion', 'e.numero_serie')
            ->where('a.id_estatus', $id_estatus_activo)
            ->where('a.id_usuario', $id_funcion)
            ->where('a.activo', true)
            ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
}