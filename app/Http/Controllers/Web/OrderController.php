<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }
    public function index(Request $request)
    {
        try {
            $query = Order::with('orderDetails.product');
            
            if ($request->filled('cliente_nombre')) {
                $query->where('cliente_nombre', 'like', '%' . $request->cliente_nombre . '%');
            }
            
            if ($request->filled('fecha_desde')) {
                $query->where('fecha', '>=', $request->fecha_desde);
            }
            
            if ($request->filled('fecha_hasta')) {
                $query->where('fecha', '<=', $request->fecha_hasta);
            }
            
            if ($request->filled('total_min')) {
                $query->where('total', '>=', $request->total_min);
            }
            
            if ($request->filled('total_max')) {
                $query->where('total', '<=', $request->total_max);
            }
            
            $orders = $query->paginate(10)->withQueryString();
            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Error loading orders', ['error' => $e->getMessage()]);
            return view('orders.index', ['orders' => collect()])
                ->with('error', 'Error al cargar pedidos');
        }
    }

    public function create()
    {
        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'total' => 'required|numeric',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'cliente_nombre' => $request->cliente_nombre,
                'fecha' => $request->fecha,
                'total' => $request->total
            ]);

            foreach ($request->products as $product) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price']
                ]);
            }

            DB::commit();
            Log::info('Order created', ['order_id' => $order->id, 'cliente' => $order->cliente_nombre]);
            return redirect()->route('orders.index')
                ->with('success', 'Pedido creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating order', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return back()->with('error', 'Error al crear el pedido: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('orderDetails.product');
        $products = Product::all();
        
        // Formatear la fecha para el input type="date"
        $order->fecha = date('Y-m-d', strtotime($order->fecha));
        
        // Asegurarnos de que los datos estén en el formato correcto y no sean nulos
        $orderDetails = $order->orderDetails->map(function($detail) {
            return [
                'product_id' => (string)$detail->product_id,
                'quantity' => (int)$detail->quantity,
                'unit_price' => (float)$detail->unit_price
            ];
        })->values()->toArray(); // Usar values() para reindexar el array
        
        // Si no hay detalles, inicializar con un item vacío
        if (empty($orderDetails)) {
            $orderDetails = [
                ['product_id' => '', 'quantity' => 1, 'unit_price' => 0]
            ];
        }
        
        return view('orders.edit', compact('order', 'products', 'orderDetails'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'total' => 'required|numeric',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $order->update([
                'cliente_nombre' => $request->cliente_nombre,
                'fecha' => $request->fecha,
                'total' => $request->total
            ]);

            // Eliminar detalles existentes
            $order->orderDetails()->delete();

            // Crear nuevos detalles
            foreach ($request->products as $product) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price']
                ]);
            }

            DB::commit();
            Log::info('Order updated', ['order_id' => $order->id, 'cliente' => $order->cliente_nombre]);
            return redirect()->route('orders.index')
                ->with('success', 'Pedido actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating order', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            return back()->with('error', 'Error al actualizar el pedido: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Order $order)
    {
        try {
            $orderInfo = ['id' => $order->id, 'cliente' => $order->cliente_nombre];
            $order->delete();
            Log::info('Order deleted', $orderInfo);

            return redirect()->route('orders.index')
                ->with('success', 'Pedido eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error deleting order', ['error' => $e->getMessage(), 'order_id' => $order->id]);
            return back()->with('error', 'Error al eliminar pedido: ' . $e->getMessage());
        }
    }
}
