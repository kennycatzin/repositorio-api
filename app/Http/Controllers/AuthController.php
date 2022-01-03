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
            return response()->json(['message' => 'User Registration Failed! '.$e, 'ok'=> false], 409);
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
                        'email' => "kenny.catzin@gmail.com", 
                        'password' => "123456"
                    ];

        $user = User::where('id', $id)->count();
        $user_data = User::where('id', $id)->first();

        if($user > 0){
            if(! $token = Auth::attempt($credentials)){
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            return $this->respondWithToken($token, $user_data);
        }else{
            return response()->json(['message' => 'Unauthorized'], 401);
        }

    }


}