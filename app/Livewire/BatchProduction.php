<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductionSchedule;
use App\Models\FlavorLayout;
use App\Models\ColumnConfig;
use App\Models\ProductFlavor;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Component;
use Carbon\Carbon;

class BatchProduction extends Component
{
    use WithPagination;
    #[Url(as: 'date', keep: true)]
    public $selectedDate;
    public $searchTerm = '';
    public $selectedType = 'Bottle';
    // Products are computed in render(), not stored as property
    public $formula = [];
    public $productNames = [];
    
    // Column Manager
    public $showColumnManager = false;
    public $newColumnName = '';
    public $newColumnLabel = '';
    public $editingColumnId = null;
    public $editColumnName = '';
    public $editColumnLabel = '';

    public function mount()
    {
        if (!$this->selectedDate) {
            $this->selectedDate = now()->format('Y-m-d');
        }
        $this->loadProductNames();
        $this->loadProducts();
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
        $this->loadProducts();
    }

    #[On('dateChanged')]
    public function handleDateChange($date)
    {
        $this->selectedDate = $date;
        $this->loadProducts();
    }

    public function updatedSearchTerm()
    {
        $this->loadProducts();
    }

    public function updatedSelectedType()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        // Products are computed via getProductsProperty()
        // This method exists for compatibility - no action needed
    }

    public function getProductsProperty()
    {
        // Table shows all products - no filters
        $query = Product::where('name', '!=', null)->where('name', '!=', '');

        return $query->distinct()->paginate(5)->through(function ($record) {
            $batchPerDay = $this->calculateBatchPerDayByName($record->name);
            $totalBatch = $this->calculateTotalBatchByName($record->name);

            // Load flavors for this product with batch and quantities
            $flavors = ProductFlavor::where('product_id', $record->id)
                ->get(['id', 'flavor_name', 'batch', 'qty_200ml', 'qty_500ml', 'qty_1000ml'])
                ->toArray();

            // Check for Plain - No Flavor
            $plainFlavor = ProductFlavor::where('product_id', $record->id)
                ->where('flavor_name', 'Plain - No Flavor')
                ->select('id', 'flavor_name', 'batch', 'qty_200ml', 'qty_500ml', 'qty_1000ml')
                ->first();

            // If no flavors but has Plain - No Flavor, add it
            if (empty($flavors) && $plainFlavor) {
                $flavors = [$plainFlavor->toArray()];
            }

            // Calculate totals from flavors
            $totalBatchFromFlavors = collect($flavors)->sum('batch');
            $total200ml = collect($flavors)->sum('qty_200ml');
            $total500ml = collect($flavors)->sum('qty_500ml');
            $total1000ml = collect($flavors)->sum('qty_1000ml');

            return [
                'id' => $record->id,
                'name' => $record->name,
                'category' => $record->category,
                'type' => $record->type,
                'container_size_ml' => $record->container_size_ml,
                'batch_per_day' => $batchPerDay,
                'total_batch' => $totalBatch,
                'flavors' => $flavors,
                'total_batch_flavors' => $totalBatchFromFlavors,
                'total_200ml' => $total200ml,
                'total_500ml' => $total500ml,
                'total_1000ml' => $total1000ml,
            ];
        });
    }


    public function calculateBatchPerDayByName($productName)
    {
        $product = Product::where('name', $productName)->first();
        if (!$product) {
            return 0;
        }

        $schedule = ProductionSchedule::where('product_id', $product->id)
            ->where('production_date', $this->selectedDate)
            ->first();

        return $schedule ? $schedule->batch_quantity : 0;
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

        return ProductionSchedule::where('product_id', $product->id)
            ->whereBetween('production_date', [$firstDayOfMonth, $selectedDateStr])
            ->sum('batch_quantity');
    }

    public function calculateBatchPerDay($productId)
    {
        $schedule = ProductionSchedule::where('product_id', $productId)
            ->where('production_date', $this->selectedDate)
            ->first();

        return $schedule ? $schedule->batch_quantity : 0;
    }

    public function calculateTotalBatch($productId)
    {
        $selectedDate = Carbon::parse($this->selectedDate);
        $firstDayOfMonth = $selectedDate->copy()->startOfMonth()->format('Y-m-d');
        $selectedDateStr = $selectedDate->format('Y-m-d');

        return ProductionSchedule::where('product_id', $productId)
            ->whereBetween('production_date', [$firstDayOfMonth, $selectedDateStr])
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
        $this->newColumnLabel = '';
    }

    public function addColumn()
    {
        $this->validate([
            'newColumnName' => 'required|string|max:50',
            'newColumnLabel' => 'required|string|max:50',
        ]);

        $maxOrder = ColumnConfig::forType($this->selectedType)->max('sort_order') ?? 0;

        ColumnConfig::create([
            'type' => $this->selectedType,
            'column_name' => $this->newColumnName,
            'column_label' => $this->newColumnLabel,
            'sort_order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        $this->newColumnName = '';
        $this->newColumnLabel = '';

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
        $flavor = ProductFlavor::find($flavorId);
        if ($flavor) {
            $flavor->$field = (int) $value;
            $flavor->save();
        }
    }

    public function updatePlainFlavor($productId, $field, $value)
    {
        // Find or create "Plain - No Flavor" for this product
        $flavor = ProductFlavor::firstOrCreate(
            [
                'product_id' => $productId,
                'flavor_name' => 'Plain - No Flavor',
            ],
            [
                'measurement' => 'N/A',
                'sizes' => 'All',
                'ingredients' => 'None',
                'batch' => 0,
                'qty_200ml' => 0,
                'qty_500ml' => 0,
                'qty_1000ml' => 0,
            ]
        );

        $flavor->$field = (int) $value;
        $flavor->save();
    }

    public function editColumn($id)
    {
        $column = ColumnConfig::find($id);
        if ($column) {
            $this->editingColumnId = $id;
            $this->editColumnName = $column->column_name;
            $this->editColumnLabel = $column->column_label;
        }
    }

    public function updateColumn()
    {
        $this->validate([
            'editColumnName' => 'required|string|max:50',
            'editColumnLabel' => 'required|string|max:50',
        ]);

        $column = ColumnConfig::find($this->editingColumnId);
        if ($column) {
            $column->update([
                'column_name' => $this->editColumnName,
                'column_label' => $this->editColumnLabel,
            ]);
        }

        $this->cancelEditColumn();
        session()->flash('message', 'Column updated successfully.');
    }

    public function cancelEditColumn()
    {
        $this->editingColumnId = null;
        $this->editColumnName = '';
        $this->editColumnLabel = '';
    }

    public function render()
    {
        return view('livewire.batch-production', [
            'products' => $this->products,
            'columns' => $this->columns,
            'allColumns' => $this->allColumns,
        ]);
    }
}
