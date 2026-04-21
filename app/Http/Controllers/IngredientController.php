<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Category;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
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
            'beginning_inventory' => 'nullable|numeric|min:0',
            'received_quantity' => 'nullable|numeric|min:0',
            'ending_inventory' => 'nullable|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
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
            'sku' => 'required|string|unique:ingredients,sku,'.$id,
            'category_id' => 'required|exists:categories,id',
            'unit_of_measurement' => 'required|string|max:50',
            'beginning_inventory' => 'nullable|numeric|min:0',
            'received_quantity' => 'nullable|numeric|min:0',
            'ending_inventory' => 'nullable|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
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
            'beginning_inventory' => 'nullable|numeric|min:0',
            'received_date' => 'nullable|date',
            'received_quantity' => 'nullable|numeric|min:0',
            'released_date' => 'nullable|date',
            'released_quantity' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $ingredient->fill($validated);
        // current_stock is auto-calculated by the model: beginning_inventory + received_quantity
        $ingredient->save();

        // Return JSON for AJAX request
        return response()->json([
            'success' => true,
            'current_stock' => number_format($ingredient->current_stock, 2),
            'status' => $ingredient->status,
        ]);
    }
}
