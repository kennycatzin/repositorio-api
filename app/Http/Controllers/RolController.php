<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Rol;
use Illuminate\Support\Facades\DB;
use  App\Models\Subcategoria;
use Illuminate\Support\Facades\Mail;

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
            $rol->timestamps = false;
            $rol->fecha_modificacion = $this->fechaActual();
            $rol->usuario_modificacion = $request->get('usuario');
            $rol->save();
            return $this->crearRespuesta(1, $rol, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function bajaRol(Request $request, $id_rol){
        try {
            $rol = Rol::find($id_rol);
            $rol->activo = false;
            $rol->timestamps = false;
            $rol->fecha_modificacion = $this->fechaActual();
            $rol->usuario_modificacion = $request->get('usuario');
            $rol->save();
            return $this->crearRespuesta(1, $rol, 'Se ha eliminado la información', 201);
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
    public function getRolesAdmin(){
        try {
            $roles = DB::table('roles as r')
                    ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                    ->select('r.*', 'departamento')
                    ->where('r.activo', 1)
                    ->where('d.activo', 1)
                    ->get();
            
            // Rol::where('activo', 1)
            //             ->orderBy('rol', 'ASC')                        
            //             ->get();

            return $this->crearRespuesta(1, $roles, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getRolesPaginado($index){
        try {
            $totales = DB::table('roles as r')
                    ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                    ->select('r.*', 'departamento')
                    ->where('r.activo', 1)
                    ->where('d.activo', 1)                   
                    ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $roles = DB::table('roles as r')
                    ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                    ->select('r.*', 'departamento')
                    ->where('r.activo', 1)
                    ->where('d.activo', 1)
                    ->skip($index)
                    ->take(8)
                    ->get();
            return response()->json([
                'data' => $roles,
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
            $contador = DB::table('roles as r')
                        ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                        ->select('r.*', 'departamento')
                        ->where('r.activo', 1)
                        ->where('d.activo', 1)
                        ->orWhere('rol', 'LIKE', '%'.$valor.'%')
                        ->count();
            $query = DB::table('roles as r')
                        ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                        ->select('r.*', 'departamento')
                        ->orWhere('r.rol', 'LIKE', '%'.$valor.'%')
                        ->where('r.activo', 1)
                        ->where('d.activo', 1)
                        ->get();
            return $this->crearRespuesta(1, $query, $contador, 200);
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
                            ->where('id_rol', $id_rol)
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
                $temp_dataUs = DB::table('users as u')
                            ->join('archivo_usuario as au', 'u.id', '=', 'au.id_usuario')
                            ->select('u.*', 'au.*')
                            ->where('u.id_rol', $id_rol)
                            ->where('au.id_archivo', $archivo["id_archivo"])
                            ->count();    
                $val_archivoUS = DB::table('users as u')
                            ->join('archivo_usuario as au', 'u.id', '=', 'au.id_usuario')
                            ->select('au.id as mi_idusuario', 'u.*', 'au.id as kenny', 'au.activo', 'au.id_archivo')
                            ->where('u.id_rol', $id_rol)
                            ->where('au.id_archivo', $archivo["id_archivo"])
                            ->get();
                            // Estoy obteniendo el primero y no todos !!!!!!!!!!!!!!!!!!!!!111

                if($temp_dataUs == 0){
                    $usuarios = DB::table('users')
                                ->select('id')
                                ->where('id_rol', $id_rol)
                                ->get();
                    foreach($usuarios as $user){
                         DB::insert('insert into archivo_usuario      
                        (id_usuario, id_archivo, fecha_caducidad, vigente, tipo, activo, fecha_creacion, 
                        fecha_modificacion, usuario_creacion, usuario_modificacion, id_estatus, fecha_estatus) 
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                        [$user->id, $archivo["id_archivo"], $this->fechaActual(), 1, 1, 
                        1, $this->fechaActual(), $this->fechaActual(), $id_usuario, $id_usuario, 4, $this->fechaActual()]);
                    }                                    
                }else{
                   foreach($val_archivoUS as $listado){                            
                            DB::update('update archivo_usuario 
                            set activo = 1, fecha_modificacion = ?, usuario_modificacion = ?
                            where id_archivo = ? and id = ?', 
                            [$this->fechaActual(), $id_usuario, $archivo["id_archivo"], $listado->mi_idusuario]);
                   }                    
                }
            }
            return $this->crearRespuesta(1, null, 'Configuración guardada correctamente', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo almacenar la información '.$th->getMessage(). ' '.$th->getLine(), 300);
        }
    }
    public function storeConfArchivoRol(Request $request){              
        try {
            $json = json_encode($request->input());
            $ojso = json_decode($json, true);
            $id_archivo = $request->get('id_archivo');
            $id_usuario = $request->get('usuario');
            $archivos = $ojso["roles"];
            $listCorreos = array();

            foreach($archivos as $rol){
                $correo = DB::table('roles as r')
                        ->join('departamento as d', 'd.id', '=', 'r.id_departamento')
                        ->select('d.correo')
                        ->where('r.id', $rol["id_rol"])
                        ->first();
                array_push($listCorreos, $correo->correo);
                $temp_data = DB::table('archivo_rol')
                            ->select('*')
                            ->where('id_rol', $rol["id_rol"])
                            ->where('id_archivo', $id_archivo)
                            ->count();
                $val_archivo = DB::table('archivo_rol')
                            ->select('*')
                            ->where('id_rol', $rol["id_rol"])
                            ->first();
                if($temp_data == 0){
                    DB::insert('insert into archivo_rol      
                    (id_rol, id_archivo, activo, fecha_creacion, fecha_modificacion, 
                    usuario_creacion, usuario_modificacion) values (?, ?, ?, ?, ?, ?, ?)', 
                    [$rol["id_rol"], $id_archivo, 1, $this->fechaActual(), 
                    $this->fechaActual(), $id_usuario, $id_usuario]);
                }else{
                    if($val_archivo->activo == 0){
                        DB::update('update archivo_rol 
                        set activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
                        where id_rol = ? and id_archivo = ?', 
                        [1, $this->fechaActual(), $id_usuario, $rol["id_rol"], $id_archivo]);
                    }
                }
                $temp_dataUs = DB::table('users as u')
                            ->join('archivo_usuario as au', 'u.id', '=', 'au.id_usuario')
                            ->select('u.*', 'au.*')
                            ->where('u.id_rol', $rol["id_rol"])
                            ->where('au.id_archivo',  $id_archivo)
                            ->count();    
                $val_archivoUS = DB::table('users as u')
                            ->join('archivo_usuario as au', 'u.id', '=', 'au.id_usuario')
                            ->select('au.id as mi_idusuario', 'u.*', 'au.id as kenny', 'au.activo', 'au.id_archivo')
                            ->where('u.id_rol', $rol["id_rol"])
                            ->where('au.id_archivo',  $id_archivo)
                            ->get();

                if($temp_dataUs == 0){
                    //TODO: Enviar correos a los departamentos
                    $usuarios = DB::table('users')
                                ->select('id')
                                ->where('id_rol', $rol['id_rol'])
                                ->get();
                    foreach($usuarios as $user){
                         DB::insert('insert into archivo_usuario      
                        (id_usuario, id_archivo, fecha_caducidad, vigente, tipo, activo, fecha_creacion, 
                        fecha_modificacion, usuario_creacion, usuario_modificacion, id_estatus, fecha_estatus) 
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                        [$user->id, $id_archivo, $this->fechaActual(), 1, 1, 
                        1, $this->fechaActual(), $this->fechaActual(), $id_usuario, $id_usuario, 4, $this->fechaActual()]);
                    }                                    
                }else{
                   foreach($val_archivoUS as $listado){                            
                            DB::update('update archivo_usuario 
                            set activo = 1, fecha_modificacion = ?, usuario_modificacion = ?, id_estatus = ?
                            where id_archivo = ? and id = ?', 
                            [$this->fechaActual(), $id_usuario, 4, $id_archivo, $listado->mi_idusuario]);
                   }                    
                }
                
            }
            $configuracion = env('MAIL_ENVIAR', '');
            if($configuracion == 1){
                $info = DB::table('archivo')
                ->select('nombre as observaciones', DB::raw("CONCAT(nombre, ' ',descripcion) AS archivo"))
                ->where('id', $id_archivo)
                ->first();
                $data = array(
                            'titulo' => "Actualización de documento",
                            'archivo' => $info->archivo,
                            'observaciones' =>  "Tiene una nueva versión y se encuentra disponible en el Repositorio."
                        );
                Mail::send('plantilla_archivo', $data, function($message) use ($listCorreos) {
                    $message->to($listCorreos)
                            ->subject('Actualización de documento');
                    $message->from(env('MAIL_USERNAME'),'Repositorio');
                });
            }
            
            return $this->crearRespuesta(1, null, 'Configuración guardada correctamente', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo almacenar la información '.$th->getMessage(). ' '.$th->getLine(), 300);
        }
    }
    public function eliminarArchivo(Request $request){
        try {
            $id_archivo_rol = $request->get('id_archivo_rol');
            $id_archivo = $request->get('id_archivo');
            $usuario =$request->get('usuario');
            DB::update('update archivo_rol 
            set activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
            where id = ?', 
            [0, $this->fechaActual(), $usuario, $id_archivo_rol]);

            DB::update('update archivo_usuario set activo = false,
            usuario_modificacion = ?, fecha_modificacion = ?
            where id_archivo = ?', 
            [$usuario, $this->fechaActual(), $id_archivo]);

            return $this->crearRespuesta(1, null, 'Se ha eliminado el archivo del rol', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
    public function getCatSubArchivosAdmin($id_rol){
        try {
            $categorias = array();
            $subcategorias = array();
            $arregloCat = array();
            $arregloSub = array();
            $data = array();
            $archivo_rol = DB::table('archivo_rol as ar')
                                    ->join('archivo as a', 'a.id', '=', 'ar.id_archivo')
                                    ->join('subcategoria as s', 's.id', '=', 'a.id_subcategoria')
                                    ->join('categoria as c', 'c.id', '=', 's.id_categoria')
                                    ->join('detalle_archivo as da', 'a.id', '=', 'da.id_archivo')
                                    ->select('c.id as id_categoria', 
                                            'c.titulo as categoria',
                                            'c.descripcion as desc_categoria',  
                                            's.id as id_subcategoria',
                                            's.titulo as subcategoria', 
                                            's.descripcion as desc_subcategoria', 
                                            'c.imagen', 
                                            'a.nombre',
                                            'a.descripcion',
                                            'da.url')
                                    ->where('ar.activo', 1)
                                    ->where('a.activo', 1)
                                    ->where('da.actual', 1)
                                    ->where('ar.id_rol', $id_rol)
                                    ->get();
            foreach($archivo_rol as $lista){
                if(!in_array($lista->id_categoria, $categorias, true)){
                    array_push($categorias, array(
                                                    "id" => $lista->id_categoria,
                                                    "titulo" => $lista->categoria,
                                                    "descripcion" => $lista->desc_categoria
                                                ));
                    array_push($arregloCat, $lista->id_categoria);
                }
                if(!in_array($lista->id_subcategoria, $subcategorias, true)){
                    array_push($subcategorias, array(
                                                    "id" => $lista->id_subcategoria,
                                                    "titulo" => $lista->subcategoria,
                                                    "descripcion" => $lista->desc_subcategoria
                                                ));
                    array_push($arregloSub, $lista->id_subcategoria);
                }
            }
            $misCategorias = DB::table('categoria')
                            ->whereIn('id', $arregloCat)
                            ->get();
            $contador = 0;
            $contadorSub = 0;
            $contadorArchivo = 0;
            foreach($misCategorias as $categoria){              
                $misSubcategorias = Subcategoria::where('id_categoria', $categoria->id)
                                            ->whereIn('id', $arregloSub)
                                            ->where('activo', 1)
                                            ->orderBy('orden', 'ASC')                        
                                            ->get();
                $contadorSub = 0;
                foreach($misSubcategorias as $subcat){
                    $misArchivos = DB::table('archivo_rol as ar')
                                    ->join('archivo as a', 'ar.id_archivo', '=', 'a.id' )
                                    ->join('subcategoria as s', 's.id', '=', 'a.id_subcategoria')
                                    ->select('ar.id_archivo', 'a.descripcion', 's.titulo', 'a.nombre')
                                    ->where('ar.activo', 1)
                                    ->where('a.activo', 1)
                                    ->where('ar.id_rol', $id_rol)
                                    ->where('a.id_subcategoria', $subcat->id)
                                    ->orderBy('a.consecutivo', 'DESC')
                                    ->get();
                    if($contadorSub == 0){
                        $misSubcategorias=json_decode(json_encode($misSubcategorias), true);
                    }
                    $misSubcategorias[$contadorSub]+=["archivos"=>$misArchivos];
                    $contadorSub ++; 
                }
                if($contador == 0){
                    $misCategorias=json_decode(json_encode($misCategorias), true);
                }
                $misCategorias[$contador]+=["clave"=>$this->randw()];
                $misCategorias[$contador]+=["subcategorias"=>$misSubcategorias];
                $contador ++;
            }
            return $this->crearRespuesta(1, $misCategorias, 'info', 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
    public function getRolesByDepartamento($id_departamento){
        try {
            $data = DB::table('roles as r')
                        ->join('departamento as d', 'd.id', '=', 'r.id_departamento')
                        ->select('r.id as id_rol', 'r.rol', 'r.descripcion', 'd.departamento')
                        ->where('r.activo', 1)
                        ->where('r.id_departamento', $id_departamento)
                        ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
}