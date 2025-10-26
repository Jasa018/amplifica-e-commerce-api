@extends('layouts.app')

@section('title', 'Cotizar Envío')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Cotizar Envío</h2>
        <button onclick="abrirHistorial()" 
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Historial
        </button>
    </div>

    <div class="p-6" x-data="cotizacionData()">
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
        
        <!-- Modal Historial -->
        <div id="modalHistorial" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" onclick="this.classList.add('hidden')">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Historial de Cotizaciones</h3>
                    <button onclick="document.getElementById('modalHistorial').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div id="historialContent" class="text-center py-8 text-gray-500">
                    <p>Cargando historial...</p>
                </div>
            </div>
        </div>
        
        <!-- Modal Detalle -->
        <div x-show="showDetalle" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="showDetalle = false">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white" @click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Detalle de Cotización</h3>
                    <button @click="showDetalle = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div x-show="detalleCotizacion">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="font-semibold">Destino:</p>
                            <p x-text="detalleCotizacion?.comuna_destino"></p>
                        </div>
                        <div>
                            <p class="font-semibold">Peso Total:</p>
                            <p x-text="(detalleCotizacion?.peso_total || 0) + ' kg'"></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <p class="font-semibold mb-2">Productos:</p>
                        <template x-for="producto in (detalleCotizacion?.productos || [])" :key="producto">
                            <div class="bg-gray-50 p-2 rounded mb-2">
                                <p x-text="'Peso: ' + producto.weight + ' kg - Cantidad: ' + producto.quantity"></p>
                            </div>
                        </template>
                    </div>
                    
                    <div>
                        <p class="font-semibold mb-2">Tarifas:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <template x-for="tarifa in (detalleCotizacion?.tarifas || [])" :key="tarifa">
                                <div class="bg-blue-50 p-3 rounded">
                                    <p class="font-medium" x-text="tarifa.name || 'Sin nombre'"></p>
                                    <p class="text-lg font-bold text-blue-600" x-text="'$' + (tarifa.price || 0).toLocaleString()"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const regionalConfig = @json($regionalConfig);
const products = @json($products);

function cotizacionData() {
    return {
        showHistorial: false,
        loadingHistorial: false,
        historial: [],
        showDetalle: false,
        detalleCotizacion: null,
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
                        region: this.selectedRegion,
                        comuna: this.form.comuna,
                        products: productsArray.map((item, index) => ({
                            ...item,
                            product_id: this.items[index].product_id
                        }))
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
        },
        
        async cargarHistorial() {
            this.loadingHistorial = true;
            try {
                const response = await fetch('/api/historial-cotizaciones', {
                    headers: {
                        'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.historial = await response.json();
                } else {
                    console.error('Error cargando historial');
                }
            } catch (e) {
                console.error('Error:', e);
            } finally {
                this.loadingHistorial = false;
            }
        },
        
        async verDetalle(id) {
            try {
                const response = await fetch(`/api/historial-cotizaciones/${id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.detalleCotizacion = await response.json();
                    this.showDetalle = true;
                }
            } catch (e) {
                console.error('Error:', e);
            }
        },
        
        async eliminarCotizacion(id) {
            if (confirm('¿Está seguro de eliminar esta cotización?')) {
                try {
                    const response = await fetch(`/api/historial-cotizaciones/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
                            'Accept': 'application/json'
                        }
                    });
                    
                    if (response.ok) {
                        this.historial = this.historial.filter(c => c.id !== id);
                    }
                } catch (e) {
                    console.error('Error:', e);
                }
            }
        },
        
        async cargarHistorialWeb() {
            this.loadingHistorial = true;
            try {
                const response = await fetch('/historial-cotizaciones', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.historial = await response.json();
                } else {
                    console.error('Error cargando historial');
                }
            } catch (e) {
                console.error('Error:', e);
            } finally {
                this.loadingHistorial = false;
            }
        },
        
        async verDetalleWeb(id) {
            try {
                const response = await fetch(`/historial-cotizaciones/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.detalleCotizacion = await response.json();
                    this.showDetalle = true;
                }
            } catch (e) {
                console.error('Error:', e);
            }
        },
        
        async eliminarCotizacionWeb(id) {
            if (confirm('¿Está seguro de eliminar esta cotización?')) {
                try {
                    const response = await fetch(`/historial-cotizaciones/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    if (response.ok) {
                        this.historial = this.historial.filter(c => c.id !== id);
                    }
                } catch (e) {
                    console.error('Error:', e);
                }
            }
        },
        
        toggleHistorial() {
            this.showHistorial = !this.showHistorial;
            if (this.showHistorial) {
                this.cargarHistorialWeb();
            }
        }
    }
}

