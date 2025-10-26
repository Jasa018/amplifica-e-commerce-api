<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

class CotizacionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_cotizacion_with_valid_data()
    {
        Http::fake([
            'https://postulaciones.amplifica.io/auth' => Http::response([
                'token' => 'fake-token-123'
            ], 200),
            'https://postulaciones.amplifica.io/getRate' => Http::response([
                ['name' => 'Express', 'price' => 5000],
                ['name' => 'Estándar', 'price' => 3000]
            ], 200)
        ]);

        $cotizacionData = [
            'comuna' => 'Viña del Mar',
            'productos' => [
                ['weight' => 2.0, 'quantity' => 2]
            ]
        ];

        $response = $this->postJson('/api/cotizar-envio', $cotizacionData);

        $response->assertStatus(200);
    }

    public function test_cotizacion_validation_fails_with_missing_data()
    {
        $response = $this->postJson('/api/cotizar-envio', []);

        $response->assertStatus(422)
                ->assertJsonStructure(['error', 'details']);
    }

    public function test_cotizacion_calculates_correct_total_weight()
    {
        Http::fake([
            'https://postulaciones.amplifica.io/auth' => Http::response([
                'token' => 'fake-token-123'
            ], 200),
            'https://postulaciones.amplifica.io/getRate' => Http::response([
                ['name' => 'Express', 'price' => 5000]
            ], 200)
        ]);

        $cotizacionData = [
            'comuna' => 'Viña del Mar',
            'productos' => [
                ['weight' => 1.5, 'quantity' => 2], // 1.5 * 2 = 3.0
                ['weight' => 2.0, 'quantity' => 1]  // 2.0 * 1 = 2.0
            ]
        ];

        $response = $this->postJson('/api/cotizar-envio', $cotizacionData);

        $response->assertStatus(200);
    }

    public function test_cotizacion_handles_api_error()
    {
        Http::fake([
            'https://postulaciones.amplifica.io/auth' => Http::response([
                'token' => 'fake-token-123'
            ], 200),
            'https://postulaciones.amplifica.io/cotizar' => Http::response([
                'error' => 'API Error'
            ], 500)
        ]);

        $product = Product::factory()->create();

        $cotizacionData = [
            'comuna' => 'Viña del Mar',
            'productos' => [
                ['weight' => 1.0, 'quantity' => 1]
            ]
        ];

        $response = $this->postJson('/api/cotizar-envio', $cotizacionData);

        $response->assertStatus(500)
                ->assertJsonStructure(['error']);
    }
}