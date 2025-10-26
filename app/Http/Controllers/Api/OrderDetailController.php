<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderDetailController extends Controller
{
    /**
     * @OA\Get(
     *     path="/order-details",
     *     summary="Listar detalles de pedidos",
     *     tags={"Detalles de Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista de detalles")
     * )
     */
    public function index()
    {
        try {
            $orderDetails = OrderDetail::with(['order', 'product'])->get();
            return response()->json($orderDetails);
        } catch (\Exception $e) {
            Log::error('API order details index error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener detalles de pedidos'], 500);
        }
    }

    public function create()
    {
        $orders = Order::all();
        $products = Product::all();
        return response()->json(['orders' => $orders, 'products' => $products]);
    }

    /**
     * @OA\Post(
     *     path="/order-details",
     *     summary="Crear detalle de pedido",
     *     tags={"Detalles de Pedidos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"order_id", "product_id", "quantity", "unit_price"},
     *             @OA\Property(property="order_id", type="integer"),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="unit_price", type="number")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Detalle creado")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1|max:1000',
                'unit_price' => 'required|numeric|min:0|max:999999.99',
            ]);

            $orderDetail = OrderDetail::create($validated);
            Log::info('API order detail created', ['order_detail_id' => $orderDetail->id]);

            return response()->json($orderDetail, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API order detail creation error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al crear detalle de pedido'], 500);
        }
    }

    public function show(OrderDetail $orderDetail)
    {
        $orderDetail->load(['order', 'product']);
        return response()->json($orderDetail);
    }

    public function edit(OrderDetail $orderDetail)
    {
        $orders = Order::all();
        $products = Product::all();
        return response()->json(['orderDetail' => $orderDetail, 'orders' => $orders, 'products' => $products]);
    }

    public function update(Request $request, OrderDetail $orderDetail)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'sometimes|required|exists:orders,id',
                'product_id' => 'sometimes|required|exists:products,id',
                'quantity' => 'sometimes|required|integer|min:1|max:1000',
                'unit_price' => 'sometimes|required|numeric|min:0|max:999999.99',
            ]);

            $orderDetail->update($validated);
            Log::info('API order detail updated', ['order_detail_id' => $orderDetail->id]);

            return response()->json($orderDetail);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API order detail update error', ['error' => $e->getMessage(), 'order_detail_id' => $orderDetail->id]);
            return response()->json(['error' => 'Error al actualizar detalle de pedido'], 500);
        }
    }

    public function destroy(OrderDetail $orderDetail)
    {
        try {
            $orderDetailId = $orderDetail->id;
            $orderDetail->delete();
            Log::info('API order detail deleted', ['order_detail_id' => $orderDetailId]);

            return response()->json(['message' => 'Detalle de orden eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('API order detail deletion error', ['error' => $e->getMessage(), 'order_detail_id' => $orderDetail->id]);
            return response()->json(['error' => 'Error al eliminar detalle de pedido'], 500);
        }
    }
}
