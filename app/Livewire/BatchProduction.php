<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\ProductionSchedule;
use App\Models\FlavorLayout;
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
    public $selectedCategory = '';
    public $selectedType = '';
    // Products are computed in render(), not stored as property
    public $formula = [];
    public $categories = [];
    public $productNames = [];

    public function mount()
    {
        if (!$this->selectedDate) {
            $this->selectedDate = now()->format('Y-m-d');
        }
        $this->loadCategories();
        $this->loadProductNames();
        $this->loadProducts();
    }

    public function loadCategories()
    {
        $this->categories = Product::where('category', '!=', null)
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values()
            ->toArray();
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

    public function updatedSelectedCategory()
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

            return [
                'id' => $record->id,
                'name' => $record->name,
                'category' => $record->category,
                'type' => $record->type,
                'container_size_ml' => $record->container_size_ml,
                'batch_per_day' => $batchPerDay,
                'total_batch' => $totalBatch,
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


    public function render()
    {
        return view('livewire.batch-production', [
            'products' => $this->products,
        ]);
    }
}
