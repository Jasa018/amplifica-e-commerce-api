@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Editar Producto</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" id="name" name="name" value="{{ $product->name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Precio</label>
            <input type="number" id="price" name="price" step="0.01" value="{{ $product->price }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700">Peso</label>
                <input type="number" id="weight" name="weight" step="0.01" value="{{ $product->weight }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="width" class="block text-sm font-medium text-gray-700">Ancho</label>
                <input type="number" id="width" name="width" step="0.01" value="{{ $product->width }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="height" class="block text-sm font-medium text-gray-700">Alto</label>
                <input type="number" id="height" name="height" step="0.01" value="{{ $product->height }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
        </div>

        <div>
            <label for="length" class="block text-sm font-medium text-gray-700">Largo</label>
            <input type="number" id="length" name="length" step="0.01" value="{{ $product->length }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div>
            <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
            <input type="number" id="stock" name="stock" value="{{ $product->stock }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="pt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">Actualizar Producto</button>
            <a href="{{ route('products.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md">Cancelar</a>
        </div>
    </form>
</div>
@endsection
