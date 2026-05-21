<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Exports\ProductsPdfExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! $request->user() || ! $request->user()->is_admin) {
                abort(403, 'Unauthorized access.');
            }

            return $next($request);
        });
    }

     public function index(Request $request)
    {
        $query = Product::query();

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                  ->orWhere('name', 'like', '%' . $search . '%')
                  ->orWhere('category', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Category filter
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $products = $query->with('ingredients', 'flavors.sizes.columnConfig')->paginate(10)->withQueryString();
        $ingredients = \App\Models\Ingredient::where('is_active', true)->orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $flavors = \App\Models\Ingredient::where('is_active', true)
            ->whereHas('category', function ($q) {
                $q->where('name', 'Flavor');
            })->orderBy('name')->get();
        $sizes = \App\Models\ColumnConfig::where('is_active', true)->where('column_name', '!=', 'batch')->orderBy('sort_order')->get()->groupBy('type');

        return view('products.index', compact('products', 'ingredients', 'categories', 'flavors', 'sizes'));
    }


    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:products,code|max:50',
            'name' => 'required|max:255',
            'category' => 'required|max:100',
            'flavors' => 'nullable|array',
            'flavors.*' => 'string|max:100',
            'flavor_sizes' => 'nullable|array',
            'flavor_sizes.*' => 'nullable|array',
            'flavor_sizes.*.*' => 'string|max:100',
            'type' => 'nullable|string|max:50',
            'unit' => 'required|max:50',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,id',
        ]);
        
        try {
            $data = $request->except('image', 'ingredients', 'flavors', 'flavor_sizes');

            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            $product = Product::create($data);

            // Sync ingredients
            if ($request->has('ingredients')) {
                $product->ingredients()->sync($request->ingredients);
            }

            // Create flavors and per-flavor sizes
            $flavorSizes = $request->input('flavor_sizes', []);
            if ($request->has('flavors')) {
                foreach ($request->flavors as $flavorName) {
                    $flavor = $product->flavors()->create([
                        'flavor_name' => $flavorName,
                        'is_active' => true,
                    ]);

                    // Create sizes for this specific flavor
                    if (isset($flavorSizes[$flavorName]) && is_array($flavorSizes[$flavorName])) {
                        foreach ($flavorSizes[$flavorName] as $sizeName) {
                            $columnConfig = \App\Models\ColumnConfig::where('column_name', $sizeName)
                                ->where('type', $request->type)->first();
                            $sizeMl = (int) preg_replace('/[^0-9]/', '', $sizeName);
                            $flavor->sizes()->create([
                                'column_config_id' => $columnConfig?->id,
                                'size_ml' => $sizeMl > 0 ? $sizeMl : 0,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            }

            return redirect()
                ->route('products.ingredients', $product->id)
                ->with('success', 'Product "' . $request->name . '" added successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Log the error for debugging
            Log::error('Product store error: ' . $e->getMessage());
            
            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()
                    ->withErrors(['code' => 'The product code "' . $request->code . '" already exists. Please use a different code.']);
            }
            
            // Other database errors
            return back()->withInput()
                ->withErrors(['error' => 'A database error occurred. Please try again or contact support.']);
                
        } catch (\Exception $e) {
            Log::error('Product store unexpected error: ' . $e->getMessage());
            
            return back()->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'code' => 'required|unique:products,code,' . $product->id . '|max:50',
            'name' => 'required|max:255',
            'category' => 'required|max:100',
            'flavors' => 'nullable|array',
            'flavors.*' => 'string|max:100',
            'flavor_sizes' => 'nullable|array',
            'flavor_sizes.*' => 'nullable|array',
            'flavor_sizes.*.*' => 'string|max:100',
            'type' => 'nullable|string|max:50',
            'unit' => 'required|max:50',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'exists:ingredients,id',
        ]);

        try {
            $data = $request->except('image', 'ingredients', 'flavors', 'flavor_sizes');

            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image if exists
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                
                // Store new image
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            $product->update($data);

            // Sync ingredients
            $product->ingredients()->sync($request->input('ingredients', []));

            // Sync flavors and per-flavor sizes - delete old, create new
            $product->flavors()->delete();
            $flavorSizes = $request->input('flavor_sizes', []);
            if ($request->has('flavors')) {
                foreach ($request->flavors as $flavorName) {
                    $flavor = $product->flavors()->create([
                        'flavor_name' => $flavorName,
                        'is_active' => true,
                    ]);

                    if (isset($flavorSizes[$flavorName]) && is_array($flavorSizes[$flavorName])) {
                        foreach ($flavorSizes[$flavorName] as $sizeName) {
                            $columnConfig = \App\Models\ColumnConfig::where('column_name', $sizeName)
                                ->where('type', $request->type)->first();
                            $sizeMl = (int) preg_replace('/[^0-9]/', '', $sizeName);
                            $flavor->sizes()->create([
                                'column_config_id' => $columnConfig?->id,
                                'size_ml' => $sizeMl > 0 ? $sizeMl : 0,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $product->name . '" updated successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Log the error for debugging
            Log::error('Product update error: ' . $e->getMessage());
            
            // Check if it's a duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()
                    ->withErrors(['code' => 'The product code "' . $request->code . '" already exists. Please use a different code.']);
            }
            
            // Other database errors
            return back()->withInput()
                ->withErrors(['error' => 'A database error occurred. Please try again or contact support.']);
                
        } catch (\Exception $e) {
            Log::error('Product update unexpected error: ' . $e->getMessage());
            
            return back()->withInput()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again.']);
        }
    }

    public function ingredientDetail(Product $product)
    {
        $product->load('ingredients.category', 'flavors');

        $ingredientProductIds = DB::table('ingredient_product')
            ->where('product_id', $product->id)
            ->pluck('id', 'ingredient_id');

        $ingredientBatchRules = [];
        foreach ($ingredientProductIds as $ingredientId => $ingredientProductId) {
            $rules = \App\Models\BatchRule::where('ingredient_product_id', $ingredientProductId)
                ->orderBy('batch_limit')
                ->get(['batch_limit', 'measurement']);
            if ($rules->isNotEmpty()) {
                $ingredientBatchRules[$ingredientId] = $rules;
            }
        }

        return view('products.productNext', compact('product', 'ingredientBatchRules'));
    }

    public function updateIngredientMeasurement(Request $request, Product $product)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'measurement' => 'nullable|string|max:100',
            'batch_limit' => 'nullable|integer|min:1',
        ]);

        $product->ingredients()->updateExistingPivot($request->ingredient_id, [
            'measurement' => $request->measurement,
            'batch_limit' => $request->batch_limit,
        ]);

        return response()->json(['success' => true]);
    }

    public function updateFlavorMeasurement(Request $request, $flavor)
    {
        $flavor = \App\Models\ProductFlavor::findOrFail($flavor);
        $flavor->measurement = $request->measurement;
        $flavor->save();

        return response()->json(['success' => true]);
    }

    public function saveBatchRules(Request $request, Product $product)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'rules' => 'present|array',
        ]);

        $rules = $request->input('rules', []);

        if (!empty($rules)) {
            $request->validate([
                'rules.*.batch_limit' => 'required|integer|min:1',
                'rules.*.measurement' => 'required|string|max:100',
            ]);
        }

        $ingredientProduct = DB::table('ingredient_product')
            ->where('product_id', $product->id)
            ->where('ingredient_id', $request->ingredient_id)
            ->first();

        if (!$ingredientProduct) {
            return response()->json(['success' => false, 'message' => 'Ingredient not found for this product'], 404);
        }

        \App\Models\BatchRule::where('ingredient_product_id', $ingredientProduct->id)->delete();

        foreach ($rules as $rule) {
            \App\Models\BatchRule::create([
                'ingredient_product_id' => $ingredientProduct->id,
                'batch_limit' => $rule['batch_limit'],
                'measurement' => $rule['measurement'],
            ]);
        }

        return response()->json(['success' => true, 'rules' => $rules]);
    }

    public function getBatchRules(Product $product, $ingredient)
    {
        $ingredientProduct = DB::table('ingredient_product')
            ->where('product_id', $product->id)
            ->where('ingredient_id', $ingredient)
            ->first();

        if (!$ingredientProduct) {
            return response()->json(['rules' => []]);
        }

        $rules = \App\Models\BatchRule::where('ingredient_product_id', $ingredientProduct->id)
            ->orderBy('batch_limit')
            ->get(['batch_limit', 'measurement']);

        return response()->json(['rules' => $rules]);
    }

    public function deleteFlavor($flavor)
    {
        $flavor = \App\Models\ProductFlavor::findOrFail($flavor);
        $flavor->delete();

        return response()->json(['success' => true]);
    }

    public function destroy(Product $product)
    {
        try {
            // Delete image file if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $productName = $product->name;
            $product->delete();
            
            return redirect()
                ->route('products.index')
                ->with('success', 'Product "' . $productName . '" deleted successfully.');
                
        } catch (\Exception $e) {
            Log::error('Product delete error: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'An error occurred while deleting the product.']);
        }
    }
    
    /**
     * Optional: Add method to remove image only
     */
    public function removeImage(Product $product)
    {
        try {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
                
                $product->update(['image' => null]);
                
                return response()->json(['success' => true, 'message' => 'Image removed successfully.']);
            }
            
            return response()->json(['success' => false, 'message' => 'No image found.']);
            
        } catch (\Exception $e) {
            Log::error('Remove image error: ' . $e->getMessage());
            
            return response()->json(['success' => false, 'message' => 'Error removing image.'], 500);
        }
    }

     /* Export products to Excel
     */
    public function exportExcel(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'category' => $request->category
        ];

        return Excel::download(new ProductsExport($filters), 'products-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Export products to CSV
     */
    public function exportCsv(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'category' => $request->category
        ];

        return Excel::download(new ProductsExport($filters), 'products-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Export products to PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'category' => $request->category
        ];

        $pdfExport = new ProductsPdfExport($filters);
        return $pdfExport->download();
    }

    /**
     * Import products from Excel/CSV
     */
    /**
 * Import products from Excel/CSV
 */
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:5120' // Max 5MB
    ]);

    try {
        $import = new ProductsImport();
        Excel::import($import, $request->file('file'));
        
        // Get counts from import class
        $successCount = $import->getSuccessCount();
        $totalRows = $import->getRowCount();
        
        if ($successCount > 0) {
            return redirect()->route('products.index')
                ->with('success', "Successfully imported {$successCount} out of {$totalRows} products!");
        } else {
            return redirect()->route('products.index')
                ->with('error', 'No products were imported. Please check your file format.');
        }
        
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();
        $errorMessages = [];
        foreach ($failures as $failure) {
            $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
        
        Log::error('Import validation failed: ' . implode(' | ', $errorMessages));
        
        return redirect()->route('products.index')
            ->with('error', 'Import validation failed: ' . implode(' | ', array_slice($errorMessages, 0, 3)));
            
    } catch (\Exception $e) {
        Log::error('Import error: ' . $e->getMessage());
        
        return redirect()->route('products.index')
            ->with('error', 'Error importing products: ' . $e->getMessage());
    }
}

    /**
     * Download sample import template
     */
    public function downloadTemplate()
    {
        $headers = [
            'code', 'name', 'category', 'unit', 'description'
        ];

        $sampleData = [
            ['P-001', 'Sample Product', 'Solid', 'Pieces', 'Sample description'],
            ['', 'Another Product', 'Liquid', 'Liters', 'Optional description']
        ];

        return Excel::download(new class($headers, $sampleData) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $headers;
            protected $data;

            public function __construct($headers, $data)
            {
                $this->headers = $headers;
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headers;
            }
        }, 'import-template.xlsx');
    }
}