<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function updateUser(Request $request, $id_usuario){
        try {
            $usuario = User::find($id_usuario);
            $usuario->name = $request->get('name');
            $usuario->email = $request->get('email');
            $usuario->id_rol = $request->get('id_rol');
            $usuario->tipo = $request->get('tipo');
            $usuario->activo = $request->get('activo');
            $usuario->fecha_modificacion = $this->fechaActual();
            $usuario->usuario_modificacion = $request->get('usuario');
            $usuario->save();
            $this->asignarRolUsuario($request);
            return $this->crearRespuesta(1, $usuario, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function asignarRolUsuario(Request $request){
        try {
            $id_usuario = $request->get('id_usuario');
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
                        DB::insert('insert into archivo_usuario 
                        (id_usuario, id_archivo, fecha_caducidad, vigente, tipo, activo, 
                        usuario_creacion, usuario_modificacion, fecha_creacion, fecha_modificacion) 
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                        [$id_usuario, $archivo->id_archivo, $this->fechaActual(), 1, 1, 1, $usuario, $usuario, 
                        $this->fechaActual(), $this->fechaActual()]);
                    }else{
                        if($val_archivo->activo == 0){
                            DB::update('update archivo_usuario 
                            set tipo = ?, vigente = ?, activo = ?, usuario_modificacion = ?,
                            fecha_modificacion = ? where id_archivo = ? and id_usuario = ?', 
                            [1, 1, 1, $usuario, $this->fechaActual(), $archivo->id_archivo, $id_usuario]);
                        }
                    }
                }
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
            $list_archivos = DB::table('archivo_usuario as au')
                                    ->join('archivo as a', 'a.id', '=', 'au.id_archivo')
                                    ->join('subcategoria as s', 's.id', '=', 'a.id_subcategoria')
                                    ->join('categoria as c', 'c.id', '=', 's.id_categoria')
                                    ->join('detalle_archivo as da', 'a.id', '=', 'da.id_archivo')
                                    ->select('c.id as id_categoria', 
                                            'c.descripcion as categoria', 
                                            's.id as id_subcategoria',
                                            's.descripcion as subcategoria', 
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
                                                    "id_categoria" => $lista->id_categoria,
                                                    "categoria" => $lista->categoria
                                                ));
                }
                if(!in_array($lista->id_subcategoria, $subcategorias, true)){
                    array_push($subcategorias, array(
                                                    "id_subcategoria" => $lista->id_subcategoria,
                                                    "subcategoria" => $lista->subcategoria
                                                ));
                }
            }
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
            $data = DB::table('archivo_usuario as au')
                            ->join('archivo as a', 'a.id', '=', 'au.id_archivo')
                            ->join('detalle_archivo as da', 'a.id', '=', 'da.id_archivo')
                            ->select(
                                    'a.id_subcategoria',
                                    'a.nombre',
                                    'a.descripcion',
                                    'da.url')
                            ->where('au.activo', 1)
                            ->where('da.actual', 1)
                            ->where('a.id_subcategoria', $id_subcategoria)
                            ->where('au.id_usuario', $id_usuario)
                            ->get();
            return $this->crearRespuesta(1, $data, 'info', 200);            
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getLine().' '.$th->getMessage(), 300);
        }
    }
}