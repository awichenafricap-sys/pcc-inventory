<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Ingredient;
use App\Models\InventoryTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
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
        
        return view('supplies.index', compact('ingredients', 'totalBeginning', 'totalEnding'));
    }

    public function next(Ingredient $ingredient)
    {
        $ingredient->load(['category', 'inventoryTrackings']);
        return view('supplies.SuppIngredients.next', compact('ingredient'));
    }

    /**
     * Store a new inventory tracking record.
     */
    public function storeInventoryTracking(Request $request, Ingredient $ingredient)
    {
        try {
            $data = $request->validate([
                'in_released' => 'nullable|numeric|min:0',
                'out' => 'nullable|string|max:255',
            ]);

            // Get previous ending if exists, otherwise use beginning_inventory
            $lastTracking = $ingredient->inventoryTrackings()->latest()->first();
            $beginning = $lastTracking ? $lastTracking->ending : ($ingredient->beginning_inventory ?? 0);
            
            $inReleased = $data['in_released'] ?? 0;
            
            // Check if beginning is already zero (no more stock)
            if ($beginning <= 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No more stock available. Please restock before releasing.',
                    ], 422);
                }
                return redirect()->back()->withErrors(['in_released' => 'No more stock available. Please restock before releasing.']);
            }
            
            // Check if released exceeds beginning
            if ($inReleased > $beginning) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Released quantity ({$inReleased}) exceeds available stock ({$beginning}).",
                    ], 422);
                }
                return redirect()->back()->withErrors(['in_released' => "Released quantity ({$inReleased}) exceeds available stock ({$beginning})."]);
            }
            
            // Formula: beginning - released = total
            $total = $beginning - $inReleased;
            $ending = $total;

            $tracking = $ingredient->inventoryTrackings()->create([
                'beginning' => $beginning,
                'in_released' => $inReleased,
                'out' => $data['out'] ?? null,
                'total' => $total,
                'ending' => $ending,
            ]);

            // Update released_quantity in ingredients table (sum of all in_released)
            $totalReleased = $ingredient->inventoryTrackings()->sum('in_released');
            $ingredient->released_quantity = $totalReleased;
            $ingredient->save();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'tracking' => $tracking,
                ]);
            }

            return redirect()->back()->with('success', 'Inventory tracking added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('supplies.index')->with('success', 'Item deleted.');
    }
}
