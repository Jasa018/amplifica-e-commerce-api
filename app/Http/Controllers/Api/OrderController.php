<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="Listar pedidos",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista de pedidos")
     * )
     */
    public function index()
    {
        try {
            $orders = Order::with('orderDetails.product')->get();
            return OrderResource::collection($orders);
        } catch (\Exception $e) {
            Log::error('API orders index error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener pedidos'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Crear pedido",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cliente_nombre", "fecha", "total"},
     *             @OA\Property(property="cliente_nombre", type="string"),
     *             @OA\Property(property="fecha", type="string", format="date"),
     *             @OA\Property(property="total", type="number")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Pedido creado")
     * )
     */
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

            return new OrderResource($order);
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

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     summary="Obtener pedido",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Pedido encontrado")
     * )
     */
    public function show(Order $order)
    {
        try {
            $order->load('orderDetails.product');
            return new OrderResource($order);
        } catch (\Exception $e) {
            Log::error('API order show error', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            return response()->json(['error' => 'Error al obtener pedido'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}",
     *     summary="Actualizar pedido",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Pedido actualizado")
     * )
     */
    public function update(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'cliente_nombre' => 'required|string|max:255',
                'fecha' => 'required|date',
                'total' => 'required|numeric|min:0|max:999999.99',
            ]);

            $order->update($validated);
            $order->load('orderDetails.product');
            Log::info('API order updated', ['order_id' => $order->id]);

            return new OrderResource($order);
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

    /**
     * @OA\Delete(
     *     path="/orders/{id}",
     *     summary="Eliminar pedido",
     *     tags={"Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Pedido eliminado")
     * )
     */
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
