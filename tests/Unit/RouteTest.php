<?php

namespace Tests\Unit;

use App\Http\Controllers\AddressSuggestionController;
use Illuminate\Http\Request;

use Tests\TestCase;

class RouteTest extends TestCase
{
    /**
     * Testa a rota de login.
     *
     * @return void
     */
    public function testLoginRoute()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'exemplo@email.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Testa a rota de sugestão de endereço.
     *
     * @return void
     */
    public function testAddressSuggestionRoute()
    {
        ##Faz a chamada de login
        $response = $this->postJson('/api/login', [
            'email' => 'exemplo@email.com',
            'password' => '12345678',
        ]);

        ##Extrai o token da resposta
        $token = $response->json('token');

        ##Usa o token na próxima chamada de teste
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/address/suggest', [
            'uf' => 'SP',
            'city' => 'São Paulo',
            'address_query' => 'Avenida Paulista',
        ]);

        $response->assertStatus(200);
        $this->assertIsArray($response->json());
    }
}
