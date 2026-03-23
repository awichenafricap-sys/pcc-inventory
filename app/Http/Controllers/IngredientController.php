<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Category;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index() // Feature #1: Listahan
    {
        $query = Ingredient::with('category');
        
        // Filter by category if specified
        if (request()->has('category')) {
            $categoryId = request('category');
            $query->where('category_id', $categoryId);
        }
        
        $ingredients = $query->orderBy('name')->get();
        
        $lowStockCount = Ingredient::whereRaw('current_stock <= minimum_stock')->count();
        $nearExpiryCount = Ingredient::where('expiry_date', '<=', now()->addDays(7))->count();
        
        // Get current category for breadcrumb
        $currentCategory = null;
        if (request()->has('category')) {
            $currentCategory = Category::find(request('category'));
        }
        
        return view('ingredient.index', compact('ingredients', 'lowStockCount', 'nearExpiryCount', 'currentCategory'));
    }
    /**
     * Show the form for creating a new resource.
     */
      public function create() // Feature #6: Quick Add
    {
        $categories = Category::all();
        $selectedCategoryId = request('category_id');
        
        return view('ingredient.create', compact('categories', 'selectedCategoryId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:ingredients,sku',
            'category_id' => 'required|exists:categories,id',
            'unit_of_measurement' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
        ]);

        $ingredient = Ingredient::create($validated);

        return redirect()->route('categories.show', $validated['category_id'])
                        ->with('success', 'Ingredient created successfully!');
    }

    /**
     * Display the specified resource.
     */
      public function show($id) // Feature #3: Detailed View
    {
        $ingredient = Ingredient::with('category')->findOrFail($id);
        return view('ingredient.show', compact('ingredient'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) // Feature #4: Edit
    {
        $ingredient = Ingredient::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('ingredient.edit', compact('ingredient', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:ingredients,sku,'.$id,
            'category_id' => 'required|exists:categories,id',
            'unit_of_measurement' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
        ]);

        $ingredient->update($validated);

        return redirect()->route('categories.show', $ingredient->category_id)
                        ->with('success', 'Ingredient updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();

        return redirect()->route('ingredients.index')
                        ->with('success', 'Ingredient deleted successfully!');
    }
}
