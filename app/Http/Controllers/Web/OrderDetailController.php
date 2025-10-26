<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }
    public function index()
    {
        try {
            $orderDetails = OrderDetail::with(['order', 'product'])->get();
            return view('order-details.index', compact('orderDetails'));
        } catch (\Exception $e) {
            Log::error('Error loading order details', ['error' => $e->getMessage()]);
            return view('order-details.index', ['orderDetails' => collect()])
                ->with('error', 'Error al cargar detalles de pedidos');
        }
    }

    public function create()
    {
        $orders = Order::all();
        $products = Product::all();
        return view('order-details.create', compact('orders', 'products'));
    }

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
            Log::info('Order detail created', ['order_detail_id' => $orderDetail->id]);

            return redirect()->route('order-details.index')
                             ->with('success', 'Detalle de orden creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error creating order detail', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return back()->withInput()->with('error', 'Error al crear detalle: ' . $e->getMessage());
        }
    }

    public function show(OrderDetail $orderDetail)
    {
        $orderDetail->load(['order', 'product']);
        return view('order-details.show', compact('orderDetail'));
    }

    public function edit(OrderDetail $orderDetail)
    {
        $orders = Order::all();
        $products = Product::all();
        return view('order-details.edit', compact('orderDetail', 'orders', 'products'));
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
            Log::info('Order detail updated', ['order_detail_id' => $orderDetail->id]);

            return redirect()->route('order-details.index')
                             ->with('success', 'Detalle de orden actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error updating order detail', ['error' => $e->getMessage(), 'order_detail_id' => $orderDetail->id]);
            return back()->withInput()->with('error', 'Error al actualizar detalle: ' . $e->getMessage());
        }
    }

    public function destroy(OrderDetail $orderDetail)
    {
        try {
            $orderDetailId = $orderDetail->id;
            $orderDetail->delete();
            Log::info('Order detail deleted', ['order_detail_id' => $orderDetailId]);

            return redirect()->route('order-details.index')
                             ->with('success', 'Detalle de orden eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error deleting order detail', ['error' => $e->getMessage(), 'order_detail_id' => $orderDetail->id]);
            return back()->with('error', 'Error al eliminar detalle: ' . $e->getMessage());
        }
    }
}
