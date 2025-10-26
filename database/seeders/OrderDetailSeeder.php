<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderDetail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        if ($orders->isEmpty() || $products->isEmpty()) {
            $this->command->info('No orders or products found, seeding order details skipped.');
            return;
        }

        foreach ($orders as $order) {
            $productsToAttach = $products->random(rand(1, 3));
            foreach ($productsToAttach as $product) {
                OrderDetail::factory()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                    'unit_price' => $product->price,
                ]);
            }
        }

        // Recalculate and update each order total based on its order details
        foreach ($orders as $order) {
            $total = $order->orderDetails()->sum(DB::raw('quantity * unit_price'));
            $order->update(['total' => $total]);
        }
    }
}
