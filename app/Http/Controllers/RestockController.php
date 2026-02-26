<?php

namespace App\Http\Controllers;

use App\Models\Restock;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{
    /**
     * Display a listing of the restock.
     */
    public function index()
    {
        $restock = Restock::query()
            ->selectRaw('batch_code, batch_date, MAX(restock_date) as restock_date, COUNT(*) as entries_count, COALESCE(SUM(quantity), 0) as total_quantity, MAX(created_at) as latest_created_at')
            ->groupBy('batch_code', 'batch_date')
            ->orderByDesc('batch_date')
            ->orderByDesc('latest_created_at')
            ->paginate(10);

        $batchDetails = [];
        if ($restock->count()) {
            $pairs = $restock->getCollection();

            $rows = Restock::with('item:id,name')
                ->where(function ($query) use ($pairs) {
                    foreach ($pairs as $pair) {
                        $query->orWhere(function ($q) use ($pair) {
                            $q->where('batch_code', $pair->batch_code)
                                ->whereDate('batch_date', $pair->batch_date);
                        });
                    }
                })
                ->orderBy('item_id')
                ->get();

            $batchDetails = $rows->groupBy(function ($row) {
                return $row->batch_code . '|' . date('Y-m-d', strtotime((string) $row->batch_date));
            })->map(function ($group) {
                return $group->map(function ($row) {
                    return [
                        'item_name' => $row->item?->name,
                        'quantity' => (int) $row->quantity,
                        'notes' => $row->notes,
                    ];
                })->values();
            })->toArray();
        }

        $items = Item::all();
        
        return view('restock.index', compact('restock', 'items', 'batchDetails'));
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
        // Backward compatibility: if single-row payload is sent, normalize to restocks[]
        if (! $request->has('restocks')) {
            $request->merge([
                'restocks' => [[
                    'item_id' => $request->input('item_id'),
                    'quantity' => $request->input('quantity'),
                    'notes' => $request->input('notes'),
                ]],
                'restock_date' => $request->input('restock_date'),
                'batch_code' => $request->input('batch_code'),
                'batch_date' => $request->input('batch_date'),
            ]);
        }

        $validated = $request->validate([
            'restock_date' => 'required|date',
            'batch_code' => 'nullable|string|max:100',
            'batch_date' => 'required|date',
            'restocks' => 'required|array|min:1',
            'restocks.*.item_id' => 'required|exists:items,id|distinct',
            'restocks.*.quantity' => 'required|integer|min:1',
            'restocks.*.notes' => 'nullable|string',
        ]);

        if (empty($validated['batch_code'])) {
            $validated['batch_code'] = $this->generateBatchCode($validated['batch_date']);
        }

        $itemIds = collect($validated['restocks'])->pluck('item_id')->all();

        $alreadyInBatch = Restock::query()
            ->where('batch_code', $validated['batch_code'])
            ->whereDate('batch_date', $validated['batch_date'])
            ->whereIn('item_id', $itemIds)
            ->with('item:id,name')
            ->get();

        if ($alreadyInBatch->isNotEmpty()) {
            $names = $alreadyInBatch->map(fn ($row) => $row->item?->name)->filter()->implode(', ');

            return back()->withInput()->withErrors([
                'restocks' => 'These items are already added in this batch: ' . $names,
            ]);
        }

        $createdRows = DB::transaction(function () use ($validated) {
            $rows = collect($validated['restocks'])->map(function (array $row) use ($validated) {
                $restock = Restock::create([
                    'item_id' => $row['item_id'],
                    'quantity' => $row['quantity'],
                    'notes' => $row['notes'] ?? null,
                    'restock_date' => $validated['restock_date'],
                    'batch_code' => $validated['batch_code'],
                    'batch_date' => $validated['batch_date'],
                ]);

                return [
                    'id' => $restock->id,
                    'item_name' => $restock->item->name,
                    'quantity' => $restock->quantity,
                    'formatted_date' => $restock->restock_date->format('M d, Y'),
                    'batch_code' => $restock->batch_code,
                    'formatted_batch_date' => $restock->batch_date ? $restock->batch_date->format('M d, Y') : '-',
                    'notes' => $restock->notes,
                ];
            })->values();

            $this->syncItemStocksFromRestocks(collect($validated['restocks'])->pluck('item_id')->all());

            return $rows;
        });

        // If AJAX request, return JSON
        if ($request->expectsJson()) {
            $batchSummary = Restock::query()
                ->selectRaw('batch_code, batch_date, MAX(restock_date) as restock_date, COUNT(*) as entries_count, COALESCE(SUM(quantity), 0) as total_quantity')
                ->where('batch_code', $validated['batch_code'])
                ->whereDate('batch_date', $validated['batch_date'])
                ->groupBy('batch_code', 'batch_date')
                ->first();

            return response()->json([
                'rows' => $createdRows,
                'batch' => [
                    'batch_code' => $batchSummary?->batch_code,
                    'formatted_batch_date' => $batchSummary?->batch_date ? date('M d, Y', strtotime((string) $batchSummary->batch_date)) : '-',
                    'batch_date_key' => $batchSummary?->batch_date ? date('Y-m-d', strtotime((string) $batchSummary->batch_date)) : null,
                    'formatted_restock_date' => $batchSummary?->restock_date ? date('M d, Y', strtotime((string) $batchSummary->restock_date)) : '-',
                    'entries_count' => (int) ($batchSummary?->entries_count ?? 0),
                    'total_quantity' => (int) ($batchSummary?->total_quantity ?? 0),
                    'details' => $createdRows->map(function ($row) {
                        return [
                            'item_name' => $row['item_name'],
                            'quantity' => (int) $row['quantity'],
                            'notes' => $row['notes'] ?? null,
                        ];
                    })->values(),
                ],
            ]);
        }

        return redirect()->route('restock.index')
                        ->with('success', 'Restock record(s) created successfully.');
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
        $originalItemId = $restock->item_id;

        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'restock_date' => 'required|date',
            'batch_code' => 'required|string|max:100',
            'batch_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($restock, $validated, $originalItemId) {
            $restock->update($validated);
            $this->syncItemStocksFromRestocks([$originalItemId, $restock->item_id]);
        });

        return redirect()->route('restock.index')
                        ->with('success', 'Restock record updated successfully.');
    }

    /**
     * Remove the specified restock from storage.
     */
    public function destroy(Restock $restock)
    {
        $itemId = $restock->item_id;

        DB::transaction(function () use ($restock, $itemId) {
            $restock->delete();
            $this->syncItemStocksFromRestocks([$itemId]);
        });

        return redirect()->route('restock.index')
                        ->with('success', 'Restock record deleted successfully.');
    }

    private function generateBatchCode(string $batchDate): string
    {
        $dateKey = date('Ymd', strtotime($batchDate));

        $sequence = Restock::query()
            ->whereDate('batch_date', $batchDate)
            ->distinct('batch_code')
            ->count('batch_code') + 1;

        return 'BATCH-' . $dateKey . '-' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
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