// Función para abrir historial
async function abrirHistorial() {
    document.getElementById('modalHistorial').classList.remove('hidden');
    
    try {
        const response = await fetch('/historial-cotizaciones', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const historial = await response.json();
            mostrarHistorial(historial);
        } else {
            document.getElementById('historialContent').innerHTML = '<p class="text-red-500">Error al cargar historial</p>';
        }
    } catch (e) {
        console.error('Error:', e);
        document.getElementById('historialContent').innerHTML = '<p class="text-red-500">Error de conexión</p>';
    }
}

// Función para mostrar historial
function mostrarHistorial(historial) {
    const content = document.getElementById('historialContent');
    
    if (historial.length === 0) {
        content.innerHTML = '<p class="text-gray-500">No hay cotizaciones en el historial</p>';
        return;
    }
    
    let html = '<div class="max-h-96 overflow-y-auto">';
    historial.forEach(cotizacion => {
        // Productos
        let productosHtml = '';
        if (cotizacion.productos && cotizacion.productos.length > 0) {
            productosHtml = cotizacion.productos.map(p => 
                `<span class="text-xs bg-gray-100 px-2 py-1 rounded">${p.name || 'Producto'}: ${p.weight}kg x${p.quantity}</span>`
            ).join(' ');
        }
        
        // Tarifas
        let tarifasHtml = '';
        if (cotizacion.tarifas && cotizacion.tarifas.length > 0) {
            tarifasHtml = cotizacion.tarifas.map(t => 
                `<span class="text-xs bg-blue-100 px-2 py-1 rounded">${t.name || 'Tarifa'}: $${(t.price || 0).toLocaleString()}</span>`
            ).join(' ');
        }
        
        html += `
            <div id="cotizacion-${cotizacion.id}" class="border border-gray-200 rounded-lg p-4 mb-3">
                <div class="space-y-2">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${cotizacion.region || 'Región'} - ${cotizacion.comuna}</p>
                        </div>
                        <button onclick="eliminarCotizacion(${cotizacion.id})" class="text-red-600 hover:text-red-800 text-sm px-2 py-1 border border-red-300 rounded">Eliminar</button>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-700">Productos:</p>
                        <div class="flex flex-wrap gap-1 mt-1">${productosHtml || '<span class="text-xs text-gray-400">Sin productos</span>'}</div>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-700">Peso Total: <span class="font-bold">${cotizacion.peso_total} kg</span></p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-medium text-gray-700">Tarifas Disponibles:</p>
                        <div class="flex flex-wrap gap-1 mt-1">${tarifasHtml || '<span class="text-xs text-gray-400">Sin tarifas</span>'}</div>
                    </div>
                    
                    <div class="text-xs text-gray-500 pt-2 border-t">
                        Fecha: ${new Date(cotizacion.created_at).toLocaleString()}
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    content.innerHTML = html;
}

// Función para eliminar cotización
async function eliminarCotizacion(id) {
    if (!confirm('¿Está seguro de eliminar esta cotización?')) {
        return;
    }
    
    try {
        const response = await fetch(`/historial-cotizaciones/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            // Remover elemento del DOM
            const elemento = document.getElementById(`cotizacion-${id}`);
            if (elemento) {
                elemento.remove();
            }
            
            // Verificar si quedan elementos
            const content = document.getElementById('historialContent');
            const cotizaciones = content.querySelectorAll('[id^="cotizacion-"]');
            if (cotizaciones.length === 0) {
                content.innerHTML = '<p class="text-gray-500">No hay cotizaciones en el historial</p>';
            }
        } else {
            alert('Error al eliminar la cotización');
        }
    } catch (e) {
        console.error('Error:', e);
        alert('Error de conexión al eliminar');
    }
}
</script>

@endsection