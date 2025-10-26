<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderDetailController extends Controller
{
    public function index()
    {
        return response()->json(OrderDetail::with(['order', 'product'])->get());
    }

    public function create()
    {
        $orders = Order::all();
        $products = Product::all();
        return response()->json(['orders' => $orders, 'products' => $products]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $orderDetail = OrderDetail::create($request->all());

        return response()->json($orderDetail, 201);
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
        $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
        ]);

        $orderDetail->update($request->all());

        return response()->json($orderDetail);
    }

    public function destroy(OrderDetail $orderDetail)
    {
        $orderDetail->delete();

        return response()->json(['message' => 'Detalle de orden eliminado exitosamente'], 200);
    }
}
