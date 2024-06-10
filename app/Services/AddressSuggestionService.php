<?php

namespace App\Services;

use GuzzleHttp\Client;

class AddressSuggestionService
{
    protected $viaCepUrl = 'https://viacep.com.br/ws/';

    public function suggestAddress($uf, $city, $addressQuery)
    {
        $client = new Client();

        ##construir a URL de consulta para o ViaCEP
        $url = $this->viaCepUrl."$uf/$city/$addressQuery/json";

        ##fazer a solicitação HTTP para o ViaCEP
        $response = $client->request('GET', $url);

        #verificar se a solicitação foi bem-sucedida
        if ($response->getStatusCode() === 200) {
            #extrair os dados da resposta
            $data = json_decode($response->getBody(), true);

            // Verificar se há resultados na resposta
            if (!empty($data)) {
                // Extrair os resultados e retorná-los como sugestões
                $suggestions = array_map(function ($item) {
                    return [
                        'cep' => $item['cep'],
                        'logradouro' => $item['logradouro'],
                        'bairro' => $item['bairro'],
                        'cidade' => $item['localidade'],
                        'uf' => $item['uf'],
                    ];
                }, $data);

                return $suggestions;
            }
        }

        // Em caso de falha na solicitação ou ausência de resultados, retornar um array vazio
        return [];
    }
}
