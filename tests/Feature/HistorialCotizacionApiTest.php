<?php

namespace Tests\Feature;

use App\Models\Cotizacion;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class HistorialCotizacionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_historial_cotizaciones()
    {
        Cotizacion::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/historial-cotizaciones');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'region', 'comuna', 'peso_total', 'productos', 'tarifas']
                    ]
                ]);
    }

    public function test_can_get_single_cotizacion()
    {
        $cotizacion = Cotizacion::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/historial-cotizaciones/{$cotizacion->id}");

        $response->assertStatus(200)
                ->assertJsonFragment(['id' => $cotizacion->id]);
    }

    public function test_can_delete_cotizacion()
    {
        $cotizacion = Cotizacion::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/historial-cotizaciones/{$cotizacion->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('cotizaciones', ['id' => $cotizacion->id]);
    }

    public function test_cannot_access_other_user_cotizaciones()
    {
        $otherUser = User::factory()->create();
        $cotizacion = Cotizacion::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/historial-cotizaciones/{$cotizacion->id}");

        $response->assertStatus(404);
    }


}