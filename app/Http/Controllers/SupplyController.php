<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $today = now()->format('Y-m-d');

        $ingredients = Ingredient::with(['category', 'stockView', 'products' => function ($query) {
            $query->withPivot('measurement');
        }])
            ->orderBy('name')
            ->paginate(15);

        // Compute system ending for each ingredient
        $systemEndings = [];
        $variances = [];
        $releasedUsedItemsArr = [];
        foreach ($ingredients as $ingredient) {
            // System Ending: beginning + in_items - totalProductOut (original formula from SupplyNext)
            $productMeasurements = $ingredient->products->pluck('pivot.measurement', 'id')->toArray();
            $productIds = $ingredient->products->pluck('id');

            $productBatchTotals = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
                ->where('production_date', $today)
                ->selectRaw('product_id, SUM(total_batch) as grand_total_batch')
                ->groupBy('product_id')
                ->pluck('grand_total_batch', 'product_id')
                ->toArray();

            $totalProductOut = 0;
            foreach ($productBatchTotals as $productId => $batchTotal) {
                $measurement = $productMeasurements[$productId] ?? null;
                if ($batchTotal && $measurement) {
                    $totalProductOut += $batchTotal * floatval($measurement);
                }
            }

            $beginInv = $ingredient->beginning_inventory ?? 0;
            $inItems = $ingredient->in_items ?? 0;
            $systemEnding = $beginInv + $inItems - $totalProductOut;
            $systemEndings[$ingredient->id] = $systemEnding;

            // Released/Used Items: beginning + receive_items - actual_ending
            $receiveItems = $ingredient->receive_items ?? 0;
            $actualEnding = $ingredient->actual_ending ?? 0;
            $releasedUsedItems = $beginInv + $receiveItems - $actualEnding;
            $releasedUsedItemsArr[$ingredient->id] = $releasedUsedItems;

            // Variance: actual_ending - system_ending
            $variances[$ingredient->id] = $actualEnding - $systemEnding;

            // Save computed released/used items to database
            if ($ingredient->released_used_items != $releasedUsedItems) {
                $ingredient->update(['released_used_items' => $releasedUsedItems]);
            }
        }

        return view('supplies.SuppIngredients.SuppliesIngredients', compact('ingredients', 'systemEndings', 'variances', 'releasedUsedItemsArr'));
    }

    /**
     * Auto-save a single ingredient field via AJAX.
     */
    public function updateField(Request $request, Ingredient $ingredient)
    {
        $allowedFields = ['beginning_inventory', 'in_items', 'actual_ending', 'receive_items', 'date_receive'];
        $field = $request->input('field');

        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Invalid field'], 422);
        }

        $value = $request->input('value', 0);
        if ($field === 'date_receive') {
            $value = $value === '' || $value === null ? null : $value;
        } else {
            $value = $value === '' || $value === null ? 0 : floatval($value);
        }

        $ingredient->update([$field => $value]);

        // Released/Used Items: beginning + receive_items - actual_ending
        $releasedUsedItems = ($ingredient->beginning_inventory ?? 0) + ($ingredient->receive_items ?? 0) - ($ingredient->actual_ending ?? 0);
        if ($ingredient->released_used_items != $releasedUsedItems) {
            $ingredient->update(['released_used_items' => $releasedUsedItems]);
        }

        // System Ending: beginning + in_items - totalProductOut
        $ingredient->load(['products' => function ($query) {
            $query->withPivot('measurement');
        }]);
        $today = now()->format('Y-m-d');
        $productMeasurements = $ingredient->products->pluck('pivot.measurement', 'id')->toArray();
        $productIds = $ingredient->products->pluck('id');

        $productBatchTotals = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
            ->where('production_date', $today)
            ->selectRaw('product_id, SUM(total_batch) as grand_total_batch')
            ->groupBy('product_id')
            ->pluck('grand_total_batch', 'product_id')
            ->toArray();

        $totalProductOut = 0;
        foreach ($productBatchTotals as $productId => $batchTotal) {
            $measurement = $productMeasurements[$productId] ?? null;
            if ($batchTotal && $measurement) {
                $totalProductOut += $batchTotal * floatval($measurement);
            }
        }

        $systemEnding = ($ingredient->beginning_inventory ?? 0) + ($ingredient->in_items ?? 0) - $totalProductOut;

        $variance = ($ingredient->actual_ending ?? 0) - $systemEnding;

        return response()->json([
            'success' => true,
            'field' => $field,
            'value' => $ingredient->$field,
            'beginning' => number_format($ingredient->beginning_inventory ?? 0, 1),
            'variance' => number_format($variance, 2),
            'released_used_items' => number_format($releasedUsedItems, 1),
        ]);
    }

    /**
     * Bulk update ingredients inventory fields.
     */
    public function updateIngredients(Request $request)
    {
        $validated = $request->validate([
            'ingredients' => 'required|array',
            'ingredients.*.beginning_inventory' => 'nullable|numeric|min:0',
            'ingredients.*.date_receive' => 'nullable|date',
            'ingredients.*.receive_items' => 'nullable|numeric|min:0',
            'ingredients.*.actual_ending' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['ingredients'] as $id => $data) {
            $ingredient = Ingredient::find($id);
            if ($ingredient) {
                $ingredient->update([
                    'beginning_inventory' => $data['beginning_inventory'] ?? 0,
                    'date_receive' => $data['date_receive'] ?? null,
                    'receive_items' => $data['receive_items'] ?? 0,
                    'actual_ending' => $data['actual_ending'] ?? 0,
                ]);
            }
        }

        return redirect()->route('supplies.ingredients')->with('status', 'Ingredients updated successfully.');
    }

    /**
     * Display ingredient detail page with two side-by-side tables.
     */
    public function ingredientDetail(Request $request, Ingredient $ingredient)
    {
        $selectedDate = $request->query('date', now()->format('Y-m-d'));
        $selectedType = $request->query('type', 'all');

        $ingredient->load(['products' => function ($query) {
            $query->withPivot('measurement');
        }]);

        $productIds = $ingredient->products->pluck('id');
        $productMeasurements = $ingredient->products->pluck('pivot.measurement', 'id')->toArray();

        // Get batch rules for all ingredient-product relationships
        $ingredientProductIds = DB::table('ingredient_product')
            ->whereIn('product_id', $productIds)
            ->where('ingredient_id', $ingredient->id)
            ->pluck('id', 'product_id')
            ->toArray();

        // Load batch rules for each product
        $productBatchRules = [];
        foreach ($ingredientProductIds as $productId => $ipId) {
            $rules = DB::table('batch_rules')
                ->where('ingredient_product_id', $ipId)
                ->orderBy('batch_limit')
                ->get(['batch_limit', 'measurement']);
            if ($rules->isNotEmpty()) {
                $productBatchRules[$productId] = $rules->toArray();
            }
        }

        // Product-level batch totals with column divisors (matches Batch Production)
        $productBatchTotals = $this->computeProductBatchTotals(
            $productIds->all(),
            $selectedDate,
            $selectedType
        );

        // Grand total batch across ALL types (for the card)
        $grandTotalBatch = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
            ->where('production_date', $selectedDate)
            ->sum('total_batch');

        // Build per-product display (rules-aware when batch_rules exist, else normal flow)
        $productDisplay = [];
        $totalProductOut = 0;
        foreach ($productIds as $productId) {
            $rawBatch = (float) ($productBatchTotals[$productId] ?? 0);
            $pivotMeasurement = $productMeasurements[$productId] ?? null;
            $rules = $productBatchRules[$productId] ?? null;
            $activeRule = ($rules && $rawBatch > 0) ? $this->resolveActiveRule($rawBatch, $rules) : null;

            if ($activeRule) {
                $displayBatch = min($rawBatch, (float) $activeRule['batch_limit']);
                $displayMeasurement = $activeRule['measurement'];
                $displayOut = $this->calculateTieredOut($rawBatch, $rules);
            } else {
                $displayBatch = $rawBatch;
                $displayMeasurement = $pivotMeasurement;
                $displayOut = ($rawBatch && $pivotMeasurement)
                    ? $rawBatch * floatval($pivotMeasurement)
                    : 0;
            }

            $productDisplay[$productId] = [
                'active_rule' => $activeRule,
                'batch' => $displayBatch,
                'measurement' => $displayMeasurement,
                'out' => $displayOut,
            ];
            $totalProductOut += $displayOut;
        }

        // Build daily movement history
        $inventoryMovements = [];
        $beginningInventory = $ingredient->beginning_inventory ?? 0;

        // Get all daily movements for this ingredient up to selected date
        // Key by string date to avoid Carbon object key mismatch
        $dailyMovements = \App\Models\IngredientDailyMovement::where('ingredient_id', $ingredient->id)
            ->where('movement_date', '<=', $selectedDate)
            ->orderBy('movement_date', 'asc')
            ->get()
            ->keyBy(function ($item) {
                return $item->movement_date->format('Y-m-d');
            });

        // Get per-day total_out from product_daily_batches (all types)
        $dailyOuts = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
            ->where('production_date', '<=', $selectedDate)
            ->selectRaw('production_date, SUM(total_batch) as daily_total_batch')
            ->groupBy('production_date')
            ->pluck('daily_total_batch', 'production_date');

        // Determine date range: from first movement or ingredient creation, up to selected date
        $firstMovementDate = $dailyMovements->keys()->sort()->first();
        $ingredientCreated = $ingredient->created_at ? $ingredient->created_at->format('Y-m-d') : null;
        $firstDate = $firstMovementDate ?? $ingredientCreated ?? $selectedDate;
        $startDate = \Carbon\Carbon::parse($firstDate);
        $endDate = \Carbon\Carbon::parse($selectedDate);

        $runningBalance = $beginningInventory;
        $isFirstRow = true;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $movement = $dailyMovements->get($dateStr);
            // null = no input yet, preserves empty state; actual value if saved
            $inItems = $movement ? $movement->in_items : null;
            $inValueForCalc = $inItems !== null ? (float) $inItems : 0;

            // Calculate total_out for this day (tiered when rules exist, else pivot)
            $dayOut = 0;
            if ($dailyOuts->get($dateStr, 0) > 0) {
                $dayProductBatches = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
                    ->where('production_date', $dateStr)
                    ->get();
                $dayOut = $this->calculateDayOutFromBatches(
                    $dayProductBatches,
                    $productBatchRules,
                    $productMeasurements
                );
            }

            $beginning = $runningBalance;
            $ending = $beginning + $inValueForCalc - $dayOut;

            $inventoryMovements[] = [
                'date' => $date->format('m/d/y'),
                'date_raw' => $dateStr,
                'beginning' => $beginning,
                'in' => $inItems,
                'total_out' => $dayOut > 0 ? $dayOut : null,
                'ending' => $ending,
                'has_input' => $inItems !== null,
            ];

            $runningBalance = $ending;
            $isFirstRow = false;
        }

        return view('supplies.SuppIngredients.SupplyNext', compact(
            'ingredient',
            'inventoryMovements',
            'productDisplay',
            'selectedDate',
            'selectedType',
            'grandTotalBatch',
            'totalProductOut'
        ));
    }

    /**
     * Save in_items for a specific date via AJAX.
     */
    public function updateDailyMovement(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'movement_date' => 'required|date',
            'in_items' => 'nullable|numeric',
        ]);

        $movementDate = $validated['movement_date'];
        // null/empty input = no value entered yet, store as null
        $inItemsRaw = $request->input('in_items');
        $inItems = ($inItemsRaw !== null && $inItemsRaw !== '') ? $inItemsRaw : null;
        $inValueForCalc = $inItems !== null ? (float) $inItems : 0;

        // Get product measurements for total_out calculation
        $ingredient->load(['products' => function ($query) {
            $query->withPivot('measurement');
        }]);
        $productIds = $ingredient->products->pluck('id');
        $productMeasurements = $ingredient->products->pluck('pivot.measurement', 'id')->toArray();

        // Calculate total_out for this date
        $dayOut = 0;
        $dayProductBatches = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
            ->where('production_date', $movementDate)
            ->get();

        // Get batch rules for all ingredient-product relationships
        $ingredientProductIds = DB::table('ingredient_product')
            ->whereIn('product_id', $productIds)
            ->where('ingredient_id', $ingredient->id)
            ->pluck('id', 'product_id')
            ->toArray();

        // Load batch rules for each product
        $productBatchRules = [];
        foreach ($ingredientProductIds as $productId => $ipId) {
            $rules = DB::table('batch_rules')
                ->where('ingredient_product_id', $ipId)
                ->orderBy('batch_limit')
                ->get(['batch_limit', 'measurement']);
            if ($rules->isNotEmpty()) {
                $productBatchRules[$productId] = $rules->toArray();
            }
        }

        $dayOut = $this->calculateDayOutFromBatches(
            $dayProductBatches,
            $productBatchRules,
            $productMeasurements
        );

        // Calculate beginning by walking forward from last known ending
        // This accounts for days with total_out but no movement record
        $prevMovement = \App\Models\IngredientDailyMovement::where('ingredient_id', $ingredient->id)
            ->where('movement_date', '<', $movementDate)
            ->orderBy('movement_date', 'desc')
            ->first();

        if ($prevMovement) {
            $runningBalance = (float) $prevMovement->ending;
            // Walk through days between prev movement and this date
            $walkStart = \Carbon\Carbon::parse($prevMovement->movement_date)->addDay();
            $walkEnd = \Carbon\Carbon::parse($movementDate)->subDay();
            for ($d = $walkStart->copy(); $d->lte($walkEnd); $d->addDay()) {
                $dStr = $d->format('Y-m-d');
                $dIn = 0; // no movement record = no in_items saved
                $dOut = 0;
                $dBatches = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
                    ->where('production_date', $dStr)->get();
                $dOut = $this->calculateDayOutFromBatches($dBatches, $productBatchRules, $productMeasurements);
                $runningBalance = $runningBalance + $dIn - $dOut;
            }
            $beginning = $runningBalance;
        } else {
            // No previous movement, walk from beginning_inventory
            $runningBalance = $ingredient->beginning_inventory ?? 0;
            $ingredientCreated = $ingredient->created_at ? $ingredient->created_at->format('Y-m-d') : $movementDate;
            $walkStart = \Carbon\Carbon::parse($ingredientCreated);
            $walkEnd = \Carbon\Carbon::parse($movementDate)->subDay();
            for ($d = $walkStart->copy(); $d->lte($walkEnd); $d->addDay()) {
                $dStr = $d->format('Y-m-d');
                $dIn = 0;
                $dBatches = \App\Models\ProductDailyBatch::whereIn('product_id', $productIds)
                    ->where('production_date', $dStr)->get();
                $dOut = $this->calculateDayOutFromBatches($dBatches, $productBatchRules, $productMeasurements);
                $runningBalance = $runningBalance + $dIn - $dOut;
            }
            $beginning = $runningBalance;
        }
        $ending = $beginning + $inValueForCalc - $dayOut;

        // Save/update the daily movement
        \App\Models\IngredientDailyMovement::updateOrCreate(
            [
                'ingredient_id' => $ingredient->id,
                'movement_date' => $movementDate,
            ],
            [
                'in_items' => $inItems,
                'total_out' => $dayOut,
                'ending' => $ending,
            ]
        );

        // Recalculate all subsequent days' endings
        $subsequentMovements = \App\Models\IngredientDailyMovement::where('ingredient_id', $ingredient->id)
            ->where('movement_date', '>', $movementDate)
            ->orderBy('movement_date', 'asc')
            ->get();

        $runningEnding = $ending;
        foreach ($subsequentMovements as $sub) {
            $subInValue = $sub->in_items !== null ? (float) $sub->in_items : 0;
            $sub->ending = $runningEnding + $subInValue - (float) $sub->total_out;
            $sub->save();
            $runningEnding = (float) $sub->ending;
        }

        // Recalculate totals up to this date
        $allMovements = \App\Models\IngredientDailyMovement::where('ingredient_id', $ingredient->id)
            ->where('movement_date', '<=', $movementDate)
            ->orderBy('movement_date', 'asc')
            ->get();
        $totalIn = 0;
        $totalOut = 0;
        foreach ($allMovements as $m) {
            if ($m->in_items !== null) $totalIn += (float) $m->in_items;
            $totalOut += (float) $m->total_out;
        }

        return response()->json([
            'success' => true,
            'ending' => number_format($ending, 1),
            'total_in' => number_format($totalIn, 1),
            'total_out' => number_format($totalOut, 1),
        ]);
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

    /**
     * Apply column divisor to a raw schedule quantity (matches BatchProduction::applyDivisor).
     */
    private function applyColumnDivisor(?\App\Models\ColumnConfig $column, float $rawValue): float
    {
        if ($rawValue <= 0) {
            return 0;
        }

        if (!$column || ($column->divisor_type ?? 'none') === 'none') {
            return $rawValue;
        }

        $divisor = (float) ($column->divisor_value ?? 1);

        return $divisor > 0 ? $rawValue / $divisor : $rawValue;
    }

    /**
     * Compute per-product batch totals from schedules with divisors applied.
     */
    private function computeProductBatchTotals(array $productIds, string $selectedDate, string $selectedType = 'all'): array
    {
        if (empty($productIds)) {
            return [];
        }

        $flavorIds = \App\Models\ProductFlavor::whereIn('product_id', $productIds)->pluck('id');

        if ($flavorIds->isEmpty()) {
            return [];
        }

        $query = \App\Models\ProductionSchedule::query()
            ->with(['productFlavor:id,product_id', 'productSize.columnConfig'])
            ->whereIn('product_flavor_id', $flavorIds)
            ->where('production_date', $selectedDate);

        if ($selectedType !== 'all') {
            $query->where('type', $selectedType);
        }

        $byProductAndType = [];

        foreach ($query->get() as $schedule) {
            $productId = $schedule->productFlavor?->product_id;
            if (!$productId) {
                continue;
            }

            $raw = (float) $schedule->batch_quantity;
            if ($raw <= 0) {
                continue;
            }

            $divisorBatch = $this->applyColumnDivisor($schedule->productSize?->columnConfig, $raw);
            $type = $schedule->type ?? 'Bottle';
            $byProductAndType[$productId][$type] = ($byProductAndType[$productId][$type] ?? 0) + $divisorBatch;
        }

        $totals = [];
        foreach ($byProductAndType as $productId => $byType) {
            $sum = 0;
            foreach ($byType as $type => $typeSum) {
                if ($type === 'Yogurt') {
                    $typeSum = $typeSum / 20;
                }
                $sum += $typeSum;
            }
            if ($sum > 0) {
                $totals[$productId] = (float) floor($sum);
            }
        }

        return $totals;
    }

    /**
     * Resolve the active batch rule for display (matches Batch Production logic).
     */
    private function resolveActiveRule(float $batchTotal, array $rules): ?array
    {
        if (empty($rules)) {
            return null;
        }

        foreach ($rules as $rule) {
            $limit = is_array($rule) ? $rule['batch_limit'] : $rule->batch_limit;
            if ($batchTotal <= $limit) {
                return [
                    'batch_limit' => (float) $limit,
                    'measurement' => is_array($rule) ? $rule['measurement'] : $rule->measurement,
                ];
            }
        }

        $lastRule = end($rules);

        return [
            'batch_limit' => (float) (is_array($lastRule) ? $lastRule['batch_limit'] : $lastRule->batch_limit),
            'measurement' => is_array($lastRule) ? $lastRule['measurement'] : $lastRule->measurement,
        ];
    }

    /**
     * Tiered out calculation when batch_rules exist.
     */
    private function calculateTieredOut(float $batchTotal, array $rules): float
    {
        if ($batchTotal <= 0 || empty($rules)) {
            return 0;
        }

        $totalOut = 0;
        $prevLimit = 0;
        $remaining = $batchTotal;

        foreach ($rules as $rule) {
            $limit = is_array($rule) ? $rule['batch_limit'] : $rule->batch_limit;
            $measurement = is_array($rule) ? $rule['measurement'] : $rule->measurement;
            $count = min($remaining, $limit - $prevLimit);
            if ($count > 0) {
                $totalOut += $count * floatval($measurement);
                $remaining -= $count;
                $prevLimit = $limit;
            }
            if ($remaining <= 0) {
                break;
            }
        }

        if ($remaining > 0) {
            $lastRule = end($rules);
            $lastMeasurement = is_array($lastRule) ? $lastRule['measurement'] : $lastRule->measurement;
            $totalOut += $remaining * floatval($lastMeasurement);
        }

        return $totalOut;
    }

    /**
     * Product out: tiered when rules exist, else batch × pivot measurement.
     */
    private function calculateProductOut(float $batchTotal, ?array $rules, ?float $pivotMeasurement): float
    {
        if ($batchTotal <= 0) {
            return 0;
        }

        if ($rules && !empty($rules)) {
            return $this->calculateTieredOut($batchTotal, $rules);
        }

        if ($pivotMeasurement) {
            return $batchTotal * floatval($pivotMeasurement);
        }

        return 0;
    }

    /**
     * Sum daily out across products from ProductDailyBatch rows.
     */
    private function calculateDayOutFromBatches($batches, array $productBatchRules, array $productMeasurements): float
    {
        $dayOut = 0;

        foreach ($batches->groupBy('product_id') as $productId => $productBatches) {
            $batchTotal = (float) $productBatches->sum('total_batch');
            $rules = $productBatchRules[$productId] ?? null;
            $pivotMeasurement = $productMeasurements[$productId] ?? null;
            $dayOut += $this->calculateProductOut($batchTotal, $rules, $pivotMeasurement);
        }

        return $dayOut;
    }
}
