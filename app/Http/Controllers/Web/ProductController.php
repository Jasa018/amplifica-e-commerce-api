<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }
    public function index(Request $request)
    {
        try {
            $query = Product::query();
            
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            
            if ($request->filled('price_min')) {
                $query->where('price', '>=', $request->price_min);
            }
            
            if ($request->filled('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }
            
            if ($request->filled('stock_min')) {
                $query->where('stock', '>=', $request->stock_min);
            }
            
            if ($request->filled('stock_max')) {
                $query->where('stock', '<=', $request->stock_max);
            }
            
            $products = $query->paginate(10)->withQueryString();
            return view('products.index', compact('products'));
        } catch (\Exception $e) {
            Log::error('Error loading products', ['error' => $e->getMessage()]);
            return view('products.index', ['products' => collect()])
                ->with('error', 'Error al cargar productos');
        }
    }

    public function create()
    {
        return view('products.create');
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
            Log::info('Product created', ['product_id' => $product->id, 'name' => $product->name]);

            return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error creating product', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return back()->withInput()->with('error', 'Error al crear producto: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
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
            Log::info('Product updated', ['product_id' => $product->id, 'name' => $product->name]);

            return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error updating product', ['error' => $e->getMessage(), 'product_id' => $product->id]);
            return back()->withInput()->with('error', 'Error al actualizar producto: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            $productName = $product->name;
            $product->delete();
            Log::info('Product deleted', ['product_name' => $productName]);

            return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error deleting product', ['error' => $e->getMessage(), 'product_id' => $product->id]);
            return back()->with('error', 'Error al eliminar producto: ' . $e->getMessage());
        }
    }
}
