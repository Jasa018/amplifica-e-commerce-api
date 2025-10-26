<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_can_be_created()
    {
        $order = Order::factory()->create([
            'cliente_nombre' => 'Juan Pérez',
            'total' => 199.98
        ]);

        $this->assertDatabaseHas('orders', [
            'cliente_nombre' => 'Juan Pérez',
            'total' => 199.98
        ]);
    }

    public function test_order_has_many_order_details()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();
        
        OrderDetail::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 99.99
        ]);

        $this->assertCount(1, $order->orderDetails);
        $this->assertEquals(2, $order->orderDetails->first()->quantity);
    }

    public function test_order_total_is_numeric()
    {
        $order = Order::factory()->create(['total' => 299.99]);
        
        $this->assertIsNumeric($order->total);
        $this->assertEquals(299.99, $order->total);
    }
}