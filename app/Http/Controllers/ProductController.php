<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! $request->user() || ! $request->user()->is_admin) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $products = Product::paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'code' => 'required|unique:products,code',
        'name' => 'required',
        'category' => 'required',
        'unit' => 'required',
        'current_stock' => 'required|integer|min:0',
        'reorder_level' => 'required|integer|min:0',
    ]);

    Product::create($request->all());

    return redirect()
        ->route('products.index')
        ->with('success', 'Product added successfully.');
}

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

   public function update(Request $request, Product $product)
{
    $request->validate([
        'code' => 'required|unique:products,code,' . $product->id,
        'name' => 'required',
        'category' => 'required',
        'unit' => 'required',
        'current_stock' => 'required|integer|min:0',
        'reorder_level' => 'required|integer|min:0',
    ]);

    $product->update($request->all());

    return redirect()
        ->route('products.index')
        ->with('success', 'Product updated successfully.');
}

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success','Product deleted.');
    }
}
