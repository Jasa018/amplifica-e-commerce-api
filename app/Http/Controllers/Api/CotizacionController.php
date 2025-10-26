<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AmplificaApiService;
use Illuminate\Http\Request;

class CotizacionController extends Controller
{
    private AmplificaApiService $amplificaService;

    public function __construct(AmplificaApiService $amplificaService)
    {
        $this->amplificaService = $amplificaService;
    }

    public function cotizar(Request $request)
    {
        $request->validate([
            'comuna' => 'required|string',
            'productos' => 'required|array',
            'productos.*.weight' => 'required|numeric',
            'productos.*.quantity' => 'required|integer'
        ]);

        try {
            $rate = $this->amplificaService->getRate(
                $request->productos,
                $request->comuna
            );

            return response()->json($rate);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}