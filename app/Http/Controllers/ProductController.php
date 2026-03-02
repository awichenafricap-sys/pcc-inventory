<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! $request->user() || ! $request->user()->is_admin) {
                abort(403, 'Unauthorized access.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $products = Product::latest()->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:products,code|max:50',
            'name' => 'required|max:255',
            'category' => 'required|max:100',
            'unit' => 'required|max:50',
            'current_stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max - mas practical
        ]);
        
        try {
            $data = $request->except('image');
            
            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            Product::create($data);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $request->name . '" added successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Log the error for debugging
            Log::error('Product store error: ' . $e->getMessage());
            
            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()
                    ->withErrors(['code' => 'The product code "' . $request->code . '" already exists. Please use a different code.']);
            }
            
            // Other database errors
            return back()->withInput()
                ->withErrors(['error' => 'A database error occurred. Please try again or contact support.']);
                
        } catch (\Exception $e) {
            Log::error('Product store unexpected error: ' . $e->getMessage());
            
            return back()->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'required|unique:products,code,' . $product->id . '|max:50',
            'name' => 'required|max:255',
            'category' => 'required|max:100',
            'unit' => 'required|max:50',
            'current_stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        try {
            $data = $request->except('image');
            
            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                
                // Store new image
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            $product->update($data);

            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $product->name . '" updated successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Log the error for debugging
            Log::error('Product update error: ' . $e->getMessage());
            
            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()
                    ->withErrors(['code' => 'The product code "' . $request->code . '" already exists. Please use a different code.']);
            }
            
            // Other database errors
            return back()->withInput()
                ->withErrors(['error' => 'A database error occurred. Please try again or contact support.']);
                
        } catch (\Exception $e) {
            Log::error('Product update unexpected error: ' . $e->getMessage());
            
            return back()->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Delete image file if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $productName = $product->name;
            $product->delete();
            
            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $productName . '" deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Product delete error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'An error occurred while deleting the product.']);
        }
    }
    
    /**
     * Optional: Add method to remove image only
     */
    public function removeImage(Product $product)
    {
        try {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
                
                $product->update(['image' => null]);
                
                return response()->json(['success' => true, 'message' => 'Image removed successfully.']);
            }
            
            return response()->json(['success' => false, 'message' => 'No image found.']);
            
        } catch (\Exception $e) {
            Log::error('Remove image error: ' . $e->getMessage());
            
            return response()->json(['success' => false, 'message' => 'Error removing image.'], 500);
        }
    }
}