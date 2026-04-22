<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductFlavor;
use App\Models\Ingredient;
use App\Models\ColumnConfig;
use Livewire\Attributes\Url;

class EditManage extends Component
{
    #[Url]
    public $productId;
    public $type;

    public $product;
    public $productName = '';
    public $productType = '';

    // Modal
    public $showModal = false;

    // Flavor form
    public $flavorName = '';
    public $measurement = '';
    public $selectedSizes = [];
    public $selectedIngredient = '';

    // Flavors list
    public $flavors = [];

    // Editing
    public $editingFlavorId = null;

    public function mount($productId = null, $type = null)
    {
        $this->productId = $productId;
        $this->type = $type ?? 'Bottle';
        if ($this->productId) {
            $this->loadProduct();
            $this->loadFlavors();
        }
    }

    public function loadProduct()
    {
        $this->product = Product::find($this->productId);
        if ($this->product) {
            $this->productName = $this->product->name;
            // Use passed type from route, fallback to product type
            $this->productType = $this->type ?? $this->product->type ?? 'Bottle';
        }
    }

    public function loadFlavors()
    {
        // Load flavors from database
        $this->flavors = ProductFlavor::where('product_id', $this->productId)
            ->get()
            ->map(function ($flavor) {
                return [
                    'id' => $flavor->id,
                    'name' => $flavor->flavor_name,
                    'measurement' => $flavor->measurement,
                    'sizes' => $flavor->sizes,
                    'ingredients' => $flavor->ingredients,
                ];
            })->toArray();
    }

    public function getSizesProperty()
    {
        return ColumnConfig::forType($this->productType)
            ->active()
            ->ordered()
            ->where('column_name', '!=', 'batch')
            ->get();
    }

    public function getIngredientsProperty()
    {
        return Ingredient::orderBy('name')->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->flavorName = '';
        $this->measurement = '';
        $this->selectedSizes = [];
        $this->selectedIngredient = '';
        $this->editingFlavorId = null;
    }

    public function saveFlavor()
    {
        $this->validate([
            'flavorName' => 'required|string|max:255',
            'measurement' => 'required|string|max:100',
            'selectedSizes' => 'required|array|min:1',
            'selectedIngredient' => 'required|string',
        ]);

        if ($this->editingFlavorId) {
            // Update existing flavor in database
            ProductFlavor::where('id', $this->editingFlavorId)->update([
                'flavor_name' => $this->flavorName,
                'measurement' => $this->measurement,
                'sizes' => implode(', ', $this->selectedSizes),
                'ingredients' => $this->selectedIngredient,
            ]);
        } else {
            // Add new flavor to database
            ProductFlavor::create([
                'product_id' => $this->productId,
                'flavor_name' => $this->flavorName,
                'measurement' => $this->measurement,
                'sizes' => implode(', ', $this->selectedSizes),
                'ingredients' => $this->selectedIngredient,
            ]);
        }

        $this->loadFlavors();
        $this->closeModal();
        session()->flash('message', 'Flavor saved successfully.');
    }

    public function editFlavor($id)
    {
        $flavor = collect($this->flavors)->firstWhere('id', $id);
        if ($flavor) {
            $this->editingFlavorId = $id;
            $this->flavorName = $flavor['name'];
            $this->measurement = $flavor['measurement'];
            $this->selectedSizes = explode(', ', $flavor['sizes']);
            $this->selectedIngredient = $flavor['ingredients'];
            $this->showModal = true;
        }
    }

    public function deleteFlavor($id)
    {
        ProductFlavor::where('id', $id)->delete();
        $this->loadFlavors();
        session()->flash('message', 'Flavor deleted successfully.');
    }

    public function render()
    {
        return view('livewire.edit-manage', [
            'sizes' => $this->sizes,
            'ingredients' => $this->ingredients,
        ]);
    }
}
