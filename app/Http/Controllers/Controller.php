<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
    protected function crearRespuesta($tipo, $data, $mensaje, $codigo){
        if($tipo == 1){
            return response()->json([
                'data' => $data,
                'mensaje' => $mensaje,
                'ok' => true
            ], $codigo);
        }elseif($tipo == 0){
            return response()->json([
                'data' => $data,
                'mensaje' => $mensaje,
                'ok' => false
            ], $codigo);
        }
    }
    protected function fechaActual(){
        $ldate = date('Y-m-d H:i:s');
        return $ldate;
    }
}
