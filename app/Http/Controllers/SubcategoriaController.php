<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Subcategoria;
use Illuminate\Support\Facades\DB;
use App\Models\Archivo;

class SubcategoriaController extends Controller
{
    public function storeSubcategoria(Request $request){
        try {
            $subcategoria = new Subcategoria;
            $subcategoria->id_categoria = $request->get('id_categoria');
            $subcategoria->titulo = $request->get('titulo');
            $subcategoria->descripcion = $request->get('descripcion');
            $subcategoria->orden = $request->get('orden');
            $subcategoria->activo = true;
            $subcategoria->timestamps = false;
            $subcategoria->fecha_creacion = $this->fechaActual();
            $subcategoria->fecha_modificacion = $this->fechaActual();
            $subcategoria->usuario_creacion = $request->get('usuario');
            $subcategoria->usuario_modificacion = $request->get('usuario');
            $subcategoria->save();
            return $this->crearRespuesta(1, $subcategoria, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateSubcategoria(Request $request, $id_subcategoria){
        try {
            $subcategoria = Subcategoria::find($id_subcategoria);
            $subcategoria->descripcion = $request->get('descripcion');
            $subcategoria->titulo = $request->get('titulo');
            $subcategoria->orden = $request->get('orden');
            $subcategoria->timestamps = false;
            $subcategoria->fecha_modificacion = $this->fechaActual();
            $subcategoria->usuario_modificacion = $request->get('usuario');
            $subcategoria->save();
            return $this->crearRespuesta(1, $subcategoria, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function bajaSubcategoria(Request $request, $id_subcategoria){
        try {
            $usuario = $request->get('usuario');
            $subcategoria = Subcategoria::find($id_subcategoria);
            $subcategoria->activo = 0;
            $subcategoria->timestamps = false;
            $subcategoria->fecha_modificacion = $this->fechaActual();
            $subcategoria->usuario_modificacion = $usuario;
            $subcategoria->save();

            $archivos = DB::table('archivo')
                        ->select('id')
                        ->where('id_subcategoria', $id_subcategoria)
                        ->get();
            $archivos=json_decode(json_encode($archivos), true);

            $updateArchivo = Archivo::whereIn('id', $archivos)
                            ->update(['activo' => 0, 
                                    'usuario_modificacion' => $usuario, 
                                    'fecha_modificacion' => $this->fechaActual()]); 

            $detalle_archivos = DB::table('detalle_archivo')
                            ->select('id')
                            ->where('activo', 1)
                            ->whereIn('id_archivo', $archivos)
                            ->get();
            $updateArUs = DB::table('archivo_usuario')                      
                        ->where('activo', 1)
                        ->whereIn('id_archivo', $archivos)
                        ->update(['activo' => 0, 
                                'usuario_modificacion' => $usuario, 
                                'fecha_modificacion' => $this->fechaActual()]);  
            
            $updateArRol = DB::table('archivo_rol')                      
                        ->whereIn('id_archivo', $archivos)
                        ->update(['activo' => 0, 
                                'usuario_modificacion' => $usuario, 
                                'fecha_modificacion' => $this->fechaActual()]);  

            return $this->crearRespuesta(1, $subcategoria, 'Se ha eliminado la información', 203);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getSubcategorias(){
        try {
            $subcategorias = Subcategoria::where('activo', 1)
                                    ->orderBy('orden', 'ASC')                        
                                    ->get();
            return $this->crearRespuesta(1, $subcategorias, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getSubcategoria($id_subcategoria){
        try {
            $subcategoria = Subcategoria::find($id_subcategoria);
            return $this->crearRespuesta(1, $subcategoria, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getSubcategoriasByCategoria($id_categoria){
        $data = Subcategoria::where('id_categoria', $id_categoria)
                            ->where('activo', 1)
                            ->get();
        return $this->crearRespuesta(1, $data, 'info', 200);
 
        
    }
}