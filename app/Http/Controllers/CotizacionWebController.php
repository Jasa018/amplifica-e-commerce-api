<?php

namespace App\Http\Controllers;

use App\Services\AmplificaApiService;
use App\Models\Product;
use Illuminate\Http\Request;

class CotizacionWebController extends Controller
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
            return view('cotizaciones.index', [
                'regionalConfig' => [],
                'products' => Product::all(),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function cotizar(Request $request)
    {
        $request->validate([
            'comuna' => 'required|string',
            'products' => 'required|array',
            'products.*.weight' => 'required|numeric',
            'products.*.quantity' => 'required|integer'
        ]);

        try {
            $rate = $this->amplificaService->getRate(
                $request->products,
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