<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'usuario' => 'required|unique:users',
            'password' => 'required|confirmed',
        ]);
        try {
            $user = new User;
            $user->name = $request->input('name');
            // $user->email = $request->input('email');
            $user->usuario = $request->input('usuario');
            $user->id_rol = 0;
            $user->activo = 1;
            $user->tipo = '';
            $user->fecha_creacion = $this->fechaActual();
            $user->fecha_modificacion = $this->fechaActual();
            $user->usuario_creacion = $request->input('name');
            $user->usuario_modificacion = 0;
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED', 'ok' => true], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed! '.$e->getLine(), 'ok'=> false], 409);
        }
    }
    public function updateUser(Request $request, $id_usuario){
        try {
            $this->validate($request, [
                'name' => 'required|string',
                'password' => 'confirmed',
            ]);
            $usuario = User::find($id_usuario);
            $usuario->name = $request->get('name');
            // $usuario->email = $request->get('email');
            $usuario->id_rol = $request->get('id_rol');
            $usuario->tipo = $request->get('tipo');
            $usuario->activo = true;
            $usuario->usuario = $request->get('usuario');
            $usuario->fecha_modificacion = $this->fechaActual();
            $usuario->usuario_modificacion = $request->get('id_usuario');
            if($request->input('password') != ""){
                $plainPassword = $request->input('password');
                $usuario->password = app('hash')->make($plainPassword);
            }            
            $usuario->save();
           $this->asignaArchivosUsuario($id_usuario, $request->get('id_rol'), $request->get('id_usuario'));
            return $this->crearRespuesta(1, $usuario, 'Se ha modificado la información', 201);
        } catch (\Throwable $th) {
            return $this->crearRespuesta(0, null, 'No se pudo completar la información '.$th->getMessage(). ' '.$th->getLine(), 300);
        }
    }
    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['usuario', 'password']);
        $data = User::where('usuario', $request["usuario"])->first();
        $count = User::where('usuario', $request["usuario"])->count();


        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token, $data);
    }
    public function renovarToken($id){
        $credentials = [
                        'email' => "kenny.catzin", 
                        'password' => "123456"
                    ];

        $user = User::where('id', $id)->count();
        $user_data = User::where('id', $id)->first();
        
        if($user > 0){
            return $this->respondWithToken("123456789", $user_data);
        }else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }
}