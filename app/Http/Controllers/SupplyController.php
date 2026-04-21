<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('supplies.index');
    }

    /**
     * Display the supplies ingredients page.
     */
    public function ingredients()
    {
        $ingredients = Ingredient::with('category')->orderBy('name')->paginate(15);
        
        // Get totals across ALL pages for summary cards
        $totalBeginning = Ingredient::sum('beginning_inventory');
        $totalEnding = Ingredient::sum('current_stock');
        
        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'totalBeginning' => $totalBeginning,
                'totalEnding' => $totalEnding,
            ]);
        }
        
        return view('supplies.SuppIngredients.SuppliesIngredients', compact('ingredients', 'totalBeginning', 'totalEnding'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('supplies.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
