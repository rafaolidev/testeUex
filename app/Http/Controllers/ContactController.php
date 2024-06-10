<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Services\CEPService;
use App\Services\GoogleMapsService;
use Illuminate\Support\Facades\Auth;
use App\Utils\CPFUtils;

class ContactController extends Controller
{
    protected $cepService;
    protected $googleMapsService;
    #construto da criação da validação de CEP e Latitude e Longitude
    public function __construct(CEPService $cepService, GoogleMapsService $googleMapsService)
    {
        $this->cepService = $cepService;
        $this->googleMapsService = $googleMapsService;
    }

    public function store(Request $request)
    {

        #validação dos campos da request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|max:255|unique:contacts,cpf',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zipcode' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        #usuario de criação e o mesmo do token
        $user = Auth::user();
        $validatedData['user_id'] = $user->id;

         ##Validar o CPF
         if (!CPFUtils::validateCPF($validatedData['cpf'])) { #Usando a função validateCPF do namespace CPFUtils
            return response()->json(['error' => 'CPF inválido'], 400);
        }

        ##Validação do CEP usando o serviço CEPService
        if (!$this->cepService->validateCEP($validatedData['zipcode'])) {
            return response()->json(['error' => 'CEP inválido'], 400);
        }
        #chamada do servico google maps
        $coordinates = $this->googleMapsService->getCoordinatesFromCEP($validatedData['zipcode']);
        ##Validação da Lat e Long usando o serviço google maps service
        if ($coordinates === null) {
            return response()->json(['error' => 'Falha ao obter coordenadas do CEP'], 400);
        }

        $validatedData['lat'] = $coordinates['lat'];
        $validatedData['long'] = $coordinates['long'];

        #validação ok  contato criado
        $contact = Contact::create($validatedData);

        return response()->json($contact, 201);
    }

    public function index(Request $request, $page)
    {
        $user = $request->user();

        ##query para listar os contatos do usuário autenticado
        $query = Contact::where('user_id', $user->id);

        #aplicar filtros do corpo da requisição, se fornecidos
        if ($request->has('filters')) {
            $filters = $request->filters;

            if (isset($filters['cpf'])) {
                $query->where('cpf', $filters['cpf']);
            }

            if (isset($filters['name'])) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            }

            #adicione outros filtros conforme necessário
        }

        ##aplicar ordenação, se fornecida na rota
        if ($request->has('sort')) {
            $sortDirection = $request->query('sort');
            if ($sortDirection === 'asc') {
                $query->orderBy('name', 'asc');
            } elseif ($sortDirection === 'desc') {
                $query->orderBy('name', 'desc');
            }
        }


        ##paginar os resultados de acordo valor da rota
        $contacts = $query->paginate($page);

        return response()->json($contacts);
    }

    public function delete(Request $request, $id)
    {
        #verificar se o contato pertence ao usuário autenticado
        $user = $request->user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        ##se o contato não for encontrado ou não pertencer ao usuário, retornar uma resposta de erro
        if (!$contact) {
            return response()->json(['error' => 'Contato não encontrado ou não pertence ao usuário'], 404);
        }

        ##deletar o contato
        $contact->delete();

        ##retornar uma resposta de sucesso
        return response()->json(['message' => 'Contato deletado com sucesso'], 200);
    }

    public function update(Request $request, $id)
    {
        ##encontrar o contato pelo ID
        $contact = Contact::findOrFail($id);

        #validar os campos recebidos
        $validatedData = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'zipcode' => ['nullable', 'string', 'max:11'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'zipcode' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'string', 'max:255'],
            'long' => ['nullable', 'string', 'max:255'],
        ]);

        ##atualizar os campos do contato
        $contact->fill($validatedData);

        ##se um CEP foi fornecido, validar o CEP
        if (!empty($validatedData['zipcode'])) {
            ##validar o CEP
            if (!$this->cepService->validateCEP($validatedData['zipcode'])) {
                return response()->json(['error' => 'CEP inválido'], 400);
            }
        }

        $contact->fill($validatedData);

        ##salvar as alterações do contato
        $contact->save();

        ##retornar uma resposta de sucesso
        return response()->json(['message' => 'Contato atualizado com sucesso'], 200);
    }
}
