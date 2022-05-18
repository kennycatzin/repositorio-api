<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Archivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ArchivoController extends Controller
{
    public function storeArchivo(Request $request){
        try {
            $validaUnicoArchivo = Archivo::where('id_tipo', $request->get('id_tipo'))
                                    ->where('id_departamento', $request->get('id_departamento'))
                                    ->where('consecutivo', $request->get('consecutivo'))
                                    ->where('activo', 1)
                                    ->count();
            if($validaUnicoArchivo > 0){
                return $this->crearRespuesta(0, null, 'Ya existe un archivo con estas características', 200);
            }
            $miUrl = env('APP_URL', '');
            $miNombre = $this->getNombreArchivo($request->get('id_tipo'), $request->get('id_departamento'), 0, $request->get('consecutivo'));
            $archivo = new Archivo;
            $archivo->id_subcategoria = $request->get('id_subcategoria');
            $archivo->nombre = $miNombre;
            $archivo->consecutivo = $request->get('consecutivo');
            $archivo->descripcion = $request->get('descripcion');
            $archivo->resumen = $request->get('resumen');
            $archivo->id_tipo = $request->get('id_tipo');
            $archivo->id_departamento = $request->get('id_departamento');
            $archivo->activo = true;
            $archivo->timestamps = false;
            $archivo->fecha_creacion = $this->fechaActual();
            $archivo->fecha_modificacion = $this->fechaActual();
            $archivo->usuario_creacion = $request->get('usuario');
            $archivo->usuario_modificacion = $request->get('usuario');
            $archivo->save();



            //TODO: Obtener el detalle para mostrar en pantalla, de igual manera en el update
            $last_instert = DB::getPdo()->lastInsertId();
            $this->guardarDetalleArchivo($request, $last_instert);
            $archivo=json_decode(json_encode($archivo), true);
            $miDetalle = DB::table('detalle_archivo')
                            ->select('*')
                            ->where('id_archivo', $last_instert)
                            ->where('activo', 1)
                            ->get();
            foreach($miDetalle as $miData){

                $miData->url = $miUrl.$miData->url;
            }   
            $archivo+=["detalle"=>$miDetalle];
            return $this->crearRespuesta(1, $archivo, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    private function getNombreArchivo($id_tipo, $id_departamento, $id_archivo, $numeral){
        $nombre_corto = DB::table('tipo')
                        ->select('nombre_corto')
                        ->where('id', $id_tipo)
                        ->first();
        $area = DB::table('departamento')
                        ->select('nombre_corto')
                        ->where('id', $id_departamento)
                        ->first();
        
        $consecutivo = DB::table('detalle_archivo')
                        ->where('id_archivo', $id_archivo)
                        ->count();
        $nombre = sprintf($nombre_corto->nombre_corto.'-'.$area->nombre_corto.'-'.'%03d', $numeral);
        return $nombre;
    }
    public function updateArchivo(Request $request, $id_archivo){
        try {
            $validaUnicoArchivo = Archivo::where('id', '!=', $id_archivo)
                                    ->where('id_tipo', $request->get('id_tipo'))
                                    ->where('id_departamento', $request->get('id_departamento'))
                                    ->where('consecutivo', $request->get('consecutivo'))
                                    ->where('activo', 1)
                                    ->count();
            if($validaUnicoArchivo > 0){
                return $this->crearRespuesta(0, null, 'Ya existe un archivo con estas características', 200);
            }
            $miUrl = env('APP_URL', '');
            $miNombre = '';
            $archivo = Archivo::find($id_archivo);
            $archivo->descripcion = $request->get('descripcion');
            $archivo->consecutivo = $request->get('consecutivo');
            $archivo->resumen = $request->get('resumen');
            $archivo->id_tipo = $request->get('id_tipo');
            $archivo->id_departamento = $request->get('id_departamento');
            $archivo->timestamps = false;
            $archivo->fecha_modificacion = $this->fechaActual();
            $archivo->usuario_modificacion = $request->get('usuario');   
            $miNombre = $this->getNombreArchivo($request->get('id_tipo'), $request->get('id_departamento'), $id_archivo, $request->get('consecutivo'));         
            $archivo->nombre =  $miNombre;
            
            $archivo->save();
            if ($request->hasFile('archivo')) {
                $this->guardarDetalleArchivo($request, $id_archivo);

            }
            $archivo=json_decode(json_encode($archivo), true);
            $miDetalle = DB::table('detalle_archivo')
                            ->select('*')
                            ->where('id_archivo', $id_archivo)
                            ->where('activo', 1)
                            ->get();
            foreach($miDetalle as $miData){

                $miData->url = $miUrl.$miData->url;
            }   
            $archivo+=["detalle"=>$miDetalle];
            return $this->crearRespuesta(1, $archivo, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getArchivos(){
        try {
            $archivos = Archivo::where('activo', 1)
                                    ->orderBy('nombre', 'ASC')                        
                                    ->get();
            return $this->crearRespuesta(1, $archivos, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getArchivo($id_archivo){
        try {
            $archivo = Archivo::find($id_archivo);
            return $this->crearRespuesta(1, $archivo, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function storeDetalleArchivo(Request $request){
        try {
            $detalle= DB::table('detalle_archivo')->insert(
                [
                    'id_archivo' => $request["id_archivo"], 
                    'id_tipo' => $request["id_tipo"],
                    'url'=> $request["url"],
                    'observaciones'=> $request["observaciones"],
                    'consecutivo'=> $request["consecutivo"],
                    'actual'=> $request["actual"],
                    'activo'=> true,
                    'fecha_creacion'=> $this->fechaActual(),
                    'fecha_modificacion'=> $this->fechaActual(),
                    'usuario_creacion'=> $request["usuario"],
                    'usuario_modificacion'=> $request["usuario"],
                ]
            );
            return $this->crearRespuesta(1, $detalle, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo almacenar la información '.$th->getMessage(), 300);
        }
    } 
    protected function fileUpload(Request $request, $id, $id_archivo) {
        $archivo = DB::table('detalle_archivo')
                    ->select('*')
                    ->where('id', $id)
                    ->get();
        $response = null;
        $detalle = (object) ['archivo' => ""];
        $nombre_archivo = DB::table('archivo')
                                ->select('nombre')
                                ->where('id', $id_archivo)
                                ->where('activo', 1)
                                ->first();
        if  (!is_null($archivo)){
            if ($request->hasFile('archivo')) {
                $original_filename = $request->file('archivo')->getClientOriginalName();
                $original_filename_arr = explode('.', $original_filename);
                $file_ext = end($original_filename_arr);
                $destination_path = './upload/'. $nombre_archivo->nombre.'/';
                $mi_archivo = $nombre_archivo->nombre.'.' . $file_ext;
                if ($request->file('archivo')->move($destination_path, $mi_archivo)) {
                    // $detalle->image = './upload/receta/'.$mi_archivo;
                    // $archivo->imagen = $mi_archivo;
                    // $archivo->save();
                    DB::update('update detalle_archivo 
                    set url = ?, nombre = ? where id = ?', 
                    [ '/upload/'.$nombre_archivo->nombre.'/'.$mi_archivo, $mi_archivo, $id]);
                    return $mi_archivo;
                } 
            } else {
                return $this->crearRespuesta(1, null, 'No existe el archivo', 400);
            }
        }else{
            return $this->crearRespuesta(0, null, 'No existe el usuario', 400);
        }
    }
    public function guardarDetalleArchivo(Request $request, $id_archivo){
        try {
            $id_usuario = $request->get('usuario');
            //$id_archivo = $request->get('id_archivo');
            $id_tipo = $request->get('id_tipo');
            $observaciones = '';
            $susUrl = '';
            $susNombre = '';
            $nombre_archivo = DB::table('archivo')
                                ->select('nombre')
                                ->where('id', $id_archivo)
                                ->where('activo', 1)
                                ->first();
            $detalle = DB::table('detalle_archivo')
                ->select('url', 'nombre')
                ->where('id_archivo', $id_archivo)
                ->orderBy('consecutivo', 'DESC')
                ->first();     

            $count = DB::table('detalle_archivo')
                            ->where('id_archivo', $id_archivo)
                            ->where('activo', 1)
                            ->count();
            DB::update('update detalle_archivo 
            set actual = 0 
            where id_archivo = ?', 
            [$id_archivo]);
            if($count > 0){
                $susUrl = $detalle->url;
                $susNombre = $detalle->nombre;
            }
            $consecutivo  = $request->get('consecutivo');
            $actual = $request->get('actual');
            DB::insert('insert into detalle_archivo 
            (id_archivo, id_tipo, nombre, url, observaciones, consecutivo, actual, activo,
            fecha_creacion, fecha_modificacion, usuario_creacion, usuario_modificacion) 
            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id_archivo, $id_tipo, $susNombre, $susUrl, $observaciones, $count + 1, 1, 1, $this->fechaActual(),
            $this->fechaActual(), $id_usuario, $id_usuario]);
            $archivo_guardado = $this->fileUpload($request, DB::getPdo()->lastInsertId(), $id_archivo);
            $data_departamentos = DB::table('archivo_usuario as au')
                            ->join('users as u', 'u.id', '=', 'au.id_usuario')
                            ->select('u.email as correo')
                            ->where('au.id_archivo', $id_archivo)
                            ->distinct()
                            ->get();

            $depas = array();
            foreach($data_departamentos as $depa){
                array_push($depas, $depa->correo);
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
                Mail::send('plantilla_archivo', $data, function($message) use ($depas) {
                    $message->to($depas)
                            ->subject('Actualización de documento');
                    $message->from(env('MAIL_USERNAME'),'Repositorio');
                });
            }
            return $this->crearRespuesta(1, null, 'La imagen ha sido subida con éxito', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
    } 
    public function getAdminConfigurar(Request $request){
        try {
            $id_subcategoria = $request->get('id_subcategoria');
            $id_rol = $request->get('id_rol');
            $pila = array();
            $archivos_configurados = DB::table('archivo_rol as ar')
                                        ->join('archivo as a', 'ar.id_archivo', '=', 'a.id')
                                        ->join('subcategoria as sc', 'sc.id', '=', 'a.id_subcategoria')
                                        ->select('ar.id', 'ar.id_archivo', 'a.nombre', 'a.descripcion')
                                        ->where('ar.activo', 1)
                                        ->where('ar.id_rol', $id_rol)
                                        ->where('a.id_subcategoria', $id_subcategoria)
                                        ->orderBy('descripcion', 'ASC')
                                        ->get();
            foreach ($archivos_configurados as $archivo) {
                array_push($pila, $archivo->id_archivo);
            }
            $archivos_crudos = DB::table('archivo')
            ->select('id as id_archivo', 'nombre', 'descripcion')
            ->where('id_subcategoria', $id_subcategoria)
            ->where('activo', 1)
            ->whereNotIn('id', $pila)
            ->orderBy('descripcion', 'ASC')
            ->get();
            $data = [
                    "archivos_crudos" => $archivos_crudos,
                    "archivos_configurados" => $archivos_configurados              
            ];
            return $this->crearRespuesta(1, $data, 'info.', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }                                 
    } 
    public function bajaArchivo(Request $request){
        try {
            $id_archivo = $request['id_archivo'];
            $id_usuario = $request['usuario'];

            DB::update('update detalle_archivo set activo = false,
            usuario_modificacion = ?, fecha_modificacion = ?
            where id_archivo = ?', 
            [$id_usuario, $this->fechaActual(), $id_archivo]);

            DB::update('update archivo_rol set activo = false,
            usuario_modificacion = ?, fecha_modificacion = ?
            where id_archivo = ?', 
            [$id_usuario, $this->fechaActual(), $id_archivo]);

            DB::update('update archivo_usuario set activo = false,
            usuario_modificacion = ?, fecha_modificacion = ?
            where id_archivo = ?', 
            [$id_usuario, $this->fechaActual(), $id_archivo]);

            DB::update('update archivo set activo = false, 
            usuario_modificacion = ?, fecha_modificacion = ? 
            where id = ?', 
            [$id_usuario, $this->fechaActual(), $id_archivo]);
            return $this->crearRespuesta(1, null, 'Se ha dado eliminado el registro apartado', 203);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
    }
    public function getAuxFormularioArchivo(){       
        try {
            $tipos = DB::table('tipo')
                ->select('id as id_tipo', 'tipo', 'nombre_corto', DB::raw("CONCAT(nombre_corto, '-',tipo) AS tipo_completo"))
                ->where('activo', 1)
                ->get();
        
            $depas = DB::table('departamento')
                    ->select('id as id_departamento', 'departamento', 'nombre_corto', DB::raw("CONCAT(nombre_corto, '-',departamento) AS departamento_completo"))
                    ->where('activo', 1)
                    ->get();

            $roles = DB::table('roles')
                    ->select('id as id_rol', 'rol', 'descripcion')
                    ->where('activo', 1)
                    ->get();
            $auxiliar = array(
                "tipos" => $tipos,
                "depas" => $depas,
                "roles" => $roles
            );
            return $this->crearRespuesta(1, $auxiliar, 'Info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
    }
    public function busquedaArchivo(Request $request){
        try {
            $valor = $request['busqueda'];
            $miUrl = env('APP_URL', '');
            $data = DB::table('archivo_usuario as au')
            ->join('archivo as a', 'a.id', '=', 'au.id_archivo')
            ->join('detalle_archivo as da', 'a.id', 'da.id_archivo')
            ->join('subcategoria as s', 's.id', 'a.id_subcategoria')
            ->join('categoria as c', 'c.id', 's.id_categoria')
            ->select('au.id', 'a.id as ii', 'a.nombre', 'a.descripcion', 'da.url', 
                    'c.titulo as categoria', 's.titulo as subcategoria')
            ->where('au.activo', 1)
            ->where('au.id_usuario', $request->get('id_usuario'))
            ->where(function ($query) use ($valor) {
                    $query->orWhere('a.nombre', 'LIKE', '%'.$valor.'%')
                    ->orWhere('a.descripcion', 'LIKE', '%'.$valor.'%');
            })
            ->where('da.actual', 1)
            ->get();
            foreach($data as $miData){
                $miData->url = $miUrl.$miData->url;
            } 
            return $this->crearRespuesta(1, $data, 'Info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
    }

}