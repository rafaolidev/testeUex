<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ViaCepController extends Controller
{
    public function consultaCep($cep)
    {
        $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        } else {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }
    }
}
