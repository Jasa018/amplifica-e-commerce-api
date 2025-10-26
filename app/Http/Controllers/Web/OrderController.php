<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }
    public function index()
    {
        $orders = Order::all();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'total' => 'required|numeric',
        ]);

        Order::create($request->all());

        return redirect()->route('orders.index')
            ->with('success', 'Pedido creado exitosamente.');
    }

    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'cliente_nombre' => 'required|string|max:255',
            'fecha' => 'required|date',
            'total' => 'required|numeric',
        ]);

        $order->update($request->all());

        return redirect()->route('orders.index')
            ->with('success', 'Pedido actualizado exitosamente.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Pedido eliminado exitosamente.');
    }
}
