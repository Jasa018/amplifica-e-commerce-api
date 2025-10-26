<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_orders_list()
    {
        Order::factory()->count(2)->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'cliente_nombre', 'fecha', 'total']
                    ]
                ]);
    }

    public function test_can_create_order_with_products()
    {
        $orderData = [
            'cliente_nombre' => 'Juan Pérez',
            'fecha' => '2024-01-15',
            'total' => 175.00
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
                ->assertJsonFragment(['cliente_nombre' => 'Juan Pérez'])
                ->assertJsonFragment(['total' => 175.00]);
    }

    public function test_can_get_single_order_with_details()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();
        
        $order->orderDetails()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => $product->price
        ]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id', 'cliente_nombre', 'total',
                        'order_details' => [
                            '*' => ['id', 'quantity', 'unit_price', 'product']
                        ]
                    ]
                ]);
    }

    public function test_can_update_order()
    {
        $order = Order::factory()->create();
        
        $updateData = [
            'cliente_nombre' => 'María García',
            'fecha' => $order->fecha->format('Y-m-d'),
            'total' => $order->total
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment(['cliente_nombre' => 'María García']);
    }

    public function test_can_delete_order()
    {
        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }

    public function test_order_validation_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(422)
                ->assertJsonStructure(['error', 'details']);
    }
}