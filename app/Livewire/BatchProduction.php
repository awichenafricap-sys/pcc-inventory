<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductionSchedule;
use App\Models\ProductDailyBatch;
use App\Models\ColumnConfig;
use App\Models\ProductFlavor;
use App\Models\ProductSize;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BatchProduction extends Component
{
    use WithPagination;
    #[Url(as: 'date', keep: true)]
    public $selectedDate;
    public $searchTerm = '';
    #[Url(as: 'type', keep: true)]
    public $selectedType = 'Bottle';
    // Products are computed in render(), not stored as property
    public $formula = [];
    public $productNames = [];
    public $sizeQuantities = [];
    
    // Column Manager
    public $showColumnManager = false;
    public $newColumnName = '';
    public $editingColumnId = null;
    public $editColumnName = '';
    public $editSizeVolume = '';
    public $editDivisorType = 'none';
    public $editDivisorValue = '';

    // Dates with Data Dropdown
    public $showDatesDropdown = false;

    // Daily/Monthly Modal
    public $showDailyMonthlyModal = false;

    public function getDivisorBase()
    {
        return $this->selectedType === 'Yogurt' ? 1000 : 20000;
    }

    public function updatedEditSizeVolume($value)
    {
        if ($this->editDivisorType === 'auto') {
            $volume = (int) $value;
            if ($volume > 0) {
                $this->editDivisorValue = (int) floor($this->getDivisorBase() / $volume);
            } else {
                $this->editDivisorValue = '';
            }
        }
    }

    public function updatedEditDivisorType($value)
    {
        if ($value === 'auto') {
            $volume = (int) $this->editSizeVolume;
            if ($volume > 0) {
                $this->editDivisorValue = (int) floor($this->getDivisorBase() / $volume);
            } else {
                $this->editDivisorValue = '';
            }
        } elseif ($value === 'none') {
            $this->editDivisorValue = '';
        }
    }

    // Add Size Modal
    public $showAddSizeModal = false;
    public $newSizeName = '';
    public $newSizeVolume = '';
    public $newSizeDivisorType = 'none'; // 'none', 'auto'
    public $newSizeDivisor = '';

    public function updatedNewSizeVolume($value)
    {
        if ($this->newSizeDivisorType === 'auto') {
            $volume = (int) $value;
            if ($volume > 0) {
                $this->newSizeDivisor = (int) floor($this->getDivisorBase() / $volume);
            } else {
                $this->newSizeDivisor = '';
            }
        }
    }

    public function updatedNewSizeDivisorType($value)
    {
        if ($value === 'auto') {
            $volume = (int) $this->newSizeVolume;
            if ($volume > 0) {
                $this->newSizeDivisor = (int) floor($this->getDivisorBase() / $volume);
            } else {
                $this->newSizeDivisor = '';
            }
        } elseif ($value === 'none') {
            $this->newSizeDivisor = '';
        }
    }

    public function mount()
    {
        if (!$this->selectedDate) {
            $this->selectedDate = now()->format('Y-m-d');
        }
        
        // Check if there's a stored type from localStorage (passed via JavaScript)
        // This will be handled by JavaScript on the frontend
        $this->loadProductNames();
        $this->loadProducts();
        $this->loadSizeQuantities();
    }

    public function loadProductNames()
    {
        $this->productNames = Product::where('name', '!=', null)
            ->where('name', '!=', '')
            ->distinct()
            ->pluck('name')
            ->sort()
            ->values()
            ->toArray();
    }


    public function updatedSelectedDate()
    {
        $this->resetPage();
        $this->loadProducts();
        $this->loadSizeQuantities();
    }

    #[On('dateChanged')]
    public function handleDateChange($date)
    {
        $this->selectedDate = $date;
        $this->resetPage();
        $this->loadProducts();
        $this->loadSizeQuantities();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->loadProducts();
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
        $this->loadProducts();
        $this->loadSizeQuantities();
    }

    public function selectType($type)
    {
        $this->selectedType = $type;
        $this->resetPage();
        $this->loadProducts();
        $this->loadSizeQuantities();
    }

    public function loadProducts()
    {
        // Products are computed via getProductsProperty()
        // This method exists for compatibility - no action needed
    }

    public function loadSizeQuantities()
    {
        $this->sizeQuantities = [];
        $allSizes = $this->allSizes;

        foreach ($this->products as $product) {
            foreach ($product['flavors'] as $flavor) {
                foreach ($allSizes as $sizeCol) {
                    $colId = $sizeCol['id'];
                    $sizeData = collect($flavor['sizes'] ?? [])->firstWhere('column_config_id', $colId);
                    $key = $this->sizeQuantityKey($flavor['id'], $colId);
                    $this->sizeQuantities[$key] = $sizeData ? ($sizeData['quantity'] ?? 0) : 0;
                }
            }
        }
    }

    public function applyDivisor($columnId, $value)
    {
        $sizeCol = collect($this->allSizes)->firstWhere('id', $columnId);
        if (!$sizeCol || ($sizeCol['divisor_type'] ?? 'none') === 'none') {
            return (float) $value;
        }

        $rawValue = (int) $value;
        if ($rawValue <= 0) {
            return 0;
        }

        $divisor = $sizeCol['divisor_value'] ?? 1;

        if ($divisor > 0) {
            return $rawValue / $divisor;
        }

        return (float) $rawValue;
    }

    public function updatedSizeQuantities($value, $key)
    {
        $rawValue = (int) $value;

        $parsed = $this->parseSizeQuantityKey($key);
        if (!$parsed) {
            return;
        }
        [$flavorId, $columnId] = $parsed;

        // Get size_ml from column config
        $sizeCol = collect($this->allSizes)->firstWhere('id', $columnId);
        $sizeMl = $sizeCol ? $sizeCol['size_ml'] : 0;

        // Find or create ProductSize
        $size = ProductSize::firstOrCreate(
            [
                'product_flavor_id' => $flavorId,
                'column_config_id' => $columnId,
            ],
            [
                'size_ml' => $sizeMl,
                'quantity' => 0,
                'is_active' => true,
            ]
        );

        // Store raw input value (divisor applied at display time for batch total)
        if ($rawValue > 0) {
            ProductionSchedule::updateOrCreate(
                [
                    'product_flavor_id' => $flavorId,
                    'product_size_id' => $size->id,
                    'production_date' => $this->selectedDate,
                    'type' => $this->selectedType,
                ],
                [
                    'batch_quantity' => $rawValue,
                    'status' => 'planned',
                ]
            );
        } else {
            ProductionSchedule::where('product_flavor_id', $flavorId)
                ->where('product_size_id', $size->id)
                ->where('production_date', $this->selectedDate)
                ->where('type', $this->selectedType)
                ->delete();
        }

        // Sync total_batch for the product on this date/type
        $flavor = ProductFlavor::find($flavorId);
        if ($flavor) {
            $this->syncProductDailyBatch($flavor->product_id);
        }
    }

    /**
     * Recalculate and store total_batch for a product on the selected date/type.
     */
    public function syncProductDailyBatch($productId)
    {
        $product = Product::find($productId);
        $productName = $product ? $product->name : null;

        $flavorIds = ProductFlavor::where('product_id', $productId)
            ->where('is_active', true)
            ->pluck('id');

        $batchSum = 0;
        foreach ($flavorIds as $fId) {
            foreach ($this->allSizes as $sc) {
                $rawVal = (int) ($this->sizeQuantities[$this->sizeQuantityKey($fId, $sc['id'])] ?? 0);
                $batchSum += $this->applyDivisor($sc['id'], $rawVal);
            }
        }
        if ($this->selectedType === 'Yogurt') {
            $batchSum = $batchSum / 20;
        }
        $totalBatch = (int) floor($batchSum);

        ProductDailyBatch::updateOrCreate(
            [
                'product_id' => $productId,
                'production_date' => $this->selectedDate,
                'type' => $this->selectedType,
            ],
            [
                'product_name' => $productName,
                'total_batch' => $totalBatch,
            ]
        );
    }

    public function getProductsProperty()
    {
        $query = Product::where('name', '!=', null)->where('name', '!=', '');
        
        // Filter by selected type
        if ($this->selectedType) {
            $query->where('type', $this->selectedType);
        }
        
        $paginated = $query->distinct()->paginate(5);
        $productIds = $paginated->getCollection()->pluck('id')->toArray();

        // Batch: load all flavors with sizes for all products on this page
        $allFlavors = ProductFlavor::whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->with('sizes')
            ->get()
            ->groupBy('product_id');

        $allFlavorIds = $allFlavors->flatten()->pluck('id')->toArray();

        // Batch: load all production schedules for these flavors on selected date/type
        $allSchedules = ProductionSchedule::whereIn('product_flavor_id', $allFlavorIds)
            ->where('production_date', $this->selectedDate)
            ->where('type', $this->selectedType)
            ->get()
            ->groupBy('product_flavor_id');

        // Batch: load all stored daily batches
        $allStoredBatches = ProductDailyBatch::whereIn('product_id', $productIds)
            ->where('production_date', $this->selectedDate)
            ->where('type', $this->selectedType)
            ->pluck('total_batch', 'product_id')
            ->toArray();

        // Load batch rules for these products
        $productBatchRules = [];
        $ingredientProducts = DB::table('ingredient_product')
            ->whereIn('product_id', $productIds)
            ->get();

        foreach ($ingredientProducts as $ip) {
            $rules = DB::table('batch_rules')
                ->where('ingredient_product_id', $ip->id)
                ->orderBy('batch_limit')
                ->get()
                ->map(function ($rule) {
                    return [
                        'batch_limit' => $rule->batch_limit,
                        'measurement' => $rule->measurement,
                    ];
                })
                ->toArray();

            if (!empty($rules)) {
                // Store rules for this product
                if (!isset($productBatchRules[$ip->product_id])) {
                    $productBatchRules[$ip->product_id] = [];
                }
                $productBatchRules[$ip->product_id] = $rules;
            }
        }

        // Build flavor-to-product lookup map
        $flavorToProduct = [];
        foreach ($allFlavors as $productId => $flavors) {
            foreach ($flavors as $flavor) {
                $flavorToProduct[$flavor->id] = $productId;
            }
        }

        // Derive batchPerDay from already-loaded allSchedules (no extra query)
        $batchPerDayMap = [];
        foreach ($allSchedules as $flavorId => $schedules) {
            $pid = $flavorToProduct[$flavorId] ?? null;
            if ($pid !== null) {
                $batchPerDayMap[$pid] = ($batchPerDayMap[$pid] ?? 0) + $schedules->sum('batch_quantity');
            }
        }

        // Batch: calculate totalBatch (monthly) for all products at once
        $selectedDate = Carbon::parse($this->selectedDate);
        $totalBatchMap = [];
        if (!empty($allFlavorIds)) {
            $monthlySchedules = ProductionSchedule::whereIn('product_flavor_id', $allFlavorIds)
                ->whereBetween('production_date', [
                    $selectedDate->copy()->startOfMonth()->format('Y-m-d'),
                    $selectedDate->format('Y-m-d'),
                ])
                ->where('type', $this->selectedType)
                ->selectRaw('product_flavor_id, SUM(batch_quantity) as total')
                ->groupBy('product_flavor_id')
                ->pluck('total', 'product_flavor_id')
                ->toArray();

            foreach ($monthlySchedules as $flavorId => $total) {
                $pid = $flavorToProduct[$flavorId] ?? null;
                if ($pid !== null) {
                    $totalBatchMap[$pid] = ($totalBatchMap[$pid] ?? 0) + $total;
                }
            }
        }

        $paginated->through(function ($record) use ($allFlavors, $allSchedules, $allStoredBatches, $batchPerDayMap, $totalBatchMap, $productBatchRules) {
            $flavorRecords = $allFlavors->get($record->id, collect());

            // Auto-create a "Plain" flavor if product has none
            if ($flavorRecords->isEmpty()) {
                $plainFlavor = ProductFlavor::create([
                    'product_id' => $record->id,
                    'flavor_name' => 'Plain',
                    'is_active' => true,
                ]);
                $flavorRecords = collect([$plainFlavor->load('sizes')]);
            }

            $flavors = $flavorRecords->map(function ($flavor) use ($allSchedules) {
                    $schedules = $allSchedules->get($flavor->id, collect())->keyBy('product_size_id');

                    $sizesData = $flavor->sizes->map(function ($size) use ($schedules) {
                        $schedule = $schedules->get($size->id);
                        return [
                            'id' => $size->id,
                            'column_config_id' => $size->column_config_id,
                            'size_ml' => $size->size_ml,
                            'price' => $size->price,
                            'sku' => $size->sku,
                            'quantity' => $schedule ? $schedule->batch_quantity : 0,
                            'schedule_id' => $schedule ? $schedule->id : null,
                        ];
                    })->toArray();

                    $totalQty = collect($sizesData)->sum('quantity');

                    return [
                        'id' => $flavor->id,
                        'flavor_name' => $flavor->flavor_name,
                        'measurement' => $flavor->measurement,
                        'batch' => $totalQty,
                        'sizes' => $sizesData,
                    ];
                })->toArray();

            $totalBatchFromFlavors = collect($flavors)->sum('batch');

            return [
                'id' => $record->id,
                'name' => $record->name,
                'category' => $record->category,
                'type' => $record->type,
                'container_size_ml' => $record->container_size_ml,
                'batch_per_day' => $batchPerDayMap[$record->id] ?? 0,
                'total_batch' => $totalBatchMap[$record->id] ?? 0,
                'stored_total_batch' => $allStoredBatches[$record->id] ?? 0,
                'flavors' => $flavors,
                'total_batch_flavors' => $totalBatchFromFlavors,
                'batch_rules' => $productBatchRules[$record->id] ?? [],
            ];
        });

        return $paginated;
    }

    /**
     * Get active size columns from ColumnConfig for table headers
     * Returns array of ['size_ml' => int, 'label' => string]
     */
    public function getAllSizesProperty()
    {
        return ColumnConfig::forType($this->selectedType)
            ->active()
            ->ordered()
            ->where('column_name', '!=', 'batch')
            ->get()
            ->map(function ($col) {
                $sizeMl = (int) preg_replace('/[^0-9]/', '', $col->column_name);
                return [
                    'id' => $col->id,
                    'size_ml' => $sizeMl,
                    'label' => $col->column_label,
                    'divisor_type' => $col->divisor_type ?? 'none',
                    'divisor_value' => $col->divisor_value ?? null,
                ];
            })
            ->values()
            ->toArray();
    }

    public function getDatesWithDataProperty()
    {
        return ProductionSchedule::selectRaw('DISTINCT production_date')
            ->orderBy('production_date', 'desc')
            ->limit(60)
            ->pluck('production_date')
            ->toArray();
    }

    public function toggleDatesDropdown()
    {
        $this->showDatesDropdown = !$this->showDatesDropdown;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->showDatesDropdown = false;
        $this->resetPage();
        $this->loadProducts();
        $this->loadSizeQuantities();
    }

    public function getDailyTotalProperty()
    {
        return ProductionSchedule::where('production_date', $this->selectedDate)
            ->sum('batch_quantity');
    }

    public function getMonthlyTotalProperty()
    {
        $selectedDate = Carbon::parse($this->selectedDate);
        return ProductionSchedule::whereBetween('production_date', [
                $selectedDate->copy()->startOfMonth()->format('Y-m-d'),
                $selectedDate->format('Y-m-d'),
            ])
            ->sum('batch_quantity');
    }

    public function getDailySizeTotalsProperty()
    {
        $colIds = collect($this->allSizes)->pluck('id')->toArray();
        $sizes = ProductSize::whereIn('column_config_id', $colIds)->get();

        $scheduleTotals = ProductionSchedule::where('production_date', $this->selectedDate)
            ->where('type', $this->selectedType)
            ->whereIn('product_size_id', $sizes->pluck('id'))
            ->selectRaw('product_size_id, SUM(batch_quantity) as total')
            ->groupBy('product_size_id')
            ->pluck('total', 'product_size_id')
            ->toArray();

        // Map product_size_id => column_config_id
        $result = [];
        foreach ($sizes as $size) {
            if (isset($scheduleTotals[$size->id])) {
                $result[$size->column_config_id] = ($result[$size->column_config_id] ?? 0) + $scheduleTotals[$size->id];
            }
        }
        return $result;
    }

    public function getMonthlySizeTotalsProperty()
    {
        $selectedDate = Carbon::parse($this->selectedDate);
        $colIds = collect($this->allSizes)->pluck('id')->toArray();
        $sizes = ProductSize::whereIn('column_config_id', $colIds)->get();

        $scheduleTotals = ProductionSchedule::whereBetween('production_date', [
                $selectedDate->copy()->startOfMonth()->format('Y-m-d'),
                $selectedDate->format('Y-m-d'),
            ])
            ->where('type', $this->selectedType)
            ->whereIn('product_size_id', $sizes->pluck('id'))
            ->selectRaw('product_size_id, SUM(batch_quantity) as total')
            ->groupBy('product_size_id')
            ->pluck('total', 'product_size_id')
            ->toArray();

        $result = [];
        foreach ($sizes as $size) {
            if (isset($scheduleTotals[$size->id])) {
                $result[$size->column_config_id] = ($result[$size->column_config_id] ?? 0) + $scheduleTotals[$size->id];
            }
        }
        return $result;
    }


    public function getTypeSummariesProperty()
    {
        $types = ['Bottle', 'Sachet', 'Cup', 'Yogurt', 'Batch'];
        $summaries = [];

        // Single query: totals grouped by type
        $totalsByType = ProductionSchedule::where('production_date', $this->selectedDate)
            ->whereIn('type', $types)
            ->selectRaw('type, SUM(batch_quantity) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // Single query: all active columns for all types
        $allColumns = ColumnConfig::whereIn('type', $types)
            ->active()
            ->ordered()
            ->where('column_name', '!=', 'batch')
            ->get()
            ->groupBy('type');

        // Single query: all product sizes for these columns
        $allColIds = $allColumns->flatten()->pluck('id')->toArray();
        $allSizes = ProductSize::whereIn('column_config_id', $allColIds)
            ->get();
        $sizeToColumn = $allSizes->pluck('column_config_id', 'id')->toArray();

        // Single query: schedule totals grouped by product_size_id and type
        $scheduleTotals = ProductionSchedule::where('production_date', $this->selectedDate)
            ->whereIn('type', $types)
            ->whereIn('product_size_id', $allSizes->pluck('id'))
            ->selectRaw('type, product_size_id, SUM(batch_quantity) as total')
            ->groupBy('type', 'product_size_id')
            ->get();

        // Pre-group schedule totals by type
        $scheduleByType = [];
        foreach ($scheduleTotals as $row) {
            if (!isset($scheduleByType[$row->type])) {
                $scheduleByType[$row->type] = [];
            }
            $scheduleByType[$row->type][$row->product_size_id] = $row->total;
        }

        foreach ($types as $type) {
            $typeColumns = $allColumns->get($type, collect());
            $typeColIds = $typeColumns->pluck('id')->toArray();
            $typeSizes = $allSizes->whereIn('column_config_id', $typeColIds);

            $sizeBreakdown = [];
            $typeScheduleTotals = $scheduleByType[$type] ?? [];
            foreach ($typeSizes as $size) {
                if (isset($typeScheduleTotals[$size->id])) {
                    $sizeBreakdown[$size->column_config_id] = ($sizeBreakdown[$size->column_config_id] ?? 0) + $typeScheduleTotals[$size->id];
                }
            }

            $summaries[$type] = [
                'total' => $totalsByType[$type] ?? 0,
                'sizeBreakdown' => $sizeBreakdown,
                'columns' => $typeColumns->map(fn($col) => [
                    'id' => $col->id,
                    'label' => $col->column_label,
                ])->toArray(),
            ];
        }

        return $summaries;
    }

    public function getMonthlyTypeSummariesProperty()
    {
        $types = ['Bottle', 'Sachet', 'Cup', 'Yogurt', 'Batch'];
        $selectedDate = Carbon::parse($this->selectedDate);
        $summaries = [];

        // Single query: monthly totals grouped by type
        $totalsByType = ProductionSchedule::whereBetween('production_date', [
                $selectedDate->copy()->startOfMonth()->format('Y-m-d'),
                $selectedDate->format('Y-m-d'),
            ])
            ->whereIn('type', $types)
            ->selectRaw('type, SUM(batch_quantity) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();

        // Single query: all active columns for all types
        $allColumns = ColumnConfig::whereIn('type', $types)
            ->active()
            ->ordered()
            ->where('column_name', '!=', 'batch')
            ->get()
            ->groupBy('type');

        // Single query: all product sizes for these columns
        $allColIds = $allColumns->flatten()->pluck('id')->toArray();
        $allSizes = ProductSize::whereIn('column_config_id', $allColIds)->get();

        // Single query: monthly schedule totals grouped by type and product_size_id
        $scheduleTotals = ProductionSchedule::whereBetween('production_date', [
                $selectedDate->copy()->startOfMonth()->format('Y-m-d'),
                $selectedDate->format('Y-m-d'),
            ])
            ->whereIn('type', $types)
            ->whereIn('product_size_id', $allSizes->pluck('id'))
            ->selectRaw('type, product_size_id, SUM(batch_quantity) as total')
            ->groupBy('type', 'product_size_id')
            ->get();

        // Pre-group schedule totals by type
        $scheduleByType = [];
        foreach ($scheduleTotals as $row) {
            if (!isset($scheduleByType[$row->type])) {
                $scheduleByType[$row->type] = [];
            }
            $scheduleByType[$row->type][$row->product_size_id] = $row->total;
        }

        foreach ($types as $type) {
            $typeColumns = $allColumns->get($type, collect());
            $typeColIds = $typeColumns->pluck('id')->toArray();
            $typeSizes = $allSizes->whereIn('column_config_id', $typeColIds);

            $sizeBreakdown = [];
            $typeScheduleTotals = $scheduleByType[$type] ?? [];
            foreach ($typeSizes as $size) {
                if (isset($typeScheduleTotals[$size->id])) {
                    $sizeBreakdown[$size->column_config_id] = ($sizeBreakdown[$size->column_config_id] ?? 0) + $typeScheduleTotals[$size->id];
                }
            }

            $summaries[$type] = [
                'total' => $totalsByType[$type] ?? 0,
                'sizeBreakdown' => $sizeBreakdown,
                'columns' => $typeColumns->map(fn($col) => [
                    'id' => $col->id,
                    'label' => $col->column_label,
                ])->toArray(),
            ];
        }

        return $summaries;
    }

    public function calculateBatchPerDayByName($productName)
    {
        $product = Product::where('name', $productName)->first();
        if (!$product) {
            return 0;
        }

        // Get all flavor IDs for this product
        $flavorIds = ProductFlavor::where('product_id', $product->id)->pluck('id');

        return ProductionSchedule::whereIn('product_flavor_id', $flavorIds)
            ->where('production_date', $this->selectedDate)
            ->where('type', $this->selectedType)
            ->sum('batch_quantity');
    }

    public function calculateTotalBatchByName($productName)
    {
        $product = Product::where('name', $productName)->first();
        if (!$product) {
            return 0;
        }

        $selectedDate = Carbon::parse($this->selectedDate);
        $firstDayOfMonth = $selectedDate->copy()->startOfMonth()->format('Y-m-d');
        $selectedDateStr = $selectedDate->format('Y-m-d');

        $flavorIds = ProductFlavor::where('product_id', $product->id)->pluck('id');

        return ProductionSchedule::whereIn('product_flavor_id', $flavorIds)
            ->whereBetween('production_date', [$firstDayOfMonth, $selectedDateStr])
            ->where('type', $this->selectedType)
            ->sum('batch_quantity');
    }


    public function getColumnsProperty()
    {
        return ColumnConfig::forType($this->selectedType)
            ->active()
            ->ordered()
            ->get();
    }

    public function getAllColumnsProperty()
    {
        return ColumnConfig::forType($this->selectedType)
            ->ordered()
            ->get();
    }

    public function toggleColumnManager()
    {
        $this->showColumnManager = !$this->showColumnManager;
        $this->newColumnName = '';
    }

    public function addColumn()
    {
        $this->validate([
            'newColumnName' => 'required|string|max:50',
        ]);

        $maxOrder = ColumnConfig::forType($this->selectedType)->max('sort_order') ?? 0;

        ColumnConfig::create([
            'type' => $this->selectedType,
            'column_name' => $this->newColumnName,
            'column_label' => $this->newColumnName,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        $this->newColumnName = '';

        session()->flash('message', 'Column added successfully.');
    }

    public function toggleColumnActive($id)
    {
        $column = ColumnConfig::find($id);
        if ($column) {
            $column->update(['is_active' => !$column->is_active]);
        }
    }

    public function deleteColumn($id)
    {
        $column = ColumnConfig::find($id);
        if ($column) {
            $column->delete();
        }
    }

    public function updateFlavorField($flavorId, $field, $value)
    {
        // Batch is now derived from size quantities per date via production_schedules
        // No longer saved to product_flavors
    }

    public function editColumn($id)
    {
        $column = ColumnConfig::find($id);
        if ($column) {
            $this->editingColumnId = $id;
            $this->editColumnName = $column->column_name;
            $this->editDivisorType = $column->divisor_type ?? 'none';
            $this->editDivisorValue = $column->divisor_value ?? '';
            if ($column->divisor_type === 'auto' && $column->divisor_value) {
                $this->editSizeVolume = (int) floor($this->getDivisorBase() / $column->divisor_value);
            } else {
                $this->editSizeVolume = '';
            }
        }
    }

    public function updateColumn()
    {
        $this->validate([
            'editColumnName' => 'required|string|max:50',
        ]);

        // Determine divisor value
        $divisorType = $this->editDivisorType;
        $divisorValue = null;
        if ($divisorType === 'auto') {
            $volume = (int) $this->editSizeVolume;
            $divisorValue = $volume > 0 ? (int) floor($this->getDivisorBase() / $volume) : null;
        }

        $column = ColumnConfig::find($this->editingColumnId);
        if ($column) {
            $column->update([
                'column_name' => $this->editColumnName,
                'column_label' => $this->editColumnName,
                'divisor_type' => $divisorType,
                'divisor_value' => $divisorValue,
            ]);
        }

        $this->cancelEditColumn();
        session()->flash('message', 'Column updated successfully.');
    }

    public function cancelEditColumn()
    {
        $this->editingColumnId = null;
        $this->editColumnName = '';
        $this->editSizeVolume = '';
        $this->editDivisorType = 'none';
        $this->editDivisorValue = '';
    }

    public function openAddSizeModal()
    {
        $this->showAddSizeModal = true;
        $this->newSizeName = '';
        $this->newSizeVolume = '';
        $this->newSizeDivisorType = 'none';
        $this->newSizeDivisor = '';
    }

    public function closeAddSizeModal()
    {
        $this->showAddSizeModal = false;
        $this->newSizeName = '';
        $this->newSizeVolume = '';
        $this->newSizeDivisorType = 'none';
        $this->newSizeDivisor = '';
    }

    public function saveNewSize()
    {
        $rules = [
            'newSizeName' => 'required|string|max:50',
            'newSizeVolume' => 'required|integer|min:1',
            'newSizeDivisorType' => 'required|in:none,auto',
        ];

        $this->validate($rules);

        // Determine divisor values
        $divisorType = $this->newSizeDivisorType;
        $divisorValue = null;
        if ($divisorType === 'auto') {
            $divisorValue = (int) floor($this->getDivisorBase() / (int) $this->newSizeVolume);
        }

        $maxOrder = ColumnConfig::forType($this->selectedType)->max('sort_order') ?? 0;

        ColumnConfig::updateOrCreate(
            [
                'type' => $this->selectedType,
                'column_name' => $this->newSizeName,
            ],
            [
                'column_label' => $this->newSizeName,
                'sort_order' => $maxOrder + 1,
                'is_active' => true,
                'divisor_type' => $divisorType,
                'divisor_value' => $divisorValue,
            ]
        );

        $this->closeAddSizeModal();
        session()->flash('message', 'Size added successfully.');
    }

    private function sizeQuantityKey(int $flavorId, int $columnId): string
    {
        return "{$flavorId}_{$columnId}";
    }

    private function parseSizeQuantityKey(string $key): ?array
    {
        if (!str_contains($key, '_')) {
            return null;
        }

        [$flavorId, $columnId] = explode('_', $key, 2);

        return [(int) $flavorId, (int) $columnId];
    }

    /**
     * Resolve active batch rule from live batch count (updates as quantities change).
     */
    public function resolveActiveRule(float $batchTotal, array $rules): ?array
    {
        if (empty($rules) || $batchTotal <= 0) {
            return null;
        }

        foreach ($rules as $rule) {
            $limit = (float) $rule['batch_limit'];
            if ($batchTotal <= $limit) {
                return [
                    'batch_limit' => $limit,
                    'measurement' => $rule['measurement'],
                ];
            }
        }

        $lastRule = end($rules);

        return [
            'batch_limit' => (float) $lastRule['batch_limit'],
            'measurement' => $lastRule['measurement'],
        ];
    }

    public function render()
    {
        return view('livewire.batch-production', [
            'products' => $this->products,
            'columns' => $this->columns,
            'allColumns' => $this->allColumns,
            'allSizes' => $this->allSizes,
            'dailyTotal' => $this->dailyTotal,
            'monthlyTotal' => $this->monthlyTotal,
            'dailySizeTotals' => $this->dailySizeTotals,
            'monthlySizeTotals' => $this->monthlySizeTotals,
            'typeSummaries' => $this->typeSummaries,
            'monthlyTypeSummaries' => $this->monthlyTypeSummaries,
            'datesWithData' => $this->datesWithData,
        ]);
    }
}
