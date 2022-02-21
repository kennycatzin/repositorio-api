<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    protected function respondWithToken($token, $data)
    {
        return response()->json([
            'ok' => true,
            'token' => $token,
            'usuario' => $data,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
    protected function crearRespuesta($tipo, $data, $mensaje, $codigo){
        if($tipo == 1){
            return response()->json([
                'data' => $data,
                'mensaje' => $mensaje,
                'ok' => true
            ], $codigo);
        }elseif($tipo == 0){
            return response()->json([
                'data' => $data,
                'mensaje' => $mensaje,
                'ok' => false
            ], $codigo);
        }
    }
    protected function fechaActual(){
        $ldate = date('Y-m-d H:i:s');
        return $ldate;
    }
    protected function fechaCruda(){
        $ldate = date('d-m-Y');
        return $ldate;
    }
    public function asignaArchivosUsuario($id_usuario, $id_rol, $usuario){
        DB::update('update archivo_usuario set activo = ?
                    where id_usuario = ?', 
                        [0, $id_usuario]);
        $archivos = DB::table('archivo_rol')
                    ->select('id_archivo', 'activo')
                    ->where('id_rol', $id_rol)
                    ->where('activo', 1)
                    ->get();
        $count_archivos = DB::table('archivo_rol')
                    ->select('*')
                    ->where('id_rol', $id_rol)
                    ->where('activo', 1)
                    ->count();
        if($count_archivos > 0){
            foreach($archivos as $archivo){
                $count_val_archivo = DB::table('archivo_usuario')
                                ->select('*')
                                ->where('id_usuario', $id_usuario)
                                ->where('id_archivo', $archivo->id_archivo)
                                ->count();
                $val_archivo = DB::table('archivo_usuario')
                                ->select('*')
                                ->where('id_usuario', $id_usuario)
                                ->where('id_archivo', $archivo->id_archivo)
                                ->first();
                if($count_val_archivo == 0){
                    $id_estatus = 4;
                    DB::insert('insert into archivo_usuario 
                    (id_usuario, id_archivo, fecha_caducidad, vigente, tipo, activo, 
                    usuario_creacion, usuario_modificacion, fecha_creacion, 
                    fecha_modificacion, id_estatus, fecha_estatus) 
                    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    [$id_usuario, $archivo->id_archivo, $this->fechaActual(), 1, 1, 1, $usuario, $usuario, 
                    $this->fechaActual(), $this->fechaActual(), $id_estatus, $this->fechaActual()]);
                }else{
                    if($val_archivo->activo == 0){
                        DB::update('update archivo_usuario set tipo = ?, vigente = ?,
                        activo = ?, usuario_modificacion = ?, fecha_modificacion = ? 
                        where id_archivo = ? and id_usuario = ?', 
                        [1, 1, 1, $usuario, $this->fechaActual(), $archivo->id_archivo, $id_usuario]);
                    }
                }
            }
            return true;
        }else{
            return false;
        }
    }
    public function randw($length=10){
        return substr(str_shuffle("qwertyuiopasdfghjklzxcvbnm"),0,$length);
    }
}
