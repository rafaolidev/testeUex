<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function reset(Request $request)
    {
        #validação dos dados do formulário
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        #buscar o usuário pelo e-mail
        $user = User::where('email', $request->email)->first();

        #verificar se o usuário existe
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        ##atualizar a senha do usuário
        $user->password = Hash::make($request->password);
        $user->save();

        ##retornar uma resposta de sucesso
        return response()->json(['message' => 'Senha redefinida com sucesso'], 200);
    }
}
