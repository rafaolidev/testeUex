<?php

namespace App\Http\Controllers;

use App\Services\AddressSuggestionService;
use Illuminate\Http\Request;

class AddressSuggestionController extends Controller
{

    protected $addressSuggestionService;

    public function __construct(AddressSuggestionService $addressSuggestionService)
    {
        $this->addressSuggestionService = $addressSuggestionService;
    }

    public function suggest(Request $request)
    {
        ##obter os dados da requisição
        $validatedData = $request->validate([
            'uf' => ['required', 'string', 'max:2'],
            'city' => ['required', 'string', 'max:255'],
            'address_query' => ['required','nullable', 'string', 'max:255'],
        ]);

        $uf = $validatedData['uf'];
        $city = $validatedData['city'];
        $addressQuery = $validatedData['address_query'];

        ##chamar o serviço de sugestão de endereço
        $suggestions = $this->addressSuggestionService->suggestAddress($uf, $city, $addressQuery);

        ##retornar as sugestões como JSON
        return response()->json($suggestions);
    }
}
