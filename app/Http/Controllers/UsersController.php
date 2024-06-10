<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    #criação de usuário com senha criptografada
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'cpf' => 'required|string|max:11|unique:users',
            'password' => 'required|string|min:8',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zipcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    #deletar user com validação  de senha
    public function destroyWithPassword(Request $request)
    {
        $authUser = Auth::user();
        $user = User::findOrFail($authUser->id);

        #verificar se a senha fornecida corresponde à senha do usuário
        if (Hash::check($request->input('password'), $user->password)) {
            ##se a senha estiver correta, exclua o usuário
            $user->delete();
            return response()->json(['message' => 'Usuário excluído com sucesso.']);
        } else {
            ##se a senha estiver incorreta, retorne um erro
            return response()->json(['error' => 'Senha incorreta.'], 403);
        }
    }
}
