<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $ingredient->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                @php
                    $totalBeginning = $ingredient->beginning_inventory ?? 0;
                    $totalReleased = $ingredient->inventoryTrackings->sum('in_released');
                    $lastTracking = $ingredient->inventoryTrackings->last();
                    $totalEnding = $lastTracking ? $lastTracking->ending : $totalBeginning;
                    
                    // Calculate average consumption per day (based on records)
                    $trackingCount = $ingredient->inventoryTrackings->count();
                    $averageConsumption = $trackingCount > 0 ? $totalReleased / $trackingCount : 0;
                    
                    // Days before run out (ending / average consumption)
                    $daysBeforeRunOut = $averageConsumption > 0 ? floor($totalEnding / $averageConsumption) : 0;
                @endphp
                
                <!-- Total Beginning -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-md p-3">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Beginning</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalBeginning, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ingredient->unit_of_measurement }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Released -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 dark:bg-red-900 rounded-md p-3">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Released</p>
                                <p class="text-lg font-bold text-red-600 dark:text-red-400">{{ number_format($totalReleased, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ingredient->unit_of_measurement }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Ending -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-md p-3">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Ending</p>
                                <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($totalEnding, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ingredient->unit_of_measurement }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Consumption -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-md p-3">
                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avg. Consumption</p>
                                <p class="text-lg font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($averageConsumption, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">per transaction</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Days Before Run Out -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 @if($daysBeforeRunOut <= 3) bg-red-100 dark:bg-red-900 @elseif($daysBeforeRunOut <= 7) bg-yellow-100 dark:bg-yellow-900 @else bg-purple-100 dark:bg-purple-900 @endif rounded-md p-3">
                                <svg class="w-5 h-5 @if($daysBeforeRunOut <= 3) text-red-600 dark:text-red-400 @elseif($daysBeforeRunOut <= 7) text-yellow-600 dark:text-yellow-400 @else text-purple-600 dark:text-purple-400 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Days Before Run Out</p>
                                <p class="text-lg font-bold @if($daysBeforeRunOut <= 3) text-red-600 dark:text-red-400 @elseif($daysBeforeRunOut <= 7) text-yellow-600 dark:text-yellow-400 @else text-purple-600 dark:text-purple-400 @endif">{{ $daysBeforeRunOut }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">days left</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Basic Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->name }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->sku ?? '-' }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->category->name ?? '-' }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit of Measurement</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->unit_of_measurement }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cost per Unit</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->formatted_cost }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Supplier</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->supplier ?? '-' }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Location</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->location ?? '-' }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expiry Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->expiry_date ? $ingredient->expiry_date->format('M d, Y') : '-' }}</dd>
                            </div>
                            @if($ingredient->description)
                            <div class="py-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->description }}</dd>
                            </div>
                            @endif
                            @if($ingredient->remarks)
                            <div class="py-3">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Remarks</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->remarks }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Inventory Details Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Inventory Details</h3>
                    </div>
                    <div class="p-6">
                        <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Beginning Inventory</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $ingredient->beginning_inventory ?? 0 }} {{ $ingredient->unit_of_measurement }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Received Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->received_date ? $ingredient->received_date->format('M d, Y') : '-' }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Received Quantity</dt>
                                <dd class="text-sm text-green-600 dark:text-green-400 font-semibold">+{{ $ingredient->received_quantity ?? 0 }} {{ $ingredient->unit_of_measurement }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Released Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->released_date ? $ingredient->released_date->format('M d, Y') : '-' }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Released Quantity</dt>
                                <dd class="text-sm text-red-600 dark:text-red-400 font-semibold">-{{ $ingredient->released_quantity ?? 0 }} {{ $ingredient->unit_of_measurement }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Minimum Stock Level</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->minimum_stock ?? 0 }} {{ $ingredient->unit_of_measurement }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Stock</dt>
                                <dd class="text-sm font-bold @if($ingredient->status === 'in_stock') text-green-600 dark:text-green-400 @elseif($ingredient->status === 'low_stock') text-yellow-600 dark:text-yellow-400 @else text-red-600 dark:text-red-400 @endif">{{ $ingredient->current_stock }} {{ $ingredient->unit_of_measurement }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Inventory Tracking Table -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Inventory Tracking</h3>
                    <button type="button" id="add-row-btn" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Row
                    </button>
                </div>
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 text-green-600 text-sm">{{ session('success') }}</div>
                    @endif
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Beginning</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">In (Released)</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Out</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ending</th>
                            </tr>
                        </thead>
                        <tbody id="tracking-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($ingredient->inventoryTrackings as $index => $tracking)
                                <tr data-id="{{ $tracking->id }}">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $tracking->beginning }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $tracking->in_released }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $tracking->out }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $tracking->total }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $tracking->ending }}</td>
                                </tr>
                            @endforeach
                            <!-- Input Row for New Entry -->
                            <tr id="input-row" class="hidden">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" id="new-row-no">{{ $ingredient->inventoryTrackings->count() + 1 }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" id="new-beginning">{{ $ingredient->inventoryTrackings->last()?->ending ?? $ingredient->beginning_inventory ?? 0 }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <input type="number" step="0.01" id="new-in-released" class="w-20 text-center text-sm border border-gray-300 rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <select id="new-out" class="w-28 text-sm border border-gray-300 rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                        <option value="">Select</option>
                                        <option value="Choco">Choco</option>
                                        <option value="Ricemilk">Ricemilk</option>
                                        <option value="Coffee">Coffee</option>
                                        <option value="Lacto">Lacto</option>
                                        <option value="Yogurt">Yogurt</option>
                                        <option value="Jelly">Jelly</option>
                                        <option value="Yog. 3.5oz">Yog. 3.5oz</option>
                                        <option value="C. cheese">C. cheese</option>
                                        <option value="Milkaroons">Milkaroons</option>
                                        <option value="Polvoron">Polvoron</option>
                                        <option value="Pastillas">Pastillas</option>
                                        <option value="W. taho">W. taho</option>
                                        <option value="L. choco">L. choco</option>
                                        <option value="Ice yog">Ice yog</option>
                                        <option value="Tone milk">Tone milk</option>
                                        <option value="Ice Cream">Ice Cream</option>
                                    </select>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" id="new-total">-</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100" id="new-ending">-</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Save Button for New Row -->
                    <div id="save-btn-container" class="mt-4 hidden">
                        <button type="button" id="save-tracking-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save
                        </button>
                        <button type="button" id="cancel-btn" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <script>
                const ingredientId = {{ $ingredient->id }};
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                // Elements
                const addRowBtn = document.getElementById('add-row-btn');
                const inputRow = document.getElementById('input-row');
                const saveBtnContainer = document.getElementById('save-btn-container');
                const saveTrackingBtn = document.getElementById('save-tracking-btn');
                const cancelBtn = document.getElementById('cancel-btn');
                const newInReleased = document.getElementById('new-in-released');
                const newOut = document.getElementById('new-out');
                const newTotal = document.getElementById('new-total');
                const newEnding = document.getElementById('new-ending');
                const newBeginning = document.getElementById('new-beginning');

    // Show input row
    addRowBtn.addEventListener('click', () => {
        inputRow.classList.remove('hidden');
        saveBtnContainer.classList.remove('hidden');
        addRowBtn.disabled = true;
        addRowBtn.classList.add('opacity-50');
    });

    // Cancel
    cancelBtn.addEventListener('click', () => {
        inputRow.classList.add('hidden');
        saveBtnContainer.classList.add('hidden');
        addRowBtn.disabled = false;
        addRowBtn.classList.remove('opacity-50');
        newInReleased.value = '';
        newOut.value = '';
        newTotal.textContent = '-';
        newEnding.textContent = '-';
    });

    // Calculate total and ending on input change
    newInReleased.addEventListener('input', () => {
        const beginning = parseFloat(newBeginning.textContent) || 0;
        const inReleased = parseFloat(newInReleased.value) || 0;
        // Formula: beginning - released = total
        const total = beginning - inReleased;
        newTotal.textContent = total.toFixed(2);
        newEnding.textContent = total.toFixed(2);
        
        // Validation: check if released exceeds beginning
        if (inReleased > beginning) {
            newInReleased.classList.add('border-red-500');
            newTotal.classList.add('text-red-500');
            newEnding.classList.add('text-red-500');
        } else {
            newInReleased.classList.remove('border-red-500');
            newTotal.classList.remove('text-red-500');
            newEnding.classList.remove('text-red-500');
        }
    });

    // Save tracking
    saveTrackingBtn.addEventListener('click', async () => {
        const inReleased = newInReleased.value;
        const out = newOut.value;
        const beginning = parseFloat(newBeginning.textContent) || 0;

        // Check if beginning is zero
        if (beginning <= 0) {
            alert('No more stock available. Please restock before releasing.');
            return;
        }

        // Check if released exceeds beginning
        if (parseFloat(inReleased) > beginning) {
            alert(`Released quantity (${inReleased}) exceeds available stock (${beginning}).`);
            return;
        }

        if (!inReleased && !out) {
            alert('Please enter a value for In (Released) or select a product for Out.');
            return;
        }

        try {
            const response = await fetch(`/supplies/${ingredientId}/inventory-tracking`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    in_released: inReleased,
                    out: out
                })
            });

            const result = await response.json();

            if (result.success) {
                // Reload page to show new data
                window.location.reload();
            } else {
                // Show actual error message
                let errorMsg = 'Failed to save.';
                if (result.errors) {
                    errorMsg += '\n' + Object.values(result.errors).flat().join('\n');
                } else if (result.message) {
                    errorMsg += '\n' + result.message;
                }
                alert(errorMsg);
                console.error('Server response:', result);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred: ' + error.message);
        }
    });
</script>
        </div>
    </div>
</x-app-layout>