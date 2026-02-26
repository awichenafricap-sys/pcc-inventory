<?php

namespace App\Http\Controllers;

use App\Models\Item;
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
        $products = Product::withCount('items')->paginate(15);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $items = Item::with('unit')->orderBy('name')->get();

        return view('products.create', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recipe' => 'nullable|array',
            'recipe.*' => 'nullable|integer|min:0',
        ]);

        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $recipeData = $this->buildRecipeSyncData($request);
        $product->items()->sync($recipeData);

        return redirect()->route('products.index')->with('success','Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('items');
        $items = Item::with('unit')->orderBy('name')->get();

        return view('products.edit', compact('product', 'items'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'recipe' => 'nullable|array',
            'recipe.*' => 'nullable|integer|min:0',
        ]);

        $product->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $recipeData = $this->buildRecipeSyncData($request);
        $product->items()->sync($recipeData);

        return redirect()->route('products.index')->with('success','Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success','Product deleted.');
    }

    private function buildRecipeSyncData(Request $request): array
    {
        $recipe = collect($request->input('recipe', []))
            ->mapWithKeys(fn ($quantity, $itemId) => [(int) $itemId => (int) $quantity])
            ->filter(fn ($quantity) => $quantity > 0);

        if ($recipe->isEmpty()) {
            return [];
        }

        $validItemIds = Item::whereIn('id', $recipe->keys()->all())->pluck('id')->all();

        $syncData = [];
        foreach ($validItemIds as $itemId) {
            $syncData[$itemId] = ['quantity_required' => $recipe->get($itemId)];
        }

        return $syncData;
    }
}
