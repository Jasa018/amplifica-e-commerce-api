<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AmplificaApiService;
use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CotizacionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/cotizar",
     *     summary="Cotizar envío",
     *     tags={"Cotizaciones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comuna", "productos"},
     *             @OA\Property(property="comuna", type="string", example="Providencia"),
     *             @OA\Property(
     *                 property="productos",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="weight", type="number", example=1.5),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarifas de envío",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="name", type="string", example="Tarifa Express"),
     *                 @OA\Property(property="code", type="string", example="EXP"),
     *                 @OA\Property(property="price", type="number", example=4990),
     *                 @OA\Property(property="transitDays", type="integer", example=0)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Error de validación"),
     *     @OA\Response(response=503, description="Error de conexión")
     * )
     */
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

            $pesoTotal = collect($validated['productos'])->sum(function ($product) {
                return $product['weight'] * $product['quantity'];
            });

            $rate = $this->amplificaService->getRate(
                $validated['productos'],
                $validated['comuna']
            );

            // Guardar cotización en historial
            if (Auth::check()) {
                Cotizacion::create([
                    'user_id' => Auth::id(),
                    'region_origen' => $request->input('region_origen', 'No especificada'),
                    'comuna_origen' => $request->input('comuna_origen', 'No especificada'),
                    'region_destino' => $request->input('region_destino', 'No especificada'),
                    'comuna_destino' => $validated['comuna'],
                    'peso_total' => $pesoTotal,
                    'productos' => $validated['productos'],
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