<?php

namespace App\Services;
use GuzzleHttp\Client;

class GoogleMapsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_MAPS_API_KEY');
    }

    public function getCoordinatesFromCEP($cep)
    {
        $client = new Client();

        #cria chamada para api google
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $cep,
                'key' => $this->apiKey,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        ##verificar se a requisição foi bem sucedida
        if ($response->getStatusCode() !== 200 || !isset($data['status']) || $data['status'] !== 'OK') {
            return null; // Retornar null em caso de falha na requisição
        }

        ##extrair as coordenadas de latitude e longitude da resposta
        $coordinates = [
            'lat' => $data['results'][0]['geometry']['location']['lat'],
            'long' => $data['results'][0]['geometry']['location']['lng'],
        ];

        return $coordinates;
    }
}
