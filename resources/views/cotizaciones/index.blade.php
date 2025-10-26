@extends('layouts.app')

@section('title', 'Cotizar Envío')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Cotizar Envío</h2>
    </div>

    <div class="p-6" x-data="cotizacionForm()">
        @if(isset($error))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                Error al cargar configuración regional: {{ $error }}
            </div>
        @endif
        
        @if(empty($regionalConfig))
            <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                No se pudo cargar la configuración regional. Verifique la conexión con la API.
            </div>
        @endif

        <form @submit.prevent="cotizar" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="region" class="block text-sm font-medium text-gray-700">Región</label>
                    <select id="region" x-model="selectedRegion" @change="updateComunas" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Seleccionar Región</option>
                        @if(is_array($regionalConfig))
                            @foreach($regionalConfig as $regionData)
                                @if(is_array($regionData) && isset($regionData['region']))
                                    <option value="{{ $regionData['region'] }}">{{ $regionData['region'] }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label for="comuna" class="block text-sm font-medium text-gray-700">Comuna</label>
                    <select id="comuna" x-model="form.comuna" :disabled="!selectedRegion" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm disabled:bg-gray-100">
                        <option value="">Seleccionar Comuna</option>
                        <template x-for="comuna in comunas" :key="comuna">
                            <option :value="comuna" x-text="comuna"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Productos para Cotizar</h3>
                <div class="grid grid-cols-12 gap-2 mb-3 font-medium text-gray-700">
                    <div class="col-span-6">Producto</div>
                    <div class="col-span-3">Cantidad</div>
                    <div class="col-span-2">Peso Total (kg)</div>
                    <div class="col-span-1">Acciones</div>
                </div>
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 mb-3">
                        <div class="col-span-6">
                            <select required class="block w-full rounded-md border-gray-300 shadow-sm"
                                    x-model="item.product_id" @change="updateWeight(index)">
                                <option value="">Seleccionar Producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-weight="{{ $product->weight }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <input type="number" class="block w-full rounded-md border-gray-300 shadow-sm"
                                   placeholder="Cantidad" min="1" required x-model="item.quantity" @input="updateWeight(index)">
                        </div>
                        <div class="col-span-2">
                            <input type="number" class="block w-full rounded-md border-gray-300 shadow-sm"
                                   placeholder="Peso" step="0.01" readonly x-model="item.totalWeight">
                        </div>
                        <div class="col-span-1">
                            <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem" 
                        class="mt-2 w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Agregar Producto
                </button>
            </div>

            <button type="submit" :disabled="loading" 
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md disabled:opacity-50">
                <span x-show="!loading">Cotizar</span>
                <span x-show="loading">Cotizando...</span>
            </button>
        </form>

        <div x-show="resultado" class="mt-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tarifas Disponibles</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="tarifa in resultado" :key="tarifa.code">
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <h4 class="font-semibold text-lg" x-text="tarifa.name || 'Sin nombre'"></h4>
                        <p class="text-gray-600" x-text="'Código: ' + (tarifa.code || 'N/A')"></p>
                        <p class="text-2xl font-bold text-green-600" x-text="'$' + (tarifa.price || 0).toLocaleString()"></p>
                        <p class="text-sm text-gray-500" x-text="(tarifa.transitDays || 0) + ' días de tránsito'"></p>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="error" class="mt-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <p x-text="error"></p>
        </div>
    </div>
</div>

<script>
const regionalConfig = @json($regionalConfig);
const products = @json($products);

function cotizacionForm() {
    return {
        form: {
            comuna: ''
        },
        selectedRegion: '',
        comunas: [],
        items: [{ product_id: '', unitWeight: 0, quantity: 1, totalWeight: 0 }],
        loading: false,
        resultado: null,
        error: null,
        
        updateComunas() {
            const region = regionalConfig.find(r => r && r.region === this.selectedRegion);
            this.comunas = region && region.comunas ? region.comunas : [];
            this.form.comuna = '';
        },
        
        addItem() {
            this.items.push({ product_id: '', unitWeight: 0, quantity: 1, totalWeight: 0 });
        },
        
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        
        updateWeight(index) {
            const selects = document.querySelectorAll('select[x-model*="product_id"]');
            const select = selects[index];
            const selectedOption = select ? select.querySelector('option:checked') : null;
            
            if (selectedOption && selectedOption.dataset.weight) {
                this.items[index].unitWeight = parseFloat(selectedOption.dataset.weight) || 0;
            }
            
            this.items[index].totalWeight = (this.items[index].unitWeight || 0) * (this.items[index].quantity || 0);
        },
        
        async cotizar() {
            this.loading = true;
            this.error = null;
            this.resultado = null;
            
            const productsArray = this.items.filter(item => 
                item.product_id && item.quantity > 0
            ).map(item => ({
                weight: parseFloat(item.totalWeight) || 0,
                quantity: parseInt(item.quantity) || 0
            }));
            
            if (productsArray.length === 0) {
                this.error = 'Debe seleccionar al menos un producto';
                this.loading = false;
                return;
            }
            
            try {
                const response = await fetch('/cotizar-envio', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        comuna: this.form.comuna,
                        products: productsArray
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    this.resultado = data;
                } else {
                    this.error = data.error || 'Error en la cotización';
                }
            } catch (e) {
                console.error('Error:', e);
                this.error = 'Error de conexión: ' + e.message;
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
<script src="//unpkg.com/alpinejs" defer></script>
@endsection