<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AmplificaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CotizacionController extends Controller
{
    private AmplificaApiService $amplificaService;

    public function __construct(AmplificaApiService $amplificaService)
    {
        $this->amplificaService = $amplificaService;
    }

    public function cotizar(Request $request)
    {
        try {
            $validated = $request->validate([
                'comuna' => 'required|string|max:100',
                'productos' => 'required|array|min:1|max:50',
                'productos.*.weight' => 'required|numeric|min:0.01|max:1000',
                'productos.*.quantity' => 'required|integer|min:1|max:100'
            ]);

            Log::info('API cotization request', [
                'comuna' => $validated['comuna'],
                'productos_count' => count($validated['productos'])
            ]);

            $rate = $this->amplificaService->getRate(
                $validated['productos'],
                $validated['comuna']
            );

            return response()->json($rate);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API cotization error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            $statusCode = 500;
            if (str_contains($e->getMessage(), 'conexión')) {
                $statusCode = 503;
            } elseif (str_contains($e->getMessage(), 'autenticación')) {
                $statusCode = 401;
            }
            
            return response()->json([
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }
}