<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AmplificaApiService;
use App\Models\Product;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CotizacionController extends Controller
{
    private AmplificaApiService $amplificaService;

    public function __construct(AmplificaApiService $amplificaService)
    {
        $this->amplificaService = $amplificaService;
    }

    public function index()
    {
        try {
            $regionalConfig = $this->amplificaService->getRegionalConfig();
            $products = Product::all();
            $historial = Cotizacion::where('user_id', Auth::id())
                                  ->orderBy('created_at', 'desc')
                                  ->limit(10)
                                  ->get();
            
            return view('cotizaciones.index', [
                'regionalConfig' => $regionalConfig ?? [],
                'products' => $products,
                'historial' => $historial
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading cotizaciones page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('cotizaciones.index', [
                'regionalConfig' => [],
                'products' => Product::all(),
                'historial' => collect(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function cotizar(Request $request)
    {
        try {
            $validated = $request->validate([
                'region' => 'nullable|string|max:100',
                'comuna' => 'required|string|max:100',
                'products' => 'required|array|min:1|max:50',
                'products.*.weight' => 'required|numeric|min:0.01|max:1000',
                'products.*.quantity' => 'required|integer|min:1|max:100',
                'products.*.product_id' => 'nullable|integer|exists:products,id'
            ]);

            Log::info('Cotization request', [
                'comuna' => $validated['comuna'],
                'products_count' => count($validated['products'])
            ]);

            $pesoTotal = collect($validated['products'])->sum(function ($product) {
                return $product['weight'] * $product['quantity'];
            });

            $rate = $this->amplificaService->getRate(
                $validated['products'],
                $validated['comuna']
            );

            // Guardar cotización en historial con nombres de productos
            if (Auth::check()) {
                // Obtener nombres de productos
                $productosConNombres = collect($validated['products'])->map(function ($product) {
                    $productName = 'Producto desconocido';
                    if (isset($product['product_id'])) {
                        $productModel = Product::find($product['product_id']);
                        $productName = $productModel ? $productModel->name : 'Producto no encontrado';
                    }
                    
                    return [
                        'product_id' => $product['product_id'] ?? null,
                        'name' => $productName,
                        'weight' => $product['weight'],
                        'quantity' => $product['quantity']
                    ];
                });
                
                Cotizacion::create([
                    'user_id' => Auth::id(),
                    'region' => $validated['region'] ?? 'No especificada',
                    'comuna' => $validated['comuna'],
                    'peso_total' => $pesoTotal,
                    'productos' => $productosConNombres,
                    'tarifas' => $rate
                ]);
            }

            return response()->json($rate);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Cotization error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            // Determinar código de estado basado en el tipo de error
            $statusCode = 500;
            if (str_contains($e->getMessage(), 'conexión')) {
                $statusCode = 503;
            } elseif (str_contains($e->getMessage(), 'autenticación')) {
                $statusCode = 401;
            } elseif (str_contains($e->getMessage(), 'validación')) {
                $statusCode = 422;
            }
            
            return response()->json([
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
    
    public function historial(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            
            $historial = Cotizacion::where('user_id', Auth::id())
                                  ->orderBy('created_at', 'desc')
                                  ->limit($limit)
                                  ->get();

            if ($request->expectsJson()) {
                return response()->json($historial);
            }
            
            return view('cotizaciones.historial', compact('historial'));
        } catch (\Exception $e) {
            Log::error('Error getting cotizaciones history', ['error' => $e->getMessage()]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al obtener historial'], 500);
            }
            
            return back()->with('error', 'Error al obtener historial');
        }
    }
    
    public function detalle($id, Request $request)
    {
        try {
            $cotizacion = Cotizacion::where('user_id', Auth::id())
                                   ->findOrFail($id);

            if ($request->expectsJson()) {
                return response()->json($cotizacion);
            }
            
            return view('cotizaciones.detalle', compact('cotizacion'));
        } catch (\Exception $e) {
            Log::error('Error getting cotizacion detail', ['error' => $e->getMessage(), 'id' => $id]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Cotización no encontrada'], 404);
            }
            
            return back()->with('error', 'Cotización no encontrada');
        }
    }
    
    public function eliminar($id, Request $request)
    {
        try {
            $cotizacion = Cotizacion::where('user_id', Auth::id())
                                   ->findOrFail($id);
            
            $cotizacion->delete();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Cotización eliminada del historial']);
            }
            
            return back()->with('success', 'Cotización eliminada del historial');
        } catch (\Exception $e) {
            Log::error('Error deleting cotizacion', ['error' => $e->getMessage(), 'id' => $id]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error al eliminar cotización'], 500);
            }
            
            return back()->with('error', 'Error al eliminar cotización');
        }
    }
}