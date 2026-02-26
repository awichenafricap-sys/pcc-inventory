<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\Item;
use Illuminate\Http\Request;

class RestockController extends Controller
{
    /**
     * Display a listing of the restock.
     */
    public function index()
    {
        $restock = Restock::with('item')->latest()->paginate(10);
        $items = Item::all();
        
        return view('restock.index', compact('restock', 'items'));
    }

    /**
     * Show the form for creating a new restock.
     */
    public function create()
    {
        $items = Item::all();
        
        return view('restock.create', compact('items'));
    }

    /**
     * Store a newly created restock in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'restock_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $restock = Restock::create($validated);

        // If AJAX request, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'id' => $restock->id,
                'item_name' => $restock->item->name,
                'quantity' => $restock->quantity,
                'formatted_date' => $restock->restock_date->format('M d, Y'),
                'notes' => $restock->notes,
            ]);
        }

        return redirect()->route('restock.index')
                        ->with('success', 'Restock record created successfully.');
    }

    /**
     * Show the form for editing the specified restock.
     */
    public function edit(Restock $restock)
    {
        $items = Item::all();
        
        return view('restock.edit', compact('restock', 'items'));
    }

    /**
     * Update the specified restock in storage.
     */
    public function update(Request $request, Restock $restock)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'restock_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $restock->update($validated);

        return redirect()->route('restock.index')
                        ->with('success', 'Restock record updated successfully.');
    }

    /**
     * Remove the specified restock from storage.
     */
    public function destroy(Restock $restock)
    {
        $restock->delete();

        return redirect()->route('restock.index')
                        ->with('success', 'Restock record deleted successfully.');
    }
}
