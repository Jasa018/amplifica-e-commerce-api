<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Listar productos",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de productos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="price", type="number"),
     *                 @OA\Property(property="weight", type="number"),
     *                 @OA\Property(property="stock", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $products = Product::all();
            return new ProductCollection($products);
        } catch (\Exception $e) {
            Log::error('API products index error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error al obtener productos'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Crear producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "price", "weight", "width", "height", "length", "stock"},
     *             @OA\Property(property="name", type="string", example="Producto ejemplo"),
     *             @OA\Property(property="price", type="number", example=99.99),
     *             @OA\Property(property="weight", type="number", example=1.5),
     *             @OA\Property(property="width", type="number", example=10),
     *             @OA\Property(property="height", type="number", example=5),
     *             @OA\Property(property="length", type="number", example=15),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Producto creado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
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

            return new ProductResource($product);
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

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Obtener producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Producto encontrado"),
     *     @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
    public function show(Product $product)
    {
        try {
            return new ProductResource($product);
        } catch (\Exception $e) {
            Log::error('API product show error', ['error' => $e->getMessage(), 'product_id' => $product->id]);
            return response()->json(['error' => 'Error al obtener producto'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Actualizar producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="weight", type="number"),
     *             @OA\Property(property="stock", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Producto actualizado"),
     *     @OA\Response(response=422, description="Error de validación")
     * )
     */
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

            return new ProductResource($product);
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

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Eliminar producto",
     *     tags={"Productos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Producto eliminado"),
     *     @OA\Response(response=404, description="Producto no encontrado")
     * )
     */
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
