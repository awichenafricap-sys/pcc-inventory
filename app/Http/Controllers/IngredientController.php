<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:ingredients,sku',
            'category_id' => 'nullable|exists:categories,id',
            'unit_of_measurement' => 'required|string|max:50',
            'minimum_stock' => 'nullable|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
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
        return view('categories.showIngredients', compact('ingredient'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id) // Feature #4: Edit
    {
        $ingredient = Ingredient::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('categories.editIngredients', compact('ingredient', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:ingredients,sku,'.$id,
            'category_id' => 'nullable|exists:categories,id',
            'unit_of_measurement' => 'required|string|max:50',
            'minimum_stock' => 'nullable|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $ingredient->update($validated);

        return redirect()->route('categories.show', $validated['category_id'])
                        ->with('success', 'Ingredient updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $categoryId = $ingredient->category_id;
        $ingredient->delete();

        return redirect()->route('categories.show', $categoryId)
                        ->with('success', 'Ingredient deleted successfully!');
    }

    /**
     * Update inventory fields inline.
     */
    public function updateInventory(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'minimum_stock' => 'nullable|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $ingredient->fill($validated);
        $ingredient->save();

        // Get stock info from the current_ingredient_stock view
        $stockView = \DB::table('current_ingredient_stock')->where('ingredient_id', $ingredient->id)->first();

        return response()->json([
            'success' => true,
            'current_stock' => $stockView ? number_format($stockView->current_stock, 2) : '0.00',
            'status' => $stockView->status ?? 'out_of_stock',
        ]);
    }
}
