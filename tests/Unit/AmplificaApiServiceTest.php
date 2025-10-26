<?php

namespace Tests\Unit;

use App\Services\AmplificaApiService;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AmplificaApiServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AmplificaApiService();
    }

    public function test_service_can_be_instantiated()
    {
        $this->assertInstanceOf(AmplificaApiService::class, $this->service);
    }

    public function test_get_token_returns_string()
    {
        Http::fake([
            'https://postulaciones.amplifica.io/auth' => Http::response([
                'token' => 'fake-token-123'
            ], 200)
        ]);

        $token = $this->service->getToken();
        
        $this->assertIsString($token);
        $this->assertEquals('fake-token-123', $token);
    }

    public function test_cotizar_with_valid_data()
    {
        Http::fake([
            'https://postulaciones.amplifica.io/auth' => Http::response([
                'token' => 'fake-token-123'
            ], 200),
            'https://postulaciones.amplifica.io/cotizar' => Http::response([
                'success' => true,
                'data' => [
                    'tarifas' => [
                        ['nombre' => 'Express', 'precio' => 5000],
                        ['nombre' => 'Estándar', 'precio' => 3000]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->service->cotizar([
            'region_origen' => 'Metropolitana',
            'comuna_origen' => 'Santiago',
            'region_destino' => 'Valparaíso',
            'comuna_destino' => 'Viña del Mar',
            'peso' => 2.5
        ]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_token_is_cached()
    {
        Http::fake([
            'https://postulaciones.amplifica.io/auth' => Http::response([
                'token' => 'cached-token-456'
            ], 200)
        ]);

        // Primera llamada
        $token1 = $this->service->getToken();
        
        // Segunda llamada debería usar cache
        $token2 = $this->service->getToken();
        
        $this->assertEquals($token1, $token2);
        $this->assertTrue(Cache::has('amplifica_token'));
    }
}