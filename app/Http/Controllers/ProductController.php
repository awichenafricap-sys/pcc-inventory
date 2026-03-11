<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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

        $products = $query->paginate(10)->withQueryString();

        return view('products.index', compact('products'));
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
            'unit' => 'required|max:50',
            'beginning' => 'nullable|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'ending' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max - mas practical
        ]);
        
        try {
            $data = $request->except('image');
            
            // Calculate ending automatically: reorder_level - current_stock
            $reorderLevel = $request->reorder_level ?? 0;
            $currentStock = $request->current_stock ?? 0;
            $data['ending'] = $reorderLevel - $currentStock;
            
            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $imagePath = $request->file('image')->store('products', 'public');
                $data['image'] = $imagePath;
            }

            Product::create($data);

            return redirect()
                ->route('products.index')
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
            'unit' => 'required|max:50',
            'beginning' => 'nullable|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'ending' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        try {
            $data = $request->except('image');
            
            // Calculate ending automatically: reorder_level - current_stock
            $reorderLevel = $request->reorder_level ?? 0;
            $currentStock = $request->current_stock ?? 0;
            $data['ending'] = $reorderLevel - $currentStock;
            
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
            'code', 'name', 'category', 'unit', 'current_stock', 'reorder_level', 'description'
        ];

        $sampleData = [
            ['P-001', 'Sample Product', 'Solid', 'Pieces', 100, 10, 'Sample description'],
            ['', 'Another Product', 'Liquid', 'Liters', 50, 5, 'Optional description']
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