<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Unit;
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
        $items = Item::with('unit')->paginate(15);
        $units = Unit::all();
        return view('items.index', compact('items','units'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('items.create', compact('units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'is_default' => 'sometimes|boolean',
            'default_quantity' => 'required|integer|min:1',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data['cost_per_unit'] = $data['cost_per_unit'] ?? 0;
        $data['default_quantity'] = $data['default_quantity'] ?? 0;
        $data['stock'] = $data['stock'] ?? 0;

        // If a default quantity is provided, require that the user also checked the 'Default' checkbox
        if (($data['default_quantity'] ?? 0) > 0 && ! $request->has('is_default')) {
            return back()->withInput()->withErrors(['is_default' => 'Please check "Default" when setting a default quantity.']);
        }

        // Auto-set is_default when default_quantity > 0, otherwise use checkbox
        $data['is_default'] = (($data['default_quantity'] ?? 0) > 0) || isset($data['is_default']) ? 1 : 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/items', 'public');
        }

        $item = Item::create($data);

        // If this is an AJAX request (inline add), return JSON so the page doesn't need to redirect
        if ($request->ajax() || $request->wantsJson()) {
            $item->load('unit');
            return response()->json([
                'id' => $item->id,
                'name' => $item->name,
                'unit' => $item->unit?->name,
                'cost_per_unit' => number_format($item->cost_per_unit, 2),
                'default_quantity' => $item->default_quantity ?? 0,
                'stock' => $item->stock,
                'image_url' => $item->image ? asset('storage/' . $item->image) : null,
            ], 201);
        }

        return redirect()->route('items.index')->with('success', 'Item created.');
    }

    public function edit(Item $item)
    {
        $units = Unit::all();
        return view('items.edit', compact('item','units'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'is_default' => 'sometimes|boolean',
            'default_quantity' => 'required|integer|min:1',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data['cost_per_unit'] = $data['cost_per_unit'] ?? 0;
        $data['default_quantity'] = $data['default_quantity'] ?? 0;
        $data['stock'] = $data['stock'] ?? 0;

        // If a default quantity is provided, require that the user also checked the 'Default' checkbox
        if (($data['default_quantity'] ?? 0) > 0 && ! $request->has('is_default')) {
            return back()->withInput()->withErrors(['is_default' => 'Please check "Default" when setting a default quantity.']);
        }

        // Auto-set is_default when default_quantity > 0, otherwise use checkbox
        $data['is_default'] = (($data['default_quantity'] ?? 0) > 0) || isset($data['is_default']) ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('uploads/items', 'public');
        }

        $item->update($data);

        return redirect()->route('items.index')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted.');
    }
}
