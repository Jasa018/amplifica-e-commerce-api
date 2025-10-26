@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="space-y-2">
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">ID:</span> {{ $product->id }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">Precio:</span> ${{ number_format($product->price, 2) }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">Stock:</span> {{ $product->stock }}</p>
        </div>

        <div class="space-y-2">
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">Peso:</span> {{ $product->weight }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">Ancho:</span> {{ $product->width }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">Alto:</span> {{ $product->height }}</p>
            <p class="text-sm text-gray-600"><span class="font-medium text-gray-800">Largo:</span> {{ $product->length }}</p>
        </div>
    </div>

    <div class="mt-6 text-sm text-gray-600">
        <p><span class="font-medium text-gray-800">Creado:</span> {{ $product->created_at->format('d/m/Y H:i') }}</p>
        <p><span class="font-medium text-gray-800">Actualizado:</span> {{ $product->updated_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="mt-6">
        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">Volver a la lista</a>
    </div>
</div>
@endsection
