<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Supplies - Ingredients') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Beginning Inventory Card -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium uppercase tracking-wider">Beginning Inventory</p>
                            <p id="total-beginning" class="text-white text-3xl font-bold mt-2">{{ number_format($totalBeginning ?? 0, 2) }}</p>
                            <p class="text-blue-200 text-sm mt-1">{{ now()->format('F Y') }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Ending Inventory Card -->
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-700 rounded-xl shadow-lg p-6 transform hover:scale-105 transition-transform duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium uppercase tracking-wider">Ending Inventory</p>
                            <p id="total-ending" class="text-white text-3xl font-bold mt-2">{{ number_format($totalEnding ?? 0, 2) }}</p>
                            <p class="text-emerald-200 text-sm mt-1">{{ now()->format('F Y') }}</p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div id="success-msg" class="mb-4 text-green-600">{{ session('success') }}</div>
                    @endif

                    @if($ingredients->count())
                        <table id="items-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No.</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingredients Name</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categories</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Beginning Inventory</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Receive</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual Ending</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variance</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Released/Used Item</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($ingredients as $index => $ingredient)
                                    @php
                                        $variance = ($ingredient->beginning_inventory ?? 0) - ($ingredient->current_stock ?? 0);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" data-id="{{ $ingredient->id }}">
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ ($ingredients->currentPage() - 1) * $ingredients->perPage() + $loop->iteration }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('supplies.next', $ingredient->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">{{ $ingredient->name }}</a>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ingredient->category->name ?? '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ingredient->unit_of_measurement ?? '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ ($ingredient->beginning_inventory ?? '') != '' ? $ingredient->beginning_inventory : '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">{{ $ingredient->received_date ?? '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center font-semibold @if($ingredient->status === 'in_stock') text-green-600 dark:text-green-400 @elseif($ingredient->status === 'low_stock') text-yellow-600 dark:text-yellow-400 @else text-red-600 dark:text-red-400 @endif ending-inventory">{{ $ingredient->current_stock ?? '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $variance != 0 ? $variance : '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">{{ $ingredient->released_quantity ?? '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            @if($ingredient->status === 'in_stock')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">In Stock</span>
                                            @elseif($ingredient->status === 'low_stock')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Low Stock</span>
                                            @elseif($ingredient->status === 'out_of_stock')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Out of Stock</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">-</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="{{ route('supplies.next', $ingredient->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 mr-2">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $ingredients->links() }}</div>
                    @else
                        <table id="items-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No.</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingredients Name</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Categories</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Beginning Inventory</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Receive</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual Ending</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variance</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Released/Used Item</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            </tbody>
                        </table>
                        <div id="no-items-msg" class="text-center text-gray-400 dark:text-gray-500 p-6">No ingredients found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;

    // Function to update summary cards from server
    async function updateSummaryCards() {
        try {
            const res = await fetch('/supplies/ingredients', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (res.ok) {
                const data = await res.json();
                
                const beginningCard = document.getElementById('total-beginning');
                const endingCard = document.getElementById('total-ending');
                
                if (beginningCard && data.totalBeginning !== undefined) {
                    beginningCard.textContent = parseFloat(data.totalBeginning).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    beginningCard.classList.add('animate-pulse');
                    setTimeout(() => beginningCard.classList.remove('animate-pulse'), 500);
                }
                if (endingCard && data.totalEnding !== undefined) {
                    endingCard.textContent = parseFloat(data.totalEnding).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    endingCard.classList.add('animate-pulse');
                    setTimeout(() => endingCard.classList.remove('animate-pulse'), 500);
                }
            }
        } catch (err) {
            console.error('Failed to update summary cards:', err);
        }
    }

    // Inline inventory update handlers
    document.querySelectorAll('#items-table tbody tr').forEach(row => {
        const ingredientId = row.dataset.id;
        if (!ingredientId) return;

        row.querySelectorAll('input[data-field]').forEach(input => {
            let saveTimeout;
            input.addEventListener('change', async () => {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(async () => {
                    const data = {};
                    data[input.dataset.field] = input.value;
                    
                    try {
                        const res = await fetch(`/ingredients/${ingredientId}/inventory`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        });

                        if (!res.ok) {
                            const errData = await res.json();
                            if (res.status === 422) {
                                alert(Object.values(errData.errors).flat().join('\n'));
                            } else {
                                throw new Error('Server error');
                            }
                            return;
                        }

                        const result = await res.json();
                        
                        // Update ending inventory display
                        const endingCell = row.querySelector('.ending-inventory');
                        if (endingCell && result.current_stock !== undefined) {
                            endingCell.textContent = result.current_stock;
                            // Update color based on status
                            endingCell.classList.remove('text-green-600', 'text-yellow-600', 'text-red-600', 'dark:text-green-400', 'dark:text-yellow-400', 'dark:text-red-400');
                            if (result.status === 'in_stock') {
                                endingCell.classList.add('text-green-600', 'dark:text-green-400');
                            } else if (result.status === 'low_stock') {
                                endingCell.classList.add('text-yellow-600', 'dark:text-yellow-400');
                            } else {
                                endingCell.classList.add('text-red-600', 'dark:text-red-400');
                            }
                        }

                        // Update summary cards
                        updateSummaryCards();

                        // Show save indicator
                        input.classList.add('border-green-500');
                        setTimeout(() => input.classList.remove('border-green-500'), 1000);

                    } catch (err) {
                        console.error(err);
                        input.classList.add('border-red-500');
                        setTimeout(() => input.classList.remove('border-red-500'), 2000);
                    }
                }, 500);
            });
        });
    });

</script>
