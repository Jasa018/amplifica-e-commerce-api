<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_products_list()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'name', 'price', 'weight', 'stock']
                    ]
                ]);
    }

    public function test_can_create_product()
    {
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'weight' => 1.5,
            'width' => 10,
            'height' => 5,
            'length' => 15,
            'stock' => 100
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
                ->assertJsonFragment(['name' => 'Test Product']);
        
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_can_get_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                ->assertJsonFragment(['id' => $product->id]);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create();
        
        $updateData = [
            'name' => 'Updated Product Name',
            'price' => $product->price,
            'weight' => $product->weight,
            'width' => $product->width,
            'height' => $product->height,
            'length' => $product->length,
            'stock' => $product->stock
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonFragment(['name' => 'Updated Product Name']);
    }

    public function test_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_product_validation_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
                ->assertJsonStructure(['error', 'details']);
    }
}