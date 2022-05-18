<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
   public function getDocumentosPorRol(Request $request){
        try {
            // $data = DB::table('roles as r')
            //     ->join('archivo_rol as ar', 'r.id', '=', 'ar.id_rol')
            //     ->join('archivo as a', 'a.id', '=', 'ar.id_archivo')
            //     ->select('r.id', 'r.rol', '')7
            $data;
            $id_rol = $request["id_rol"];
            if($id_rol == 0){
                $data = DB::table('roles as r')
                ->join('archivo_rol as ar', 'r.id', '=', 'ar.id_rol')
                ->join('archivo as a', 'a.id', '=', 'ar.id_archivo')
                ->join('subcategoria as s', 's.id', 'a.id_subcategoria')
                ->join('categoria as c', 'c.id', 's.id_categoria')
                ->select('r.id', 'r.rol', 'a.nombre', 'a.descripcion',  
                        'c.titulo as categoria', 's.titulo as subcategoria')
                ->where('ar.activo', 1)      
                ->orderBy('r.rol', 'ASC')
                ->get();
            }else{
                $data = DB::table('roles as r')
                ->join('archivo_rol as ar', 'r.id', '=', 'ar.id_rol')
                ->join('archivo as a', 'a.id', '=', 'ar.id_archivo')
                ->join('subcategoria as s', 's.id', 'a.id_subcategoria')
                ->join('categoria as c', 'c.id', 's.id_categoria')
                ->select('r.id', 'r.rol', 'a.nombre', 'a.descripcion',  
                        'c.titulo as categoria', 's.titulo as subcategoria')
                ->where('ar.activo', 1)     
                ->where('r.id', $id_rol)      
                ->orderBy('r.rol', 'ASC')
                ->get();
            }
            
            return $this->crearRespuesta(1, $data, 'Info', 200);

        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
   }
   public function getInfoUsuarios(Request $request){
        try {
            $id_estatus_leido = $this->getEstatusMix("VISTO");
            $id_estatus_nuevo = $this->getEstatusMix("NUEVO");


            $usuarios = DB::table('users as u')
                ->join('roles as r', 'r.id', '=', 'u.id_rol')
                ->select('u.id', 'u.name', 'u.usuario', 'u.email', 'u.ultima_conexion', 'r.rol',
                        DB::raw("(select COUNT(*) from archivo_usuario where id_usuario = u.id) as totales"),
                        DB::raw("(select COUNT(*) from archivo_usuario where id_usuario = u.id and id_estatus = ".$id_estatus_leido.") as leido"),
                        DB::raw("(select COUNT(*) from archivo_usuario where id_usuario = u.id and id_estatus = ".$id_estatus_nuevo.") as nuevo")
                )
                ->where('u.activo', 1)
                ->orderBy('u.name', 'ASC')
                ->get();

            return $this->crearRespuesta(1, $usuarios, 'Info.', 201); 
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(), 300);
        }
   }

}