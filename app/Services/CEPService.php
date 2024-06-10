<?php

namespace App\Services;

class CEPService
{
    ##valida o CEP para uso na api google para pegar a lat e long
    public function validateCEP($cep)
    {
        ##remover caracteres não numéricos do CEP
        $cep = preg_replace('/[^0-9]/', '', $cep);

        #verificar se o CEP possui 8 dígitos
        if (strlen($cep) !== 8) {
            return false;
        }

        ##fazer requisição para o serviço ViaCEP
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $response = @file_get_contents($url);

        #verificar se a requisição foi bem sucedida
        if ($response === false) {
            return false; // Falha na requisição
        }

        #analisar a resposta JSON
        $data = json_decode($response, true);

        #verificar se o CEP é válido
        if (isset($data['erro'])) {
            return false; ##CEP inválido
        }

        return true; ##CEP válido
    }
}
