<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Produce;
use App\Models\ProduceBatchUsage;
use App\Models\Product;
use App\Models\Restock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduceController extends Controller
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
        $produce = Produce::with('product')->latest()->paginate(15);
        $products = Product::with(['items.unit'])->orderBy('name')->get();

        $itemIds = $products->flatMap(fn ($product) => $product->items->pluck('id'))->unique()->values();
        $stocksByItem = Restock::query()
            ->selectRaw('item_id, COALESCE(SUM(quantity), 0) as total_quantity')
            ->whereIn('item_id', $itemIds)
            ->groupBy('item_id')
            ->pluck('total_quantity', 'item_id');

        $products->each(function ($product) use ($stocksByItem) {
            $product->items->each(function ($item) use ($stocksByItem) {
                $item->current_stock = (int) ($stocksByItem[$item->id] ?? 0);
            });
        });

        return view('produce.index', compact('produce', 'products'));
    }

    public function create()
    {
        return redirect()->route('produce.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'produced_at' => 'required|date',
            'produced_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $producedAtDateTime = $data['produced_at'] . ' ' . $data['produced_time'] . ':00';

        $product = Product::with('items')->findOrFail($data['product_id']);
        $recipeItems = $product->items;

        if ($recipeItems->isEmpty()) {
            return back()->withInput()->withErrors([
                'product_id' => 'Selected product has no recipe configured.',
            ]);
        }

        $stockMap = Restock::query()
            ->selectRaw('item_id, COALESCE(SUM(quantity), 0) as total_quantity')
            ->whereIn('item_id', $recipeItems->pluck('id'))
            ->groupBy('item_id')
            ->pluck('total_quantity', 'item_id');

        $maxPossible = $recipeItems
            ->map(fn ($item) => intdiv((int) ($stockMap[$item->id] ?? 0), max(1, (int) $item->pivot->quantity_required)))
            ->min();

        if ((int) $maxPossible <= 0) {
            return back()->withInput()->withErrors([
                'quantity' => 'Insufficient batch stock for this product recipe.',
            ]);
        }

        if ((int) $data['quantity'] > (int) $maxPossible) {
            return back()->withInput()->withErrors([
                'quantity' => "Only {$maxPossible} unit(s) can be produced with current ingredient stock.",
            ]);
        }

        DB::transaction(function () use ($data, $product, $recipeItems, $producedAtDateTime) {
            $produce = Produce::create([
                'name' => $product->name,
                'category' => 'product-production',
                'description' => $data['notes'] ?? null,
                'product_id' => $product->id,
                'quantity' => (int) $data['quantity'],
                'produced_at' => $data['produced_at'],
                'produced_at_datetime' => $producedAtDateTime,
                'notes' => $data['notes'] ?? null,
            ]);

            $affectedItemIds = [];

            foreach ($recipeItems as $recipeItem) {
                $remainingNeeded = max(1, (int) $recipeItem->pivot->quantity_required) * (int) $data['quantity'];

                $batches = Restock::query()
                    ->where('item_id', $recipeItem->id)
                    ->where('quantity', '>', 0)
                    ->orderBy('batch_date')
                    ->orderBy('restock_date')
                    ->orderBy('id')
                    ->get();

                foreach ($batches as $batch) {
                    if ($remainingNeeded <= 0) {
                        break;
                    }

                    $useQty = min($remainingNeeded, (int) $batch->quantity);
                    if ($useQty <= 0) {
                        continue;
                    }

                    $batch->decrement('quantity', $useQty);

                    ProduceBatchUsage::create([
                        'produce_id' => $produce->id,
                        'restock_id' => $batch->id,
                        'item_id' => $recipeItem->id,
                        'quantity_used' => $useQty,
                    ]);

                    $remainingNeeded -= $useQty;
                }

                $affectedItemIds[] = $recipeItem->id;
            }

            $this->syncItemStocksFromRestocks($affectedItemIds);
        });

        return redirect()->route('produce.index')->with('success', 'Production recorded and ingredient stock updated.');
    }

    public function edit(Produce $produce)
    {
        return redirect()->route('produce.index');
    }

    public function update(Request $request, Produce $produce)
    {
        return redirect()->route('produce.index');
    }

    public function destroy(Produce $produce)
    {
        DB::transaction(function () use ($produce) {
            $usages = $produce->batchUsages()->get();

            foreach ($usages as $usage) {
                Restock::whereKey($usage->restock_id)->increment('quantity', (int) $usage->quantity_used);
            }

            $affectedItemIds = $usages->pluck('item_id')->unique()->values()->all();

            $produce->delete();

            $this->syncItemStocksFromRestocks($affectedItemIds);
        });

        return redirect()->route('produce.index')->with('success', 'Produce deleted successfully.');
    }

    private function syncItemStocksFromRestocks(array $itemIds): void
    {
        $ids = collect($itemIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return;
        }

        $totals = Restock::query()
            ->selectRaw('item_id, COALESCE(SUM(quantity), 0) as total_quantity')
            ->whereIn('item_id', $ids)
            ->groupBy('item_id')
            ->pluck('total_quantity', 'item_id');

        Item::query()
            ->whereIn('id', $ids)
            ->get(['id'])
            ->each(function (Item $item) use ($totals) {
                $item->update([
                    'stock' => (int) ($totals[$item->id] ?? 0),
                ]);
            });
    }
}
