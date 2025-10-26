<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created()
    {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
            'weight' => 1.5,
            'stock' => 100
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'weight' => 1.5,
            'stock' => 100
        ]);
    }

    public function test_product_has_required_attributes()
    {
        $product = Product::factory()->create();

        $this->assertNotNull($product->name);
        $this->assertNotNull($product->price);
        $this->assertNotNull($product->weight);
        $this->assertNotNull($product->stock);
    }

    public function test_product_price_is_numeric()
    {
        $product = Product::factory()->create(['price' => 99.99]);
        
        $this->assertIsNumeric($product->price);
        $this->assertEquals(99.99, $product->price);
    }

    public function test_product_weight_is_numeric()
    {
        $product = Product::factory()->create(['weight' => 2.5]);
        
        $this->assertIsNumeric($product->weight);
        $this->assertEquals(2.5, $product->weight);
    }
}