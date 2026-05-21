<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $product->name }} - Ingredients</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Ingredients ({{ $product->ingredients->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider bg-[#5839a3] rounded-tl-lg">No</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ingredients</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Category</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Measurement</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider bg-[#5839a3] rounded-tr-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($product->ingredients as $index => $ingredient)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ingredient->name }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                            @if($ingredient->category)
                                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $ingredient->category->name }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">{{ $ingredient->unit_of_measurement ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center"
                                            id="measurement-cell-{{ $ingredient->id }}"
                                            data-pivot-measurement="{{ $ingredient->pivot->measurement ?? '' }}">
                                            @if(isset($ingredientBatchRules[$ingredient->id]) && $ingredientBatchRules[$ingredient->id]->isNotEmpty())
                                                <div class="flex flex-col items-center gap-1">
                                                    @foreach($ingredientBatchRules[$ingredient->id] as $rule)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                                            {{ $rule->batch_limit }} → {{ $rule->measurement }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <input type="text"
                                                    class="w-20 px-2 py-1 text-sm text-center border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:border-[#5839a3] focus:ring-1 focus:ring-[#5839a3]"
                                                    value="{{ $ingredient->pivot->measurement ?? '' }}"
                                                    data-product-id="{{ $product->id }}"
                                                    data-ingredient-id="{{ $ingredient->id }}"
                                                    onchange="saveMeasurement(this)"
                                                    placeholder="-">
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                            <button onclick="openMeasurementModal({{ $product->id }}, {{ $ingredient->id }}, '{{ $ingredient->name }}')" class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition mr-2">Measurement</button>
                                            <a href="{{ route('supplies.ingredients.detail', $ingredient->id) }}?date={{ request()->get('date', now()->format('Y-m-d')) }}" class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-[#5839a3] rounded-lg hover:bg-[#4a2f8c] transition">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No ingredients assigned to this product.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Flavors</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-white uppercase tracking-wider bg-[#5839a3] rounded-tl-lg">Flavor Name</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-600 dark:text-gray-300 uppercase tracking-wider">Measurement</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-white uppercase tracking-wider bg-[#5839a3] rounded-tr-lg">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($product->flavors as $flavor)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $flavor->flavor_name }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                            <input type="number" step="0.01" min="0"
                                                class="w-20 px-2 py-1 text-sm text-center border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:border-[#5839a3] focus:ring-1 focus:ring-[#5839a3]"
                                                value="{{ $flavor->measurement ?? '' }}"
                                                data-flavor-id="{{ $flavor->id }}"
                                                onchange="saveFlavorMeasurement(this)"
                                                placeholder="-">
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-sm text-center">
                                            <button onclick="deleteFlavor({{ $flavor->id }}, this)" class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition">Delete</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No flavors found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Back to Products List
                </a>
            </div>
        </div>
    </div>

    <!-- Measurement Modal -->
    <div id="measurementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Set Batch Rules</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Ingredient: <span id="modalIngredientName" class="font-semibold"></span>
                </p>
                
                <!-- Rules Container -->
                <div id="rulesContainer" class="space-y-3 mb-4">
                </div>
                
                <!-- Add Rule Button -->
                <button type="button" onclick="addRuleRow()" class="w-full mb-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition text-sm font-medium">
                    + Add Rule
                </button>
                <button type="button" onclick="clearAllRules()" class="w-full mb-4 px-4 py-2 bg-red-50 text-red-600 rounded-md hover:bg-red-100 transition text-sm font-medium">
                    Remove All Rules
                </button>
                
                <!-- Rules Preview -->
                <div id="rulesPreview" class="bg-gray-50 rounded-md p-3 mb-4 hidden">
                    <h4 class="text-xs font-semibold text-gray-700 mb-2">Rules Preview:</h4>
                    <div id="rulesPreviewList" class="text-xs text-gray-600 space-y-1"></div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeMeasurementModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">Cancel</button>
                    <button onclick="saveMeasurementFromModal()" class="px-4 py-2 bg-[#5839a3] text-white rounded-md hover:bg-[#4a2f8c] transition">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function saveFlavorMeasurement(input) {
            const flavorId = input.dataset.flavorId;
            const measurement = input.value;

            fetch(`/product-flavors/${flavorId}/measurement`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ measurement: measurement }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.style.borderColor = '#22c55e';
                    setTimeout(() => { input.style.borderColor = ''; }, 1000);
                }
            })
            .catch(() => {
                input.style.borderColor = '#ef4444';
                setTimeout(() => { input.style.borderColor = ''; }, 1000);
            });
        }

        function deleteFlavor(flavorId, btn) {
            if (!confirm('Are you sure you want to delete this flavor?')) return;

            fetch(`/product-flavors/${flavorId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.closest('tr').remove();
                }
            })
            .catch(() => {
                alert('Failed to delete flavor.');
            });
        }

        let currentProductId = null;
        let currentIngredientId = null;

        function openMeasurementModal(productId, ingredientId, ingredientName) {
            currentProductId = productId;
            currentIngredientId = ingredientId;

            document.getElementById('modalIngredientName').textContent = ingredientName;

            // Clear existing rules and add one empty row
            const container = document.getElementById('rulesContainer');
            container.innerHTML = '';
            addRuleRow();

            // Load existing rules if any
            loadExistingRules(productId, ingredientId);

            document.getElementById('measurementModal').classList.remove('hidden');
        }

        function closeMeasurementModal() {
            document.getElementById('measurementModal').classList.add('hidden');
            currentProductId = null;
            currentIngredientId = null;
            document.getElementById('rulesPreview').classList.add('hidden');
        }

        function addRuleRow(batchLimit = '', measurement = '') {
            const container = document.getElementById('rulesContainer');
            const ruleRow = document.createElement('div');
            ruleRow.className = 'rule-row flex items-end gap-2';
            ruleRow.innerHTML = `
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Batch Limit:</label>
                    <input type="number" class="batch-limit-input w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-200 text-sm" placeholder="Enter batch limit" value="${batchLimit}" oninput="updateRulesPreview()">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Measurement:</label>
                    <input type="text" class="measurement-input w-full px-3 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:text-gray-200 text-sm" placeholder="Enter measurement" value="${measurement}" oninput="updateRulesPreview()">
                </div>
                <button type="button" onclick="removeRuleRow(this)" class="mb-0.5 px-2 py-2 text-red-600 hover:bg-red-50 rounded-md transition" title="Remove rule">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            container.appendChild(ruleRow);
            updateRulesPreview();
        }

        function removeRuleRow(btn) {
            const container = document.getElementById('rulesContainer');
            btn.closest('.rule-row')?.remove();
            if (container.querySelectorAll('.rule-row').length === 0) {
                addRuleRow();
            }
            updateRulesPreview();
        }

        function clearAllRules() {
            if (!confirm('Remove all batch rules? The measurement input field will be shown again.')) {
                return;
            }
            document.getElementById('rulesContainer').innerHTML = '';
            addRuleRow();
            saveMeasurementFromModal(true);
        }

        function updateRulesPreview() {
            const container = document.getElementById('rulesContainer');
            const ruleRows = container.querySelectorAll('.rule-row');
            const previewList = document.getElementById('rulesPreviewList');
            const previewDiv = document.getElementById('rulesPreview');

            let rules = [];
            ruleRows.forEach(row => {
                const batchLimit = row.querySelector('.batch-limit-input').value;
                const measurement = row.querySelector('.measurement-input').value;
                if (batchLimit && measurement) {
                    rules.push({ batch_limit: batchLimit, measurement: measurement });
                }
            });

            // Sort rules by batch limit
            rules.sort((a, b) => parseInt(a.batch_limit) - parseInt(b.batch_limit));

            if (rules.length > 0) {
                previewDiv.classList.remove('hidden');
                previewList.innerHTML = rules.map(rule => `
                    <div class="flex items-center gap-2">
                        <span class="font-medium">Batch ${rule.batch_limit}</span>
                        <span>→</span>
                        <span class="font-medium">${rule.measurement}</span>
                    </div>
                `).join('');
            } else {
                previewDiv.classList.add('hidden');
            }
        }

        function loadExistingRules(productId, ingredientId) {
            fetch(`/products/${productId}/ingredients/${ingredientId}/rules`)
                .then(response => response.json())
                .then(data => {
                    if (data.rules && data.rules.length > 0) {
                        const container = document.getElementById('rulesContainer');
                        container.innerHTML = '';
                        data.rules.forEach(rule => {
                            addRuleRow(rule.batch_limit, rule.measurement);
                        });
                        updateRulesPreview();
                    }
                })
                .catch(() => {
                    console.log('No existing rules found');
                });
        }

        function saveMeasurementFromModal(skipEmptyConfirm = false) {
            const container = document.getElementById('rulesContainer');
            const ruleRows = container.querySelectorAll('.rule-row');
            let rules = [];

            ruleRows.forEach(row => {
                const batchLimit = row.querySelector('.batch-limit-input').value;
                const measurement = row.querySelector('.measurement-input').value;
                if (batchLimit && measurement) {
                    rules.push({ batch_limit: parseInt(batchLimit), measurement: measurement });
                }
            });

            if (rules.length === 0 && !skipEmptyConfirm) {
                if (!confirm('No rules will be saved. The measurement input field will be shown again. Continue?')) {
                    return;
                }
            }

            fetch(`/products/${currentProductId}/ingredients/rules`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    ingredient_id: currentIngredientId,
                    rules: rules,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateMeasurementCell(currentIngredientId, rules);
                    closeMeasurementModal();
                }
            })
            .catch(() => {
                alert('Failed to save rules');
            });
        }

        function renderRulesBadges(rules) {
            return rules.map(rule => `
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                    ${rule.batch_limit} → ${rule.measurement}
                </span>
            `).join('');
        }

        function updateMeasurementCell(ingredientId, rules) {
            const cell = document.getElementById(`measurement-cell-${ingredientId}`);
            if (!cell) return;

            if (rules && rules.length > 0) {
                cell.innerHTML = `
                    <div class="flex flex-col items-center gap-1">
                        ${renderRulesBadges(rules)}
                    </div>
                `;
            } else {
                const pivotValue = cell.dataset.pivotMeasurement || '';
                cell.innerHTML = `
                    <input type="text"
                        class="w-20 px-2 py-1 text-sm text-center border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-gray-200 focus:border-[#5839a3] focus:ring-1 focus:ring-[#5839a3]"
                        value="${pivotValue}"
                        data-product-id="{{ $product->id }}"
                        data-ingredient-id="${ingredientId}"
                        onchange="saveMeasurement(this)"
                        placeholder="-">
                `;
            }
        }

        function saveMeasurement(input) {
            const productId = input.dataset.productId;
            const ingredientId = input.dataset.ingredientId;
            const measurement = input.value;

            fetch(`/products/${productId}/ingredients/measurement`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    ingredient_id: ingredientId,
                    measurement: measurement,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.style.borderColor = '#22c55e';
                    setTimeout(() => { input.style.borderColor = ''; }, 1000);
                }
            })
            .catch(() => {
                input.style.borderColor = '#ef4444';
                setTimeout(() => { input.style.borderColor = ''; }, 1000);
            });
        }
    </script>
</x-app-layout>
