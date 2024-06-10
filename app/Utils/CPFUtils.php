<?php

namespace App\Utils;

class CPFUtils
{
    public static function validateCPF($cpf) {
        ##Remover caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        ##Verificar se o CPF tem 11 dígitos
        if (strlen($cpf) !== 11) {
            return false;
        }

        #Verificar se todos os dígitos são iguais
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        #Calcular o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }
        $digit1 = 11 - ($sum % 11);
        if ($digit1 >= 10) {
            $digit1 = 0;
        }

        ##Verificar o primeiro dígito verificador
        if (intval($cpf[9]) !== $digit1) {
            return false;
        }

        ##Calcular o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }
        $digit2 = 11 - ($sum % 11);
        if ($digit2 >= 10) {
            $digit2 = 0;
        }

        ##Verificar o segundo dígito verificador
        if (intval($cpf[10]) !== $digit2) {
            return false;
        }

        ##CPF válido
        return true;
    }
}
