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
            $categoria->titulo = $request->get('titulo');
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
            $categoria->titulo = $request->get('titulo');
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
        $miUrl = env('APP_URL', '');
        try {
            $categorias = Categoria::where('activo', 1)
                                    ->orderBy('titulo', 'ASC')                        
                                    ->get();
            foreach($categorias as $categoria){
                $subcategorias = Subcategoria::where('id_categoria', $categoria->id)
                                            ->where('activo', 1)
                                            ->orderBy('fecha_modificacion', 'DESC')                        
                                            ->get();
                $contadorSub = 0;
                foreach($subcategorias as $subcategoria){
                    $archivos = Archivo::where('id_subcategoria', $subcategoria->id)
                                                ->where('activo', 1)
                                                ->orderBy('fecha_modificacion', 'DESC')                        
                                                ->get();
                        $contadorArchivo = 0;
                        foreach($archivos as $archivo){
                        $detalles = DB::table('detalle_archivo')
                                        ->select('*')
                                        ->where('activo', 1)
                                        ->where('id_archivo', $archivo->id)    
                                        ->orderBy('fecha_modificacion', 'DESC')                        
                                        ->get();   
                        foreach($detalles as $miData){

                            $miData->url = $miUrl.$miData->url;
                        }                     
                        
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
                $categorias[$contador]+=["randomUUID"=>$this->randw()];
                $contador ++;
            }
            return $this->crearRespuesta(1, $categorias, 'info', 200);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo obtener la información '.$th->getMessage(), 300);
        }
    }
    public function bajaCategoria(Request $request, $id_categoria) {
        // try {
            $usuario = $request->get('usuario');
            DB::update('update categoria 
            set activo = 0, usuario_modificacion = ?, fecha_modificacion = ? 
            where id = ?', [$usuario, $this->fechaActual(), $id_categoria]);
           

            // archivo_usuario
            // archivo_rol
            $subcategorias = DB::table('subcategoria')
                            ->select('id')
                            ->where('id_categoria', $id_categoria)
                            ->get();
            $subcategorias=json_decode(json_encode($subcategorias), true);
            $updateSubcat = Subcategoria::where('id_categoria', $id_categoria)
                            ->update(['activo' => 0, 
                                    'usuario_modificacion' => $usuario, 
                                    'fecha_modificacion' => $this->fechaActual()]);
            $archivos = DB::table('archivo')
                        ->select('id')
                        ->whereIn('id_subcategoria', $subcategorias)
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
            
           $arreglo = array(
               'subcat' => $subcategorias,
               'archivos' => $archivos,
               'detalle' => $detalle_archivos
           );

            return $this->crearRespuesta(1, null, 'Se ha dado de baja la información', 200);

        // } catch (\Throwable $th) {
        //     return $this->crearRespuesta(0, null, 'No se pudo actualizar la información '.$th->getMessage(), 300);
        // }
    }


}