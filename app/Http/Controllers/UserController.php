<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use  App\Models\Subcategoria;
use App\Models\Archivo;
class UserController extends Controller
{
     /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function profile()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

    /**
     * Get all User.
     *
     * @return Response
     */
    public function allUsers()
    {
         return response()->json(['users' =>  User::all()], 200);
    }

    /**
     * Get one user.
     *
     * @return Response
     */
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!'], 404);
        }
    }

    public function asignarRolUsuario(Request $request){
        try {
            $id_usuario = $request->get('id_usuario');
            $tipo = $request->get('tipo');
            $id_rol = $request->get('id_rol');
            $usuario = $request->get('usuario');
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
                            DB::update('update archivo_usuario 
                            set tipo = ?, vigente = ?, activo = ?, usuario_modificacion = ?,
                            fecha_modificacion = ? where id_archivo = ? and id_usuario = ?', 
                            [1, 1, 1, $usuario, $this->fechaActual(), $archivo->id_archivo, $id_usuario]);
                        }
                    }
                }
                DB::update('update users set activo = 1, tipo = ?, id_rol = ? where id = ?', [ $tipo, $id_rol, $id_usuario]);
                return $this->crearRespuesta(1, null, 'Se ha configurado el perfil', 201);
            }else{
                return $this->crearRespuesta(0, null, 'No existe una configuración para este perfil', 201);
            }
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
    public function eliminarArchivo(Request $request){
        try {
            $id_archivo_usuario = $request->get('id_archivo_usuario');
            $usuario =$request->get('usuario');
            DB::update('update archivo_usuario 
            set activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
            where id = ?', 
            [0, $this->fechaActual(), $usuario, $id_archivo_usuario]);
            return $this->crearRespuesta(1, null, 'Se ha eliminado el archivo del perfil', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
    public function asignarArchivoUsuarioTemporal(Request $request){
        try {
            $id_usuario = $request->get('id_usuario');
            $id_archivo = $request->get('id_archivo');
            $fecha_caducidad = $request->get('fecha_caducidad');
            $usuario = $request->get('usuario');
            if(true){
                $count_val_archivo = DB::table('archivo_usuario')
                                    ->select('*')
                                    ->where('id_usuario', $id_usuario)
                                    ->where('id_archivo', $id_archivo)
                                    ->count();
                $val_archivo = DB::table('archivo_usuario')
                                    ->select('*')
                                    ->where('id_usuario', $id_usuario)
                                    ->where('id_archivo', $id_archivo)
                                    ->first();
                if($count_val_archivo == 0){
                    DB::insert('insert into archivo_usuario 
                    (id_usuario, id_archivo, fecha_caducidad, vigente, tipo, activo, 
                    usuario_creacion, usuario_modificacion, fecha_creacion, fecha_modificacion) 
                    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                    [$id_usuario, $id_archivo, $fecha_caducidad, 1, 2, 1, $usuario, $usuario, 
                    $this->fechaActual(), $this->fechaActual()]);
                }else{
                    if($val_archivo->activo == 0){
                        DB::update('update archivo_usuario 
                        set tipo = ?, vigente = ?, fecha_caducidad = ?, activo = ?, usuario_modificacion = ?,
                        fecha_modificacion = ? where id_archivo = ? and id_usuario = ?', 
                        [2, 1, $fecha_caducidad, 1, $usuario, $this->fechaActual(), $id_archivo, $id_usuario]);
                    }
                }
            }else{
                return $this->crearRespuesta(0, null, 'La fecha es muy antígua', 300);
            }
            return $this->crearRespuesta(1, null, 'Transacción completa', 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
    public function getCatSubCategorias(Request $request){
        try {
            $tipo = $request->get('tipo');
            $id_usuario = $request->get('id_usuario');
            $categorias = array();
            $subcategorias = array();
            $data = array();
            $arregloCat = array();
            $arregloSub = array();
            $list_archivos = DB::table('archivo_usuario as au')
                                    ->join('archivo as a', 'a.id', '=', 'au.id_archivo')
                                    ->join('subcategoria as s', 's.id', '=', 'a.id_subcategoria')
                                    ->join('categoria as c', 'c.id', '=', 's.id_categoria')
                                    ->join('detalle_archivo as da', 'a.id', '=', 'da.id_archivo')
                                    ->select('c.id as id_categoria', 
                                            'c.titulo as categoria',
                                            'c.descripcion as desc_categoria',  
                                            's.id as id_subcategoria',
                                            's.titulo as subcategoria', 
                                            's.descripcion as desc_subcategoria',  
                                            'a.nombre',
                                            'a.descripcion',
                                            'da.url')
                                    ->where('au.activo', 1)
                                    ->where('da.actual', 1)
                                    ->where('au.id_usuario', $id_usuario)
                                    ->get();
            foreach($list_archivos as $lista){
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
            $categorias = DB::table('categoria')
                    ->whereIn('id', $arregloCat)
                    ->get();
            $subcategorias = DB::table('subcategoria')
                    ->select('*', 'id as id_subcategoria')
                    ->whereIn('id', $arregloCat)
                    ->get();
            // array_push($categorias, array('subcategorias'=>$subcategorias));
            if($tipo == 1){
                return $this->crearRespuesta(1, $categorias, 'info', 200);            
            }else{
                return $this->crearRespuesta(1, $subcategorias, 'info', 200);            
            }
            // array_push($data, array("categorias" => $categorias));
            // array_push($data, array("subcategorias" => $subcategorias));
            // print_r($categorias);
            // print_r($subcategorias);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }

    public function getListadoArchivos(Request $request){
        try {
            $id_usuario = $request->get('id_usuario');
            $id_subcategoria = $request->get('id_subcategoria');
            $miUrl = env('APP_URL', '');
            $data = DB::table('archivo_usuario as au')
                            ->join('archivo as a', 'a.id', '=', 'au.id_archivo')
                            ->join('detalle_archivo as da', 'a.id', '=', 'da.id_archivo')
                            ->select(
                                    'au.id as id_archivo',
                                    'a.id_subcategoria',
                                    'a.nombre',
                                    'a.descripcion',
                                    'da.url')
                            ->where('au.activo', 1)
                            ->where('da.actual', 1)
                            ->where('a.id_subcategoria', $id_subcategoria)
                            ->where('au.id_usuario', $id_usuario)
                            ->orderBy('a.consecutivo', 'ASC')
                            ->get();
            foreach($data as $miData){

                $miData->url = $miUrl.$miData->url;
            }
            return $this->crearRespuesta(1, $data, 'info', 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
    public function setEstatusArchivoUsuario(request $request) {
        try {
            $estatus = 3;
            $id_archivo = $request['id_archivo'];
            DB::update('update archivo_usuario 
            set id_estatus = ?, fecha_estatus = ?
            where id = ?', 
            [$estatus, $this->fechaActual(), $id_archivo]);
            return $this->crearRespuesta(1, null, 'Se ha actualizado correctamente.', 201);

        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
    }
    public function totalesDashboard($id_usuario){
        try {
            $id_estatus_leido = 3;
            $id_estatus_nuevo = 4;
            $miUrl = env('APP_URL', '');
            $contador = 1;
            $leidos=DB::table('archivo_usuario')
            ->select('*')
            ->where('id_usuario', $id_usuario)
            ->where('activo', 1)
            ->where('id_estatus', $id_estatus_leido)
            ->count();
            
            $nuevos=DB::table('archivo_usuario')
            ->select('*')
            ->where('id_usuario', $id_usuario)
            ->where('activo', 1)
            ->where('id_estatus', $id_estatus_nuevo)
            ->count();
            
            $totales=DB::table('archivo_usuario')
            ->select('*')
            ->where('id_usuario', $id_usuario)
            ->where('activo', 1)
            ->count();  

            $tableros = DB::table('tablero')
                        ->select('id', 'titulo', 'descripcion', 'url', 'imagen')
                        ->orderBy('titulo', 'ASC')
                        ->whereDate('fecha_inicio', '<=', $this->fechaActual())
                        ->whereDate('fecha_final', '>=', $this->fechaActual())
                        ->where('activo', 1)
                        ->get();
            foreach($tableros as $tablero){
                $tablero->orden = $contador;
                $tablero->url = $miUrl.$tablero->url;
                $contador = $contador + 1;
            }

            $data = [
                "nuevos"=> $nuevos,
                "leidos" => $leidos,
                "totales" => $totales
            ];

            $info = [
                "dashboard" => $data,
                "tableros" => $tableros
            ];
            return $this->crearRespuesta(1, $info, 'Info.', 201); 
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
    }
    public function usuariosSinAsignar(){
        try {
            $data = DB::table('users')
            ->select('*')
            ->where('id_rol', 0)
            ->where('activo', 1)
            ->orderBy('fecha_creacion', 'ASC')
            ->get();
            return $this->crearRespuesta(1, $data, 'Info.', 200);

        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
    }
    public function setActivoFalsoUsuario(Request $request, $id_usuario){
        try {
            $usuario = User::find($id_usuario);
            $usuario->activo = false;
            $usuario->timestamps = false;
            $usuario->fecha_modificacion = $this->fechaActual();
            $usuario->usuario_modificacion = $request->get('usuario');
            $usuario->save();
            return $this->crearRespuesta(1, $usuario, 'Se ha eliminado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }    
    public function getCatSubArchivosUser($id_usuario){
        try {
            $categorias = array();
            $subcategorias = array();
            $arregloCat = array();
            $arregloSub = array();
            $data = array();
            $miUrl = env('APP_URL', '');
            $archivo_usuario = DB::table('archivo_usuario as au')
                                    ->join('archivo as a', 'a.id', '=', 'au.id_archivo')
                                    ->join('subcategoria as s', 's.id', '=', 'a.id_subcategoria')
                                    ->join('categoria as c', 'c.id', '=', 's.id_categoria')
                                    ->join('detalle_archivo as da', 'a.id', '=', 'da.id_archivo')
                                    ->select('c.id as id_categoria', 
                                            'c.titulo as categoria',
                                            'c.descripcion as desc_categoria', 
                                            'c.imagen', 
                                            's.id as id_subcategoria',
                                            's.titulo as subcategoria', 
                                            's.descripcion as desc_subcategoria',  
                                            'a.nombre',
                                            'a.descripcion',
                                            'da.url')
                                    ->where('au.activo', 1)
                                    ->where('da.actual', 1)
                                    ->where('au.id_usuario', $id_usuario)
                                    ->get();
            foreach($archivo_usuario as $lista){
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
                    $misArchivos = DB::table('archivo_usuario as au')
                                    ->join('archivo as a', 'au.id_archivo', '=', 'a.id' )
                                    ->join('subcategoria as s', 's.id', '=', 'a.id_subcategoria')
                                    ->join('detalle_archivo as da', 'da.id_archivo', '=', 'a.id' )
                                    ->select('au.id', 'au.id_archivo', 'a.descripcion', 's.titulo', 'a.nombre',
                                            'da.url')
                                    ->where('au.activo', 1)
                                    ->where('a.activo', 1)
                                    ->where('au.id_usuario', $id_usuario)
                                    ->where('a.id_subcategoria', $subcat->id)
                                    ->where('da.actual', 1)
                                    ->orderBy('a.consecutivo', 'ASC')
                                    ->get();
                                    foreach($misArchivos as $miData){
                                        $miData->url = $miUrl.$miData->url;
                                    }  
                    if($contadorSub == 0){
                        $misSubcategorias=json_decode(json_encode($misSubcategorias), true);
                    }
                    $misSubcategorias[$contadorSub]+=["archivos"=>$misArchivos];
                    $contadorSub ++; 
                }
                if($contador == 0){
                    $misCategorias=json_decode(json_encode($misCategorias), true);
                }
                $misCategorias[$contador]+=["clave"=>str_replace(' ', '', $categoria->descripcion)];
                $misCategorias[$contador]+=["icono"=> $miUrl.$categoria->imagen];
                $misCategorias[$contador]+=["subcategorias"=>$misSubcategorias];
                $contador ++;
            }
            return $this->crearRespuesta(1, $misCategorias, 'info', 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
    public function getUsuariosAdmin($index){
        try {
            $totales = DB::table('users as u')
                    ->join('roles as r', 'u.id_rol', '=', 'r.id')
                    ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                    ->select('u.name as nombre', 'u.tipo', 'r.rol', 'd.departamento')
                    ->where('u.activo', 1)
                    ->count();
            $resultado = $totales / 8;
            $resultado = ceil($resultado);
            $data = DB::table('users as u')
                    ->join('roles as r', 'u.id_rol', '=', 'r.id')
                    ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                    ->select('u.id', 'u.name as nombre', 'u.tipo', 'u.usuario',
                    'u.id_rol', 'r.rol', 'r.id_departamento', 'd.departamento')
                    ->where('u.activo', 1)
                    ->skip($index)
                    ->take(8)
                    ->get();
            return response()->json([
                'data' => $data,
                'mensaje' => $totales,
                'paginas' => $resultado,
                'ok' => true
            ], 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }   
    }
    public function getBusqueda(Request $request){
        try {
            $valor = $request['busqueda'];
            $contador = DB::table('users as u')
                    ->join('roles as r', 'u.id_rol', '=', 'r.id')
                    ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                    ->select('u.name', 'u.tipo', 'r.rol', 'd.departamento')
                    ->where('u.activo', 1)
                    ->count();
            $data = DB::table('users as u')
                    ->join('roles as r', 'u.id_rol', '=', 'r.id')
                    ->join('departamento as d', 'r.id_departamento', '=', 'd.id')
                    ->select('u.id', 'u.name as nombre', 'u.tipo', 'u.usuario',
                            'u.id_rol', 'r.rol', 'r.id_departamento', 'd.departamento')
                    ->orWhere('u.name', 'LIKE', '%'.$valor.'%')
                    ->orWhere('u.usuario', 'LIKE', '%'.$valor.'%')
                    ->where('u.activo', 1)
                    ->take(10)
                    ->get();
            return $this->crearRespuesta(1, $data, $contador, 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }   
    }
    public function guardarUsuarioAdmin(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'password' => 'required|confirmed',
            'usuario' => 'required|unique:users'
        ]);
        try {
            $user = new User;
            $user->name = $request->input('name');
            // $user->email = $request->input('email');
            $user->usuario = $request->input('usuario');
            $user->id_rol = $request->input('id_rol');
            $user->activo = 1;
            $user->tipo = $request->input('tipo');
            $user->fecha_creacion = $this->fechaActual();
            $user->fecha_modificacion = $this->fechaActual();
            $user->usuario_creacion = $request->input('id_usuario');
            $user->usuario_modificacion = $request->input('id_usuario');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();

            $this->asignaArchivosUsuario(DB::getPdo()->lastInsertId(), $request->get('id_rol'), $request->get('id_usuario'));

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED', 'ok' => true], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed! '.$e, 'ok'=> false], 409);
        }

    }
}