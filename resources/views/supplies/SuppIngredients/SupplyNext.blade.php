<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $ingredient->name }} - Inventory Detail <span class="text-sm font-normal text-gray-500">({{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }})</span></h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Type Filter + Grand Total Card --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Type:</label>
                    <select id="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="applyFilter()">
                        <option value="all" {{ $selectedType === 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="Bottle" {{ $selectedType === 'Bottle' ? 'selected' : '' }}>Bottle</option>
                        <option value="Sachet" {{ $selectedType === 'Sachet' ? 'selected' : '' }}>Sachet</option>
                        <option value="Cup" {{ $selectedType === 'Cup' ? 'selected' : '' }}>Cup</option>
                    </select>
                </div>
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg px-5 py-3 shadow-sm">
                    <p class="text-xs font-medium opacity-80">Total Batch (All Types)</p>
                    <p class="text-2xl font-bold">{{ $grandTotalBatch ?: 0 }}</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Left Table: Products using this ingredient --}}
                <div class="flex-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Products Using This Ingredient</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider bg-[#556B2F] rounded-tl-lg">No</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Product Name</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Batch</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Measurement</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Out</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $productNo = 0;
                                    @endphp
                                    @forelse($ingredient->products as $product)
                                        @php
                                            $productNo++;
                                            $productId = $product->id;
                                            $display = $productDisplay[$productId] ?? [
                                                'active_rule' => null,
                                                'batch' => 0,
                                                'measurement' => null,
                                                'out' => 0,
                                            ];
                                            $activeRule = $display['active_rule'];
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $productNo }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $product->name }}
                                                @if($activeRule)
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                                        {{ $activeRule['batch_limit'] }} → {{ $activeRule['measurement'] }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">{{ $display['batch'] ?: '-' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">{{ $display['measurement'] ?? '-' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $display['out'] > 0 ? $display['out'] : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No products found using this ingredient.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Right Table: Inventory Movement Summary --}}
                <div class="flex-1 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Inventory Movement</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Beginning</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">In</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total Out</th>
                                        <th class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider bg-[#556B2F] rounded-tr-lg">Ending</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @php
                                        $totalIn = 0;
                                        $totalOut = 0;
                                    @endphp
                                    @forelse($inventoryMovements as $movement)
                                        @php
                                            $inVal = $movement['in'];
                                            $hasInput = $movement['has_input'] ?? false;
                                            $isCurrentDate = ($movement['date_raw'] ?? '') === $selectedDate;
                                            if ($inVal !== null) $totalIn += (float) $inVal;
                                            if ($movement['total_out'] !== null) $totalOut += (float) $movement['total_out'];
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $isCurrentDate ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100 {{ $isCurrentDate ? 'font-semibold' : '' }}">{{ $movement['date'] ?? '-' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ number_format($movement['beginning'], 1) }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                                @if($isCurrentDate)
                                                    <form action="{{ route('supplies.ingredients.daily-movement', $ingredient->id) }}" method="POST" class="inline-daily-movement-form" onsubmit="return false;">
                                                        @csrf
                                                        <input type="hidden" name="movement_date" value="{{ $movement['date_raw'] }}">
                                                        <input type="number" name="in_items" value="{{ $inVal }}" step="0.01" placeholder="-" class="w-20 text-center text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 daily-in-field">
                                                    </form>
                                                @else
                                                    @if($hasInput)
                                                        <span class="text-gray-900 dark:text-gray-100">{{ number_format((float) $inVal, 1) }}</span>
                                                    @else
                                                        <span class="text-gray-400 dark:text-gray-500">-</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $movement['total_out'] !== null ? number_format($movement['total_out'], 1) : '-' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100 font-medium" data-field="ending">{{ number_format($movement['ending'], 1) }}</td>
                                        </tr>
                                    @empty
                                        @php $beginInv = $ingredient->beginning_inventory ?? 0; @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 bg-blue-50 dark:bg-blue-900/20">
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100 font-semibold">{{ \Carbon\Carbon::parse($selectedDate)->format('m/d/y') }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ number_format($beginInv, 1) }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                                    <form action="{{ route('supplies.ingredients.daily-movement', $ingredient->id) }}" method="POST" class="inline-daily-movement-form" onsubmit="return false;">
                                                        @csrf
                                                        <input type="hidden" name="movement_date" value="{{ $selectedDate }}">
                                                        <input type="number" name="in_items" value="" step="0.01" placeholder="-" class="w-20 text-center text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 daily-in-field">
                                                    </form>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $totalProductOut > 0 ? number_format($totalProductOut, 1) : '-' }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100 font-medium" data-field="ending">{{ number_format($beginInv - ($totalProductOut ?? 0), 1) }}</td>
                                        </tr>
                                    @endforelse
                                    @if(count($inventoryMovements) > 0)
                                        <tr class="font-semibold">
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-white bg-[#556B2F]">Total</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">-</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" data-field="total-in">{{ number_format($totalIn, 1) }}</td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" data-field="total-out">{{ number_format($totalOut, 1) }}</td>
                                            @php
                                                $lastEnding = $inventoryMovements[count($inventoryMovements) - 1]['ending'];
                                            @endphp
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" data-field="total-ending">{{ number_format($lastEnding, 1) }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('supplies.ingredients') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Back to Ingredients List
                </a>
            </div>
        </div>
    </div>
    <script>
    function applyFilter() {
        const type = document.getElementById('typeFilter').value;
        const url = new URL(window.location.href);
        if (type === 'all') {
            url.searchParams.delete('type');
        } else {
            url.searchParams.set('type', type);
        }
        window.location.href = url.toString();
    }

    document.addEventListener('DOMContentLoaded', function() {
        let pendingSave = false;
        let savingInput = null;

        function saveDailyMovement(input) {
            const form = input.closest('form.inline-daily-movement-form');
            if (!form) return;

            // Prevent double-save from change+blur
            if (savingInput === input && pendingSave) return;

            input.style.borderColor = '#f59e0b';
            pendingSave = true;
            savingInput = input;

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                keepalive: true,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
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

                    var row = form.closest('tr');
                    if (row && data.ending) {
                        var endingCell = row.querySelector('[data-field="ending"]');
                        if (endingCell) endingCell.textContent = data.ending;
                    }

                    var table = form.closest('table');
                    if (table) {
                        var totalInCell = table.querySelector('[data-field="total-in"]');
                        if (totalInCell && data.total_in) totalInCell.textContent = data.total_in;
                        var totalOutCell = table.querySelector('[data-field="total-out"]');
                        if (totalOutCell && data.total_out) totalOutCell.textContent = data.total_out;
                        var totalEndingCell = table.querySelector('[data-field="total-ending"]');
                        if (totalEndingCell && data.ending) totalEndingCell.textContent = data.ending;
                    }
                }
                pendingSave = false;
                savingInput = null;
            })
            .catch(function(err) {
                console.error('Auto-save error:', err);
                input.style.borderColor = '#ef4444';
                setTimeout(function() { input.style.borderColor = ''; }, 1500);
                pendingSave = false;
                savingInput = null;
            });
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('daily-in-field')) {
                saveDailyMovement(e.target);
            }
        });

        document.addEventListener('focusout', function(e) {
            if (e.target.classList.contains('daily-in-field')) {
                saveDailyMovement(e.target);
            }
        }, true);

        document.addEventListener('keydown', function(e) {
            if (e.target.classList.contains('daily-in-field') && e.key === 'Enter') {
                e.preventDefault();
                e.target.blur();
                saveDailyMovement(e.target);
            }
        });

        // Prevent navigation while save is in progress
        window.addEventListener('beforeunload', function(e) {
            if (pendingSave) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });
    </script>
</x-app-layout>
