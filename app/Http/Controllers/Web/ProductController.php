<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['web', 'auth']);
    }
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Product::create($validatedData);

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'weight' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'length' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validatedData);

        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
