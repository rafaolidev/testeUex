<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    #faz o login e  gera o token de autorização oauth2 e tamb´me  informações do user caso
    #o front utilize tokens
    public function login(Request $request){

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password] )) {

            $user = Auth::user();
            $user->token = $user->createToken(''.$user->email)->accessToken;
            return response()->json($user, 200);

        }else{
            $response = ["message" => "Usuário não existe"];
            return response()->json($response, 422);
        }

    }
}
