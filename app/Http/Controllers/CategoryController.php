<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ensure default "Flavor" category always exists
        Category::firstOrCreate(
            ['slug' => 'flavor'],
            ['name' => 'Flavor', 'description' => 'Flavorings and extracts', 'color' => '#FF69B4']
        );

        $categories = Category::withCount('ingredients')->orderBy('name')->get();
        
        $ingredientsQuery = Ingredient::with('category');
        
        // Filter by status (low_stock)
        if ($request->filled('filter')) {
            if ($request->filter === 'low_stock') {
                $ingredientsQuery->whereRaw('released_used_items <= minimum_stock');
            }
        }
        
        // Search by name and unit
        if ($request->filled('search')) {
            $search = $request->search;
            $ingredientsQuery->where(function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('unit_of_measurement', 'like', '%' . $search . '%');
            });
        }
        
        $ingredients = $ingredientsQuery->orderBy('name')->get();
        
        // Calculate counts based on all ingredients (not filtered)
        $allIngredients = Ingredient::all();
        $totalIngredientsCount = $allIngredients->count();
        $lowStockCount = $allIngredients->filter(function($i) { $v = $i->released_used_items ?? 0; return $v > 0 && $v <= $i->minimum_stock; })->count();
        
        return view('categories.index', compact('categories', 'ingredients', 'totalIngredientsCount', 'lowStockCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
        ]);

        $category = Category::create($validated);

        return redirect()->route('categories.index')
                        ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load('ingredients');
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
                        ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->ingredients()->count() > 0) {
            return redirect()->route('categories.index')
                            ->with('error', 'Cannot delete category with associated ingredients!');
        }

        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', 'Category deleted successfully!');
    }
}
