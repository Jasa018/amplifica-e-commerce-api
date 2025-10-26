@extends('layouts.app')

@section('title', 'Crear Pedido')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Crear Nuevo Pedido</h2>
    </div>

    @if ($errors->any())
        <div class="p-4 mb-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" class="p-6" x-data="orderForm()" x-init="items.forEach((_,i)=>updatePrice(i)); calculateTotal()">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Información del Pedido -->
            <div class="space-y-4">
                <div>
                    <label for="cliente_nombre" class="block text-sm font-medium text-gray-700">Cliente</label>
                    <input type="text" id="cliente_nombre" name="cliente_nombre" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
                    <input type="date" id="fecha" name="fecha" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <!-- Productos del Pedido -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Productos del Pedido</h3>
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 mb-3">
                        <div class="col-span-5">
                            <select :name="'products['+index+'][product_id]'" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    x-model="item.product_id"
                                    @change="updatePrice(index)">
                                <option value="">Seleccionar Producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <input type="number" :name="'products['+index+'][quantity]'" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Cantidad" min="1" required
                                   x-model="item.quantity"
                                   @input="calculateTotal()">
                        </div>
                        <div class="col-span-3">
                            <input type="number" :name="'products['+index+'][unit_price]'" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Precio" step="0.01" required readonly
                                   x-model="item.unit_price">
                        </div>
                        <div class="col-span-1">
                            <button type="button" @click="removeItem(index)"
                                    class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem"
                        class="mt-2 w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-200">
                    Agregar Producto
                </button>
            </div>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-4">
            <div class="flex justify-between items-center">
                <div class="text-xl font-semibold text-gray-900">
                    Total: $<span x-text="total.toFixed(2)"></span>
                    <input type="hidden" name="total" x-model="total">
                </div>
                <div class="space-x-3">
                    <a href="{{ route('orders.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                        Guardar Pedido
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="//unpkg.com/alpinejs" defer></script>
<script>
function orderForm() {
    return {
        items: [{
            product_id: '',
            quantity: 1,
            unit_price: 0
        }],
        total: 0,
        addItem() {
            this.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0
            });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
                this.calculateTotal();
            }
        },
        updatePrice(index) {
            const product = document.querySelector(`select[name="products[${index}][product_id]"] option:checked`);
            if (product) {
                this.items[index].unit_price = parseFloat(product.dataset.price) || 0;
                this.calculateTotal();
            }
        },
        calculateTotal() {
            this.total = this.items.reduce((sum, item) => {
                return sum + (parseFloat(item.unit_price) * parseInt(item.quantity) || 0);
            }, 0);
        }
    }
}
</script>
@endsection
