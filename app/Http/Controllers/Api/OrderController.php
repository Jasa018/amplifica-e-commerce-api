<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::all();
            return response()->json($orders);
        } catch (\Exception $e) {
            Log::error('API orders index error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener pedidos'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente_nombre' => 'required|string|max:255',
                'fecha' => 'required|date',
                'total' => 'required|numeric|min:0|max:999999.99',
            ]);

            $order = Order::create($validated);
            Log::info('API order created', ['order_id' => $order->id]);

            return response()->json($order, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API order creation error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al crear pedido'], 500);
        }
    }

    public function show(Order $order)
    {
        try {
            return response()->json($order);
        } catch (\Exception $e) {
            Log::error('API order show error', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            return response()->json(['error' => 'Error al obtener pedido'], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'cliente_nombre' => 'required|string|max:255',
                'fecha' => 'required|date',
                'total' => 'required|numeric|min:0|max:999999.99',
            ]);

            $order->update($validated);
            Log::info('API order updated', ['order_id' => $order->id]);

            return response()->json($order);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API order update error', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            return response()->json(['error' => 'Error al actualizar pedido'], 500);
        }
    }

    public function destroy(Order $order)
    {
        try {
            $orderId = $order->id;
            $order->delete();
            Log::info('API order deleted', ['order_id' => $orderId]);

            return response()->json(['message' => 'Pedido eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('API order deletion error', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            return response()->json(['error' => 'Error al eliminar pedido'], 500);
        }
    }
}
