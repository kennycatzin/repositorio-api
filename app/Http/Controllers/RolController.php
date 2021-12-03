<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Rol;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function storeRol(Request $request){
        try {
            $rol = new Rol;
            $rol->id_departamento = $request->get('id_departamento');
            $rol->rol = $request->get('rol');
            $rol->tipo = $request->get('tipo');
            $rol->descripcion = $request->get('descripcion');
            $rol->activo = true;
            $rol->timestamps = false;
            $rol->fecha_creacion = $this->fechaActual();
            $rol->fecha_modificacion = $this->fechaActual();
            $rol->usuario_creacion = $request->get('usuario');
            $rol->usuario_modificacion = $request->get('usuario');
            $rol->save();
            return $this->crearRespuesta(1, $rol, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateRol(Request $request, $id_rol){
        try {
            $rol = Rol::find($id_rol);
            $rol->id_departamento = $request->get('id_departamento');
            $rol->rol = $request->get('rol');
            $rol->tipo = $request->get('tipo');
            $rol->descripcion = $request->get('descripcion');
            $rol->activo = $request->get('activo');;
            $rol->timestamps = false;
            $rol->fecha_modificacion = $this->fechaActual();
            $rol->usuario_modificacion = $request->get('usuario');
            $rol->save();
            return $this->crearRespuesta(1, $rol, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getRoles(){
        try {
            $roles = Rol::where('activo', 1)
                        ->orderBy('rol', 'ASC')                        
                        ->get();
            return $this->crearRespuesta(1, $roles, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getRol($id_rol){
        try {
            $rol = Rol::find($id_rol);
            return $this->crearRespuesta(1, $rol, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function storeConfRolArchivo(Request $request){              
        try {
            $json = json_encode($request->input());
            $ojso = json_decode($json, true);
            $id_rol = $request->get('id_rol');
            $id_usuario = $request->get('usuario');
            $archivos = $ojso["archivos"];
            foreach($archivos as $archivo){
                $temp_data = DB::table('archivo_rol')
                            ->select('*')
                            ->where('id_archivo', $archivo["id_archivo"])
                            ->where('id_rol', $id_rol)
                            ->count();
                $val_archivo = DB::table('archivo_rol')
                            ->select('*')
                            ->where('id_archivo', $archivo["id_archivo"])
                            ->where('id_archivo', $archivo["id_archivo"])
                            ->first();
                if($temp_data == 0){
                    DB::insert('insert into archivo_rol      
                    (id_rol, id_archivo, activo, fecha_creacion, fecha_modificacion, 
                    usuario_creacion, usuario_modificacion) values (?, ?, ?, ?, ?, ?, ?)', 
                    [$id_rol, $archivo["id_archivo"], 1, $this->fechaActual(), 
                    $this->fechaActual(), $id_usuario, $id_usuario]);
                }else{
                    if($val_archivo->activo == 0){
                        DB::update('update archivo_rol 
                        set activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
                        where id_archivo = ? and id_rol = ?', 
                        [1, $this->fechaActual(), $id_usuario, $archivo["id_archivo"], $id_rol]);
                    }
                }
            }
            return $this->crearRespuesta(1, null, 'Configuración guardada correctamente', 300);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo almacenar la información '.$th->getMessage(). ' '.$th->getLine(), 300);
        }
    }
    public function eliminarArchivo(Request $request){
        try {
            $id_archivo_rol = $request->get('id_archivo_rol');
            $usuario =$request->get('usuario');
            DB::update('update archivo_rol 
            set activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
            where id = ?', 
            [0, $this->fechaActual(), $usuario, $id_archivo_rol]);
            return $this->crearRespuesta(1, null, 'Se ha eliminado el archivo del rol', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
}