<!DOCTYPE html>
<html>
<head>
    <title>Products List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #5839a3;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #5839a3;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .status-in-stock {
            background-color: #d4edda;
            color: #155724;
        }
        .status-low-stock {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-out-stock {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .summary {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Products Inventory List</h1>
        <p>Generated on: {{ $date_generated }}</p>
    </div>

    @if(!empty($filters))
    <div class="summary">
        <h3>Applied Filters:</h3>
        @if(!empty($filters['search']))
        <span class="summary-item"><strong>Search:</strong> {{ $filters['search'] }}</span>
        @endif
        @if(!empty($filters['category']))
        <span class="summary-item"><strong>Category:</strong> {{ $filters['category'] }}</span>
        @endif
    </div>
    @endif

    <div class="summary">
        <span class="summary-item"><strong>Total Products:</strong> {{ $total_products }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Unit</th>
                <th>Stock</th>
                <th>Reorder Level</th>
                <th>Status</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category }}</td>
                <td>{{ $product->unit }}</td>
                <td>{{ $product->current_stock }}</td>
                <td>{{ $product->reorder_level }}</td>
                <td>
                    @php
                        $status = 'In Stock';
                        $statusClass = 'status-in-stock';
                        if ($product->current_stock == 0) {
                            $status = 'Out of Stock';
                            $statusClass = 'status-out-stock';
                        } elseif ($product->current_stock <= $product->reorder_level) {
                            $status = 'Low Stock';
                            $statusClass = 'status-low-stock';
                        }
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                </td>
                <td>{{ $product->description ?? 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center;">No products found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically by the Inventory Management System.</p>
    </div>
</body>
</html>