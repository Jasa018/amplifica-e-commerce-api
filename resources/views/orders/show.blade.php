@extends('layouts.app')

@section('title', 'Detalle del Pedido')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Detalle del Pedido</h2>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información del Pedido -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Información General</h3>
            <div class="space-y-3">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">ID del Pedido:</span>
                    <span class="ml-2 text-gray-900">{{ $order->id }}</span>
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Cliente:</span>
                    <span class="ml-2 text-gray-900">{{ $order->cliente_nombre }}</span>
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Fecha:</span>
                    <span class="ml-2 text-gray-900">{{ $order->fecha }}</span>
                </p>
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Total del Pedido:</span>
                    <span class="ml-2 text-gray-900">${{ number_format($order->total, 2) }}</span>
                </p>
            </div>
        </div>

        <!-- Productos del Pedido -->
        <div class="bg-white border rounded-lg overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Productos en el Pedido</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->orderDetails as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->product->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($detail->unit_price, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($detail->quantity * $detail->unit_price, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <a href="{{ route('orders.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                Volver a la Lista
            </a>
            <a href="{{ route('orders.edit', $order->id) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
                Editar Pedido
            </a>
        </div>
    </div>
</div>
@endsection
