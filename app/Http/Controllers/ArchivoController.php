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
            $archivo = new Archivo;
            $archivo->id_subcategoria = $request->get('id_subcategoria');
            $archivo->nombre = $request->get('nombre');
            $archivo->descripcion = $request->get('descripcion');
            $archivo->resumen = $request->get('resumen');
            $archivo->activo = true;
            $archivo->timestamps = false;
            $archivo->fecha_creacion = $this->fechaActual();
            $archivo->fecha_modificacion = $this->fechaActual();
            $archivo->usuario_creacion = $request->get('usuario');
            $archivo->usuario_modificacion = $request->get('usuario');
            $archivo->save();
            return $this->crearRespuesta(1, $archivo, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateArchivo(Request $request, $id_archivo){
        try {
            $archivo = Archivo::find($id_archivo);
            $archivo->nombre = $request->get('nombre');
            $archivo->descripcion = $request->get('descripcion');
            $archivo->resumen = $request->get('resumen');
            $archivo->activo = $request->get('activo');
            $archivo->timestamps = false;
            $archivo->fecha_modificacion = $this->fechaActual();
            $archivo->usuario_modificacion = $request->get('usuario');
            $archivo->save();
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
        $detalle = (object) ['imagen' => ""];
        if  (!is_null($archivo)){
            if ($request->hasFile('archivo')) {
                $original_filename = $request->file('archivo')->getClientOriginalName();
                $original_filename_arr = explode('.', $original_filename);
                $file_ext = end($original_filename_arr);
                $destination_path = './upload/'.$id_archivo.'/';
                $mi_archivo = 'AF-' . $id . '.' . $file_ext;
                if ($request->file('archivo')->move($destination_path, $mi_archivo)) {
                    // $detalle->image = './upload/receta/'.$mi_archivo;
                    // $archivo->imagen = $mi_archivo;
                    // $archivo->save();
                    DB::update('update detalle_archivo 
                    set url = ?, nombre = ? where id = ?', 
                    [ '/upload/'.$id_archivo.'/'.$mi_archivo, $mi_archivo, $id]);
                    return $mi_archivo;
                } 
            } else {
                return $this->crearRespuesta(1, null, 'No existe el archivo', 400);
            }
        }else{
            return $this->crearRespuesta(0, null, 'No existe el usuario', 400);
        }
    }
    public function guardarDetalleArchivo(Request $request){
        try {
            $id_usuario = $request->get('id_usuario');
            $id_archivo = $request->get('id_archivo');
            $id_tipo = $request->get('id_tipo');
            $nombre_archivo = DB::table('archivo')
                                ->select('nombre')
                                ->where('id', $id_archivo)
                                ->where('activo', 1)
                                ->first();
            
            $observaciones = $request->get('observaciones');
            DB::update('update detalle_archivo 
            set actual = 0 
            where id_archivo = ?', 
            [$id_archivo]);
            $count = DB::table('detalle_archivo')
                            ->where('id_archivo', $id_archivo)
                            ->where('activo', 1)
                            ->count();
            $consecutivo  = $request->get('consecutivo');
            $actual = $request->get('actual');
            DB::insert('insert into detalle_archivo 
            (id_archivo, id_tipo, nombre, url, observaciones, consecutivo, actual, activo,
            fecha_creacion, fecha_modificacion, usuario_creacion, usuario_modificacion) 
            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id_archivo, $id_tipo, '', '', $observaciones, $count + 1, 1, 1, $this->fechaActual(),
            $this->fechaActual(), $id_usuario, $id_usuario]);
            $archivo_guardado = $this->fileUpload($request, DB::getPdo()->lastInsertId(), $id_archivo);
            $data_departamentos = DB::table('archivo_usuario as au')
                            ->join('users as u', 'u.id', '=', 'au.id_usuario')
                            ->join('roles as r', 'r.id', '=', 'u.id_rol')
                            ->join('departamento as d', 'd.id', '=', 'r.id_departamento')
                            ->select('d.departamento', 'd.correo')
                            ->where('au.id_archivo', $id_archivo)
                            ->distinct()
                            ->get();
            if($count > 0){
                foreach($data_departamentos as $departamento){
                    $data = array(
                        'equipo' => $departamento->departamento,
                        'archivo' => $nombre_archivo,
                        'observaciones' => $observaciones
                    );
                    Mail::send('plantilla_correo', $data, function($message) {
                        $message->to($departamento->correo, 'Pruebas')
                                ->subject('Pruebas notificación del correo');
                        $message->from(env('MAIL_USERNAME'),'Repositorio interno');
                    });
                }
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
                                        ->select('ar.*')
                                        ->where('ar.activo', 1)
                                        ->where('ar.id_rol', $id_rol)
                                        ->where('a.id_subcategoria', $id_subcategoria)
                                        ->get();
            foreach ($archivos_configurados as $archivo) {
                array_push($pila, $archivo->id_archivo);
            }
            $archivos_crudos = DB::table('archivo')
            ->select('*')
            ->where('id_subcategoria', $id_subcategoria)
            ->where('activo', 1)
            ->whereNotIn('id', $pila)
            ->get();
            $data = [
                    "archivos_crudos" => $archivos_crudos,
                    "archivos_configurados" => $archivos_configurados              
            ];
            return $this->crearRespuesta(1, $data, 'info.', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'Ha ocurrido un error '.$th->getMessage(), 300);
        }
        
                                    
    }  

}