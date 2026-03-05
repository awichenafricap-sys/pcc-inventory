<?php

namespace App\Exports;

use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductsPdfExport
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::query();

        // Apply filters if any
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }

        return $query->get();
    }

    public function download()
    {
        $products = $this->collection();
        
        $data = [
            'products' => $products,
            'date_generated' => now()->format('F d, Y H:i:s'),
            'total_products' => $products->count(),
            'filters' => $this->filters
        ];

        $pdf = Pdf::loadView('exports.products-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('products-list-' . now()->format('Y-m-d') . '.pdf');
    }
}