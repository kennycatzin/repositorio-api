<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Tablero;
use Illuminate\Support\Facades\DB;
use DateTime;
class TableroController extends Controller
{
    public function storeTablero(Request $request){
        try {
            $tablero = new Tablero;
            $tablero->titulo = $request->get('titulo');
            $tablero->descripcion = $request->get('descripcion');
            $tablero->tipo = $request->get('tipo');
            $tablero->fecha_final = $request->get('fecha_final');
            $tablero->fecha_inicio = $request->get('fecha_inicio');
            $tablero->activo = true;
            $tablero->timestamps = false;
            $tablero->fecha_creacion = $this->fechaActual();
            $tablero->fecha_modificacion = $this->fechaActual();
            $tablero->usuario_creacion = $request->get('usuario');
            $tablero->usuario_modificacion = $request->get('usuario');
            $tablero->save();
            $this->fileUpload($request, DB::getPdo()->lastInsertId());
            return $this->crearRespuesta(1, $tablero, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function getTableros(){
        try {
            $miUrl = env('APP_URL', '');
            $tableros = DB::table('tablero')
                        ->orderBy('titulo', 'ASC')
                        ->whereDate('fecha_inicio', '<=', $this->fechaActual())
                        ->whereDate('fecha_final', '>=', $this->fechaActual())
                        ->where('activo', true)
                        ->get();
            foreach($tableros as $tablero){
                $tablero->url = $miUrl.$tablero->url;
                $datetime1 = new DateTime($tablero->fecha_final);
                $datetime2 = new DateTime( $this->fechaActual());
                $interval = $datetime1->diff($datetime2);
                $tablero->dias = $interval->format('%a');
            }
            return $this->crearRespuesta(1, $tableros, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getTablero($id_tablero){
        try {
            $tablero = Tablero::find($id_tablero);
            return $this->crearRespuesta(1, $tablero, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    protected function fileUpload(Request $request, $id) {
        $tablero = DB::table('tablero')
                    ->select('*')
                    ->where('id', $id)
                    ->get();
        $response = null;
        $detalle = (object) ['archivo' => ""];
        if  (!is_null($tablero)){
            if ($request->hasFile('archivo')) {
                $original_filename = $request->file('archivo')->getClientOriginalName();
                $original_filename_arr = explode('.', $original_filename);
                $file_ext = end($original_filename_arr);
                $destination_path = './upload/tablero/';
                $mi_archivo = 'AFTablero-' . $id . '.' . $file_ext;
                if ($request->file('archivo')->move($destination_path, $mi_archivo)) {

                    // $detalle->image = './upload/receta/'.$mi_archivo;
                    // $archivo->imagen = $mi_archivo;
                    // $archivo->save();
                    DB::update('update tablero 
                    set url = ?, imagen = ? where id = ?', 
                    [ '/upload/tablero/'.$mi_archivo, $mi_archivo, $id]);
                    return $mi_archivo;
                } 
            } else {
                return $this->crearRespuesta(1, null, 'No existe el archivo', 400);
            }
        }else{
            return $this->crearRespuesta(0, null, 'No existe el usuario', 400);
        }
    }
    public function updateTablero(Request $request, $id_tablero){
        try {
            $tablero = Tablero::find($id_tablero);
            $tablero->titulo = $request['titulo'];
            $tablero->descripcion = $request->get('descripcion');
            $tablero->tipo = $request->get('tipo');
            $tablero->fecha_final = $request->get('fecha_final');
            $tablero->fecha_inicio = $request->get('fecha_inicio');
            $tablero->timestamps = false;
            $tablero->fecha_modificacion = $this->fechaActual();
            $tablero->usuario_modificacion = $request->get('usuario');
            $tablero->save();
            $this->fileUpload($request, $id_tablero);
            return $this->crearRespuesta(1, $tablero, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function bajaAviso(Request $request){
        try {
            $id_tablero = $request->get('id_tablero');
            $usuario = $request->get('usuario');
            DB::update('update tablero 
                        set activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
                        where id = ?', 
                        [0, $this->fechaActual(), $usuario, $id_tablero]);
            return $this->crearRespuesta(1, null, 'Se ha eliminado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getArchivosByEstatus(Request $request){
        try {
            $miUrl = env('APP_URL', '');
            $data = DB::table('archivo_usuario as au')
            ->join('archivo as a', 'a.id', '=', 'au.id_archivo')
            ->join('detalle_archivo as da', 'a.id', 'da.id_archivo')
            ->join('subcategoria as s', 's.id', 'a.id_subcategoria')
            ->join('categoria as c', 'c.id', 's.id_categoria')
            ->select('au.id', 'a.nombre', 'a.descripcion', 'da.url', 
                    'c.titulo as categoria', 's.titulo as subcategoria')
            ->where('au.activo', 1)
            ->where('au.id_usuario', $request->get('id_usuario'))
            ->where('au.id_estatus', $request->get('id_estatus'))
            ->where('da.actual', 1)
            ->get();
            foreach($data as $miData){
                $miData->url = $miUrl.$miData->url;
            } 
            return $this->crearRespuesta(1, $data, 'Info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }

    }
}