<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Category',
            'Unit',
            'Current Stock',
            'Reorder Level',
            'Description',
            'Status',
            'Created At'
        ];
    }

    public function map($product): array
    {
        // Determine status
        $status = 'In Stock';
        if ($product->current_stock == 0) {
            $status = 'Out of Stock';
        } elseif ($product->current_stock <= $product->reorder_level) {
            $status = 'Low Stock';
        }

        return [
            $product->code,
            $product->name,
            $product->category,
            $product->unit,
            $product->current_stock,
            $product->reorder_level,
            $product->description,
            $status,
            $product->created_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}