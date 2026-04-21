<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    private $rowCount = 0;
    private $successCount = 0;

    public function model(array $row)
    {
        $this->rowCount++;
        
        try {
            // Check if required fields exist
            if (empty($row['name']) || empty($row['category']) || empty($row['unit'])) {
                Log::warning('Missing required fields in row: ' . json_encode($row));
                return null;
            }

            // Generate a unique code if not provided
            $code = !empty($row['code']) ? trim($row['code']) : $this->generateUniqueCode();

            // Check if code already exists
            if (Product::where('code', $code)->exists()) {
                // If code exists, generate a unique one
                $code = $this->generateUniqueCode();
            }

            $product = new Product([
                'code' => $code,
                'name' => trim($row['name']),
                'category' => trim($row['category']),
                'unit' => trim($row['unit']),
                'current_stock' => (int) ($row['current_stock'] ?? 0),
                'reorder_level' => (int) ($row['reorder_level'] ?? 0),
                'description' => !empty($row['description']) ? trim($row['description']) : null,
            ]);

            $this->successCount++;
            
            Log::info('Product imported successfully: ' . $code);
            
            return $product;
            
        } catch (\Exception $e) {
            Log::error('Error importing row: ' . $e->getMessage() . ' - Data: ' . json_encode($row));
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'P-' . strtoupper(Str::random(5));
        } while (Product::where('code', $code)->exists());

        return $code;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }
}