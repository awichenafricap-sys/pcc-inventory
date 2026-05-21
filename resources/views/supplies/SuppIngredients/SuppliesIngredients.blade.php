<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Supplies - Ingredients') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('status'))
                        <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-md text-sm font-medium">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if($ingredients->count())
                        <table id="items-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingredients Name</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Beginning Inventory</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Receive</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Receive Items</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual Ending</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">System Ending</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variance</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Released/Used Items</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($ingredients as $ingredient)
                                    @php
                                        $systemEnding = $systemEndings[$ingredient->id] ?? 0;
                                        $variance = $variances[$ingredient->id] ?? 0;
                                        $status = $systemEnding <= 0 ? 'out_of_stock' : ($systemEnding <= ($ingredient->minimum_stock ?? 0) ? 'low_stock' : 'in_stock');
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ ($ingredients->currentPage() - 1) * $ingredients->perPage() + $loop->iteration }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('supplies.ingredients.detail', $ingredient->id) }}?date={{ request()->get('date', now()->format('Y-m-d')) }}" class="text-blue-600 dark:text-blue-400 hover:underline hover:text-blue-800 dark:hover:text-blue-300">{{ $ingredient->name }}</a>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $ingredient->unit_of_measurement ?? '' }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            <form action="{{ route('supplies.ingredients.field', $ingredient->id) }}" method="POST" class="inline-auto-save-form">
                                                @csrf
                                                <input type="hidden" name="field" value="beginning_inventory">
                                                <input type="number" name="value" value="{{ $ingredient->beginning_inventory ?? 0 }}" step="0.01" min="0" class="w-20 text-center text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 auto-save-field">
                                            </form>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            <form action="{{ route('supplies.ingredients.field', $ingredient->id) }}" method="POST" class="inline-auto-save-form">
                                                @csrf
                                                <input type="hidden" name="field" value="date_receive">
                                                <input type="date" name="value" value="{{ $ingredient->date_receive ? $ingredient->date_receive->format('Y-m-d') : '' }}" class="text-center text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 auto-save-field">
                                            </form>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            <form action="{{ route('supplies.ingredients.field', $ingredient->id) }}" method="POST" class="inline-auto-save-form">
                                                @csrf
                                                <input type="hidden" name="field" value="receive_items">
                                                <input type="number" name="value" value="{{ $ingredient->receive_items ?? 0 }}" step="0.01" min="0" class="w-20 text-center text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 auto-save-field">
                                            </form>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            <form action="{{ route('supplies.ingredients.field', $ingredient->id) }}" method="POST" class="inline-auto-save-form">
                                                @csrf
                                                <input type="hidden" name="field" value="actual_ending">
                                                <input type="number" name="value" value="{{ $ingredient->actual_ending ?? 0 }}" step="0.01" min="0" class="w-20 text-center text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 auto-save-field">
                                            </form>
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" data-ingredient-id="{{ $ingredient->id }}" data-field="system_ending">{{ $systemEnding }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center font-semibold {{ $variance != 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}" data-ingredient-id="{{ $ingredient->id }}" data-field="variance">{{ number_format($variance, 2) }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" data-ingredient-id="{{ $ingredient->id }}" data-field="released_used_items">{{ number_format($releasedUsedItemsArr[$ingredient->id] ?? 0, 1) }}</td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            @if($status === 'in_stock')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">In Stock</span>
                                            @elseif($status === 'low_stock')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Low Stock</span>
                                            @elseif($status === 'out_of_stock')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Out of Stock</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">-</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="{{ route('ingredients.edit', $ingredient->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium">Edit</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4 flex items-center justify-between">
                            {{ $ingredients->links() }}
                        </div>
                    @else
                        <table id="items-table" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingredients Name</th>
                                    <th class="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Beginning Inventory</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Receive</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Receive Items</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual Ending</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">System Ending</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variance</th>
                                    <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Released/Used Items</th>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const saveTimers = {};

        function autoSaveField(input) {
            const form = input.closest('form');
            if (!form) return;

            const key = form.action + '_' + form.querySelector('[name="field"]').value;
            clearTimeout(saveTimers[key]);

            input.style.borderColor = '#f59e0b';

            saveTimers[key] = setTimeout(function() {
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                })
                .then(function(res) {
                    if (!res.ok) return res.text().then(function(t) { throw new Error(t); });
                    return res.json();
                })
                .then(function(data) {
                    if (data.success) {
                        input.style.borderColor = '#22c55e';
                        setTimeout(function() { input.style.borderColor = ''; }, 1500);

                        // Update computed cells in the same row
                        var actionParts = form.action.split('/');
                        actionParts.pop(); // remove 'field'
                        var ingredientId = actionParts.pop(); // get ingredient ID
                        var row = form.closest('tr');
                        if (row) {
                            var releasedCell = row.querySelector('[data-field="released_used_items"][data-ingredient-id="' + ingredientId + '"]');
                            if (releasedCell && data.released_used_items) releasedCell.textContent = data.released_used_items;

                            var systemEndingCell = row.querySelector('[data-field="system_ending"][data-ingredient-id="' + ingredientId + '"]');
                            if (systemEndingCell && data.system_ending) systemEndingCell.textContent = data.system_ending;

                            var varianceCell = row.querySelector('[data-field="variance"][data-ingredient-id="' + ingredientId + '"]');
                            if (varianceCell && data.variance !== undefined) {
                                varianceCell.textContent = data.variance;
                                var varianceVal = parseFloat(data.variance);
                                varianceCell.className = varianceCell.className.replace(/text-red-600|text-red-400|text-green-600|text-green-400/g, '');
                                if (varianceVal !== 0) {
                                    varianceCell.classList.add('text-red-600', 'dark:text-red-400');
                                } else {
                                    varianceCell.classList.add('text-green-600', 'dark:text-green-400');
                                }
                            }
                        }
                    }
                })
                .catch(function(err) {
                    console.error('Auto-save error:', err);
                    input.style.borderColor = '#ef4444';
                    setTimeout(function() { input.style.borderColor = ''; }, 1500);
                });
            }, 500);
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('auto-save-field')) {
                autoSaveField(e.target);
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.classList.contains('auto-save-field')) {
                e.preventDefault();
                autoSaveField(e.target);
            }
        });
    });
    </script>
</x-app-layout>
