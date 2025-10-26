<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AmplificaApiService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            
            return view('cotizaciones.index', [
                'regionalConfig' => $regionalConfig ?? [],
                'products' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading cotizaciones page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('cotizaciones.index', [
                'regionalConfig' => [],
                'products' => Product::all(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function cotizar(Request $request)
    {
        try {
            $validated = $request->validate([
                'comuna' => 'required|string|max:100',
                'products' => 'required|array|min:1|max:50',
                'products.*.weight' => 'required|numeric|min:0.01|max:1000',
                'products.*.quantity' => 'required|integer|min:1|max:100'
            ]);

            Log::info('Cotization request', [
                'comuna' => $validated['comuna'],
                'products_count' => count($validated['products'])
            ]);

            $rate = $this->amplificaService->getRate(
                $validated['products'],
                $validated['comuna']
            );

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
}