<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use  App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use  App\Models\Subcategoria;
use App\Models\Archivo;

class CategoriaController extends Controller
{
    public function storeCategoria(Request $request){
        try {
            $categoria = new Categoria;
            $categoria->descripcion = $request->get('descripcion');
            $categoria->orden = $request->get('orden');
            $categoria->activo = true;
            $categoria->timestamps = false;
            $categoria->fecha_creacion = $this->fechaActual();
            $categoria->fecha_modificacion = $this->fechaActual();
            $categoria->usuario_creacion = $request->get('usuario');
            $categoria->usuario_modificacion = $request->get('usuario');
            $categoria->save();
            return $this->crearRespuesta(1, $categoria, 'Se ha creado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
    }
    public function updateCategoria(Request $request, $id_categoria){
        try {
            $categoria = Categoria::find($id_categoria);
            $categoria->descripcion = $request->get('descripcion');
            $categoria->orden = $request->get('orden');
            $categoria->activo = $request->get('activo');
            $categoria->timestamps = false;
            $categoria->fecha_modificacion = $this->fechaActual();
            $categoria->usuario_modificacion = $request->get('usuario');
            $categoria->save();
            return $this->crearRespuesta(1, $categoria, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo la actualización la información '.$th->getMessage(), 300);
        }
    }
    public function getCategorias(){
        try {
            $categorias = Categoria::where('activo', 1)
                                    ->orderBy('orden', 'ASC')                        
                                    ->get();
            return $this->crearRespuesta(1, $categorias, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getCategoria($id_categoria){
        try {
            $categoria = Categoria::find($id_categoria);
            return $this->crearRespuesta(1, $categoria, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function getListadoDocumentos(){
        $contador = 0;
        $contadorSub = 0;
        $contadorArchivo = 0;
        try {
            $categorias = Categoria::where('activo', 1)
                                ->orderBy('orden', 'ASC')                        
                                ->get();
            foreach($categorias as $categoria){
                $subcategorias = Subcategoria::where('id_categoria', $categoria->id)
                                            ->orderBy('orden', 'ASC')                        
                                            ->get();
                $contadorSub = 0;
                foreach($subcategorias as $subcategoria){
                    $archivos = Archivo::where('id_subcategoria', $subcategoria->id)
                                                ->get();
                        $contadorArchivo = 0;
                        foreach($archivos as $archivo){
                        $detalles = DB::table('detalle_archivo')
                                        ->select('*')
                                        ->where('activo', 1)
                                        ->where('id_archivo', $archivo->id)    
                                        ->get();                        
                        
                        if($contadorArchivo == 0){
                            $archivos=json_decode(json_encode($archivos), true);
                        }
                        $archivos[$contadorArchivo]+=["detalle"=>$detalles];
                        $contadorArchivo ++;
                    }
                    if($contadorSub == 0){
                        $subcategorias=json_decode(json_encode($subcategorias), true);
                    }
                    $subcategorias[$contadorSub]+=["archivos"=>$archivos];
                    $contadorSub ++;
                }
                if($contador == 0){
                    $categorias=json_decode(json_encode($categorias), true);
                }
                $categorias[$contador]+=["subcategoria"=>$subcategorias];
                $contador ++;
            }
            return $this->crearRespuesta(1, $categorias, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
        
    }

}