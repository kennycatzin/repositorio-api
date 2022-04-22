<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use  App\Models\Estatus;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SolicitudController extends Controller
{
    public function getEquipos($id_usuario){
        try {
            $id_estatus_activo = $this->getEstatusMix("ACTIVO");
            $data = DB::table('inv_asignacion as a')
            ->join('inv_equipo as e', 'e.id', '=', 'a.id_equipo')
            ->join('inv_tipo_equipo as te', 'te.id', '=', 'e.id_tipo_equipo')
            ->join('estatus as es', 'es.id', '=', 'a.id_Estatus')
            ->join('inv_marcas as m', 'm.id', '=', 'e.id_marca')
            ->select('a.id as id_asignacion', 'te.tipo_equipo', 'e.numero_serie', 'e.modelo',
                    'es.estatus', 'm.marca')
            ->where('a.activo', 1)
            ->where('a.id_usuario', $id_usuario)
            ->where('a.id_estatus', $id_estatus_activo)
            ->orderBy('te.tipo_equipo', 'ASC')
            ->get();
            return $this->crearRespuesta(1, $data, 'Info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function getSolicitudes($tipo){
        try {
            $id_estatus_activo = $this->getEstatusMix("ACTIVO");
            $id_estatus_general;
            $contador = 0;
            if($tipo == 1){
                $id_estatus_general = $this->getEstatusMix("ACTIVO");
            }else{
                $id_estatus_general = $this->getEstatusMix("ATENDIDO");
            }
            $data = DB::table('inv_enc_solicitud as s')
                    ->join('estatus as e', 'e.id', '=', 's.id_estatus')
                    ->select('s.id', 's.nombre_usuario', 's.nombre_traslado', 's.tipo', 's.observaciones',
                            's.departamento_traslado', 's.numero_equipos', 's.fecha', 'e.estatus')
                    ->where('s.activo', 1)
                    ->where('s.id_estatus', $id_estatus_general)
                    ->get();

            foreach($data as $soli){              
                $equipos = DB::table('inv_det_solicitud as ds')
                            ->join('inv_asignacion as a', 'ds.id_asignacion', '=', 'a.id')
                            ->join('inv_equipo as eq', 'eq.id', '=', 'a.id_equipo')
                            ->join('inv_marcas as m', 'eq.id_marca', '=', 'm.id')
                            ->join('inv_tipo_equipo as te', 'te.id', '=', 'eq.id_tipo_equipo')
                            ->join('estatus as es', 'es.id', '=', 'ds.id_estatus')
                            ->join('estatus as ee', 'ee.id', '=', 'eq.id_estatus')
                            ->select('ds.id', 'ds.id_enc_solicitud', 'ds.observaciones', 'es.estatus as esta_detalle', 'ee.estatus as esta_equipo',
                                    'm.marca', 'te.tipo_equipo', 'eq.numero_serie', 'eq.modelo', 'ds.obs_tecnico')
                            ->where('ds.activo', 1)
                            ->where('ds.id_enc_solicitud', $soli->id)
                            ->orderBy('id', 'ASC')                        
                            ->get();                               
                            $currentDate = Carbon::createFromFormat('Y-m-d', $this->fechaCruda());
                            $shippingDate = Carbon::createFromFormat('Y-m-d', "$soli->fecha");
                            $diferencia_en_dias = $currentDate->diffInDays($shippingDate);
                            $soli->dias = $diferencia_en_dias;
                if($contador == 0){
                    $data=json_decode(json_encode($data), true);
                }
                $data[$contador]+=["equipo"=>$equipos];
                $contador ++;
            }       
            return $this->crearRespuesta(1, $data, 'Info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function getListaEstatusAgregar(Request $request){
        try {
            $baja = Estatus::where('activo', 1)
                    ->where('estatus', 'BAJA')                        
                    ->count();
            if($baja == 0){
                $estatus = new Estatus;
                $estatus->estatus = 'BAJA';
                $estatus->descripcion = 'BAJA';
                $estatus->tipo = 2;
                $estatus->activo = true;
                $estatus->timestamps = false;
                $estatus->fecha_creacion = $this->fechaActual();
                $estatus->fecha_modificacion = $this->fechaActual();
                $estatus->usuario_creacion = 0;
                $estatus->usuario_modificacion = 0;
                $estatus->save();
            }
            $traspaso = Estatus::where('activo', 1)
                    ->where('estatus', 'TRASPASO')                        
                    ->count();
            if($traspaso == 0){
                $estatus = new Estatus;
                $estatus->estatus = 'TRASPASO';
                $estatus->descripcion = 'TRASPASO';
                $estatus->tipo = 2;
                $estatus->activo = true;
                $estatus->timestamps = false;
                $estatus->fecha_creacion = $this->fechaActual();
                $estatus->fecha_modificacion = $this->fechaActual();
                $estatus->usuario_creacion = 0;
                $estatus->usuario_modificacion = 0;
                $estatus->save();
            }

            $devolucion = Estatus::where('activo', 1)
                    ->where('estatus', 'DEVOLUCIÓN')                        
                    ->count();
            if($devolucion == 0){
                $estatus = new Estatus;
                $estatus->estatus = 'DEVOLUCIÓN';
                $estatus->descripcion = 'DEVOLUCIÓN';
                $estatus->tipo = 2;
                $estatus->activo = true;
                $estatus->timestamps = false;
                $estatus->fecha_creacion = $this->fechaActual();
                $estatus->fecha_modificacion = $this->fechaActual();
                $estatus->usuario_creacion = 0;
                $estatus->usuario_modificacion = 0;
                $estatus->save();
            }
            $listado = Estatus::where('activo', 1)
                    ->whereIn('estatus', ['TRASPASO', 'BAJA', 'DEVOLUCIÓN', 'NO OBTENIDO'])                        
                    ->get();
            return $this->crearRespuesta(1, $listado, 'Info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function storeSolicitud(Request $request){
        try {
            $json = json_encode($request->input());
            $ojso = json_decode($json, true);
            $usuario = $request->get('usuario');
            $equipos = $ojso["equipos"];
            $id_estatus_activo = $this->getEstatusMix("ACTIVO");
            $id_estatus_revision = $this->getEstatusMix("REVISION");
            $tipo = $request->get('tipo');
            $id_usuario = 0;
            
            DB::insert('insert into inv_enc_solicitud 
                        (id_estatus, tipo, folio, nombre_usuario, nombre_traslado, departamento_traslado, 
                        numero_equipos, fecha, observaciones, activo, fecha_creacion, fecha_modificacion, 
                        usuario_creacion, usuario_modificacion) 
                        values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
                        [$id_estatus_activo, $request->get('tipo_solicitud'), '123456', $request->get('nombre_usuario'), $request->get('trasladista'), 
                        $request->get('departamento'), 10, $this->fechaCruda(), $request->get('observaciones'), 
                        1, $this->fechaActual(), $this->fechaActual(), $usuario, $usuario]);
           
            $last_instert = DB::getPdo()->lastInsertId();
            foreach($equipos as $equipo){
                DB::insert('insert into inv_det_solicitud 
                            (id_enc_solicitud, id_asignacion, id_estatus, observaciones, obs_tecnico, activo, fecha_creacion, 
                            fecha_modificacion, usuario_creacion, usuario_modificacion) 
                            values (?,?,?,?,?,?,?,?,?,?)', 
                            [$last_instert, $equipo["id_asignacion"], $id_estatus_activo, $equipo["observacion"], 
                            "",1, $this->fechaActual(), $this->fechaActual(), $usuario, $usuario]);

                DB::update('update inv_asignacion 
                            set id_estatus = ?, fecha_modificacion = ?, usuario_modificacion = ?
                            where id = ?', 
                            [$id_estatus_revision, $this->fechaActual(), $usuario, $equipo["id_asignacion"]]);
            }
            return $this->crearRespuesta(1, null, 'Se ha configurado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
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
}