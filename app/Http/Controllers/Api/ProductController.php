<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::all();
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('API products index error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener productos'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:products,name',
                'price' => 'required|numeric|min:0|max:999999.99',
                'weight' => 'required|numeric|min:0.01|max:1000',
                'width' => 'required|numeric|min:0.01|max:1000',
                'height' => 'required|numeric|min:0.01|max:1000',
                'length' => 'required|numeric|min:0.01|max:1000',
                'stock' => 'required|integer|min:0|max:999999',
            ]);

            $product = Product::create($validatedData);
            Log::info('API product created', ['product_id' => $product->id]);

            return response()->json($product, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API product creation error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al crear producto'], 500);
        }
    }

    public function show(Product $product)
    {
        try {
            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('API product show error', ['error' => $e->getMessage(), 'product_id' => $product->id]);
            return response()->json(['error' => 'Error al obtener producto'], 500);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:products,name,' . $product->id,
                'price' => 'required|numeric|min:0|max:999999.99',
                'weight' => 'required|numeric|min:0.01|max:1000',
                'width' => 'required|numeric|min:0.01|max:1000',
                'height' => 'required|numeric|min:0.01|max:1000',
                'length' => 'required|numeric|min:0.01|max:1000',
                'stock' => 'required|integer|min:0|max:999999',
            ]);

            $product->update($validatedData);
            Log::info('API product updated', ['product_id' => $product->id]);

            return response()->json($product);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Datos de validación incorrectos',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('API product update error', ['error' => $e->getMessage(), 'product_id' => $product->id]);
            return response()->json(['error' => 'Error al actualizar producto'], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $productId = $product->id;
            $product->delete();
            Log::info('API product deleted', ['product_id' => $productId]);

            return response()->json(['message' => 'Producto eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('API product deletion error', ['error' => $e->getMessage(), 'product_id' => $product->id]);
            return response()->json(['error' => 'Error al eliminar producto'], 500);
        }
    }
}
