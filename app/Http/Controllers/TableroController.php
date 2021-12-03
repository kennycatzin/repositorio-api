<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Tablero;
use Illuminate\Support\Facades\DB;

class TableroController extends Controller
{
    public function storeTablero(Request $request){
        try {
            print_r($request->input());

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
            $tableros = DB::table('tablero')
                        ->orderBy('titulo', 'ASC')
                        ->whereDate('fecha_inicio', '<=', $this->fechaActual())
                        ->whereDate('fecha_final', '>=', $this->fechaActual())
                        ->get();
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
        $detalle = (object) ['imagen' => ""];
        print_r($request->input());
        if  (!is_null($tablero)){
            echo "entro";
            if ($request->hasFile('imagen')) {
                echo "entrooo";
                $original_filename = $request->file('imagen')->getClientOriginalName();
                $original_filename_arr = explode('.', $original_filename);
                $file_ext = end($original_filename_arr);
                $destination_path = './upload/tablero/';
                $mi_archivo = 'AFTablero-' . $id . '.' . $file_ext;
                echo "dentro";
                if ($request->file('imagen')->move($destination_path, $mi_archivo)) {
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
            print_r($request->input());
            $tablero = Tablero::find($id_tablero);
            $tablero->titulo = $request['titulo'];
            $tablero->descripcion = $request->get('descripcion');
            $tablero->tipo = $request->get('tipo');
            $tablero->fecha_final = $request->get('fecha_final');
            $tablero->fecha_inicio = $request->get('fecha_inicio');
            $tablero->activo = $request->get('activo');
            $tablero->timestamps = false;
            $tablero->fecha_modificacion = $this->fechaActual();
            $tablero->usuario_modificacion = $request->get('usuario');
            $tablero->save();
            $this->fileUpload($request, $id_tablero);
            echo "llego aqui";
            return $this->crearRespuesta(1, $tablero, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
}