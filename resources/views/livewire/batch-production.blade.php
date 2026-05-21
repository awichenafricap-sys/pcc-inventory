<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Batch Production Scheduler</h1>
        <p class="text-gray-600 mt-1">Manage and track daily production batches</p>
    </div>

    <!-- Summary Cards (separate container above table) -->
    <div class="mb-6">
        <div class="flex justify-end items-center mb-4">
            <button wire:click="$set('showDailyMonthlyModal', true)" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 hover:bg-blue-100 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Daily: <span class="font-bold">{{ $dailyTotal }}</span> &middot; Monthly: <span class="font-bold">{{ $monthlyTotal }}</span>
            </button>
        </div>
        <!-- Type Summary Cards -->
        <div class="grid grid-cols-5 gap-4 mb-4">
            @foreach(['Bottle', 'Sachet', 'Cup', 'Yogurt', 'Batch'] as $type)
            @php
                $summary = $typeSummaries[$type] ?? null;
                $typeTotal = $summary['total'] ?? 0;
                $isActive = $selectedType === $type;
                $colors = [
                    'Bottle' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-600', 'icon' => '🧴'],
                    'Sachet' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-600', 'icon' => '📦'],
                    'Cup' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-600', 'icon' => '🥤'],
                    'Yogurt' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-600', 'icon' => '🥛'],
                    'Batch' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-600', 'icon' => '📋'],
                ];
                $c = $colors[$type];
            @endphp
            <div class="{{ $c['bg'] }} rounded-lg border {{ $isActive ? 'ring-2 ring-offset-1 ' . $c['border'] : $c['border'] }} p-4 cursor-pointer transition hover:shadow-md"
                 wire:click="$set('selectedType', '{{ $type }}')">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-semibold text-gray-700">{{ $c['icon'] }} {{ $type }}</h4>
                    @if($isActive)
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $c['bg'] }} {{ $c['text'] }} font-medium">Active</span>
                    @endif
                </div>
                <div class="text-2xl font-bold {{ $c['text'] }}">{{ $typeTotal }}</div>
                @if($summary && !empty($summary['columns']))
                <div class="mt-2 space-y-1">
                    @foreach($summary['columns'] as $col)
                    <div class="flex justify-between text-xs text-gray-600">
                        <span>{{ $col['label'] }}</span>
                        <span class="font-medium">{{ $summary['sizeBreakdown'][$col['id']] ?? 0 }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Products Production for {{ $selectedType }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-sm text-gray-500">
                        Date: {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
                    </p>
                    <button wire:click="toggleDatesDropdown" class="inline-flex items-center gap-1 text-xs px-2 py-1 bg-blue-50 text-blue-600 rounded-md hover:bg-blue-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Dates with Data
                    </button>
                </div>
                @if($showDatesDropdown)
                <div class="mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto divide-y divide-gray-100">
                    @forelse($datesWithData as $date)
                    @php $isActive = $selectedDate === $date; @endphp
                    <button wire:click="selectDate('{{ $date }}')" class="w-full text-left px-3 py-2.5 text-sm transition-all {{ $isActive ? 'bg-blue-600 text-white font-semibold' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700 hover:pl-4' }}">
                        {{ \Carbon\Carbon::parse($date)->format('M d, Y (D)') }}
                        @if($isActive)
                        <span class="ml-1 text-blue-200">&#10003;</span>
                        @endif
                    </button>
                    @empty
                    <div class="px-3 py-2 text-sm text-gray-400">No dates with data</div>
                    @endforelse
                </div>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchTerm"
                        placeholder="Search product..."
                        class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition w-48"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <select 
                    wire:model.live="selectedType"
                    wire:key="type-select-{{ $selectedType }}"
                    class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                    <option value="Bottle">Bottle</option>
                    <option value="Sachet">Sachet</option>
                    <option value="Cup">Cup</option>
                    <option value="Yogurt">Yogurt</option>
                    <option value="Batch">Batch</option>
                </select>
                <button wire:click="toggleColumnManager" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Manage Columns
                </button>
            </div>
            </div>
        </div>

        <!-- Column Manager Panel -->
        @if($showColumnManager)
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-medium text-gray-700">Manage Columns for {{ $selectedType }}</h3>
                <button wire:click="toggleColumnManager" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Existing Columns -->
            <div class="space-y-2">
                @foreach($allColumns as $column)
                @if($editingColumnId == $column->id)
                <!-- Edit Mode -->
                <div class="bg-white p-4 rounded-lg border-2 border-blue-400 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Column Name</label>
                            <input type="text" wire:model="editColumnName" placeholder="e.g. 500ml" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Volume (ml)</label>
                            <input type="number" wire:model.live="editSizeVolume" placeholder="e.g. 500" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Divisor</label>
                        <div class="flex items-center gap-2">
                            <select wire:model.live="editDivisorType" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="none">No Divisor</option>
                                @if($selectedType === 'Yogurt')
                                <option value="auto">Auto (1,000 ÷ volume) — Yogurt</option>
                                @else
                                <option value="auto">Auto (20,000 ÷ volume)</option>
                                @endif
                            </select>
                        </div>
                        @if($editDivisorType === 'auto')
                        @if($selectedType === 'Yogurt')
                        <div class="mt-2 bg-purple-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-purple-700">Auto Divisor (Yogurt): 1,000 ÷ {{ $editSizeVolume ?: '?' }} = <strong>{{ $editSizeVolume > 0 ? (int) floor(1000 / (int) $editSizeVolume) : '?' }}</strong></p>
                            <p class="text-[10px] text-purple-500 mt-1">This divisor is specific for Yogurt type only</p>
                        </div>
                        @else
                        <div class="mt-2 bg-purple-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-purple-700">Auto Divisor: 20,000 ÷ {{ $editSizeVolume ?: '?' }} = <strong>{{ $editSizeVolume > 0 ? (int) floor(20000 / (int) $editSizeVolume) : '?' }}</strong></p>
                        </div>
                        @endif
                        @endif
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button wire:click="cancelEditColumn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Cancel</button>
                        <button wire:click="updateColumn" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">Save</button>
                    </div>
                </div>
                @else
                <!-- View Mode -->
                <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">{{ $column->column_label }}</span>
                        <span class="text-xs text-gray-400">({{ $column->column_name }})</span>
                        @if($column->divisor_type && $column->divisor_type !== 'none')
                            @if($column->divisor_type === 'auto')
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">÷{{ $column->divisor_value }}</span>
                            @endif
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="toggleColumnActive({{ $column->id }})" class="text-xs px-2 py-1 rounded {{ $column->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $column->is_active ? 'Active' : 'Inactive' }}
                        </button>
                        <button wire:click="editColumn({{ $column->id }})" class="text-blue-500 hover:text-blue-700" title="Edit column">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <button wire:click="deleteColumn({{ $column->id }})" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this column?')" title="Delete column">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- Responsive Table Wrapper -->
        <div class="overflow-x-auto" wire:key="table-{{ $selectedDate }}-{{ $selectedType }}">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                        @foreach($allSizes as $sizeCol)
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center gap-1">
                                <span>{{ $sizeCol['label'] }}</span>
                                @if($sizeCol['divisor_type'] !== 'none')
                                    @if($sizeCol['divisor_type'] === 'auto')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700">÷{{ $sizeCol['divisor_value'] }}</span>
                                    @endif
                                @endif
                            </div>
                        </th>
                        @endforeach
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="openAddSizeModal" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-500 text-white hover:bg-blue-600 transition" title="Add new size">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        @php
                            $productBatchSum = 0;
                            foreach ($product['flavors'] as $fl) {
                                foreach ($allSizes as $sc) {
                                    $rawVal = (int) ($sizeQuantities[$fl['id'] . '_' . $sc['id']] ?? 0);
                                    $productBatchSum += $this->applyDivisor($sc['id'], $rawVal);
                                }
                            }
                            $yogurtPreDivide = $productBatchSum;
                            if ($selectedType === 'Yogurt') {
                                $productBatchSum = $productBatchSum / 20;
                            }
                            $liveBatch = (float) $productBatchSum;
                            $activeRule = (!empty($product['batch_rules']) && $liveBatch > 0)
                                ? $this->resolveActiveRule($liveBatch, $product['batch_rules'])
                                : null;
                            if ($activeRule) {
                                $productBatchSum = min($liveBatch, (float) $activeRule['batch_limit']);
                            }
                        @endphp
                        <!-- Product Row (Highlighted) -->
                        <tr class="bg-blue-100 font-semibold" wire:key="product-row-{{ $product['id'] }}-{{ $activeRule['batch_limit'] ?? 'none' }}">
                            <td class="px-6 py-3 text-sm text-gray-900">
                                {{ $product['name'] }}
                                @if($activeRule)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                        {{ $activeRule['batch_limit'] }} → {{ $activeRule['measurement'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-900">
                                @if($selectedType === 'Yogurt')
                                    {{ (int) floor($productBatchSum) ?: '-' }} <span class="text-xs text-purple-500 font-normal">({{ (int) floor($yogurtPreDivide) }})</span>
                                @else
                                    {{ (int) floor($productBatchSum) ?: '-' }}
                                @endif
                            </td>
                            @foreach($allSizes as $sizeCol)
                            @php $colId = $sizeCol['id']; @endphp
                            <td class="px-6 py-3 text-center text-sm text-gray-900">
                                @php
                                    $productSizeTotal = 0;
                                    foreach ($product['flavors'] as $fl) {
                                        $productSizeTotal += (int) ($sizeQuantities[$fl['id'] . '_' . $colId] ?? 0);
                                    }
                                @endphp
                                {{ $productSizeTotal ?: '-' }}
                            </td>
                            @endforeach
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('products.ingredients', ['product' => $product['id']]) }}" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 text-gray-500 hover:bg-blue-100 hover:text-blue-600 transition" title="Edit flavors & sizes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        
                        <!-- Flavor Sub-Rows -->
                        @if(!empty($product['flavors']))
                            @foreach($product['flavors'] as $flavor)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-3 text-sm text-gray-700 pl-12">
                                    <span class="text-gray-400 mr-1">-</span> {{ $flavor['flavor_name'] }}
                                </td>
                                <td class="px-6 py-3 text-center text-sm text-gray-700 font-medium">
                                    @php
                                        $flavorBatchSum = 0;
                                        foreach ($allSizes as $sc) {
                                            $rawVal = (int) ($sizeQuantities[$flavor['id'] . '_' . $sc['id']] ?? 0);
                                            $flavorBatchSum += $this->applyDivisor($sc['id'], $rawVal);
                                        }
                                        $flavorBatch = (int) floor($flavorBatchSum);
                                    @endphp
                                    {{ $flavorBatch ?: '-' }}
                                </td>
                                @foreach($allSizes as $sizeCol)
                                @php $colId = $sizeCol['id']; @endphp
                                <td class="px-6 py-3 text-center">
                                    <input type="number" 
                                        wire:model.live="sizeQuantities.{{ $flavor['id'] . '_' . $colId }}"
                                        min="0"
                                        class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                @endforeach
                                <td></td>
                            </tr>
                            @endforeach
                        @else
                        <!-- Plain - No Flavor Row (fallback, should not normally reach here) -->
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-sm text-gray-700 pl-12">
                                <span class="text-gray-400 mr-1">-</span> Plain
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="text-gray-400 text-sm">0</span>
                            </td>
                            @foreach($allSizes as $sizeCol)
                            <td class="px-6 py-3 text-center">
                                <span class="text-gray-400 text-sm">-</span>
                            </td>
                            @endforeach
                            <td></td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ 3 + count($allSizes) }}" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <span>No products found</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-500">
                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
            </div>
            <div class="flex gap-1">
                @if($products->onFirstPage())
                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">&laquo;</span>
                @else
                    <button wire:click="setPage({{ $products->currentPage() - 1 }})" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded transition">&laquo;</button>
                @endif

                @foreach(range(1, $products->lastPage()) as $page)
                    @if($page == $products->currentPage())
                        <span class="px-3 py-2 bg-blue-500 text-white rounded font-medium">{{ $page }}</span>
                    @else
                        <button wire:click="setPage({{ $page }})" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded transition">{{ $page }}</button>
                    @endif
                @endforeach

                @if($products->hasMorePages())
                    <button wire:click="setPage({{ $products->currentPage() + 1 }})" class="px-3 py-2 text-gray-600 hover:bg-gray-100 rounded transition">&raquo;</button>
                @else
                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">&raquo;</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Size Modal -->
    @if($showAddSizeModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="closeAddSizeModal"></div>
        <!-- Modal Content -->
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold text-gray-800">Add New Size</h3>
                    @php
                        $typeBadgeColors = [
                            'Bottle' => 'bg-blue-100 text-blue-700',
                            'Sachet' => 'bg-amber-100 text-amber-700',
                            'Cup' => 'bg-green-100 text-green-700',
                            'Yogurt' => 'bg-purple-100 text-purple-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeBadgeColors[$selectedType] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $selectedType }}
                    </span>
                </div>
                <button wire:click="closeAddSizeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Column Name</label>
                    <input type="text" wire:model="newSizeName" placeholder="e.g. 250ml, 500ml, 1000ml" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('newSizeName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Volume (ml)</label>
                    <input type="number" wire:model.live="newSizeVolume" placeholder="e.g. 250, 500, 1000" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('newSizeVolume') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Divisor</label>
                    <select wire:model.live="newSizeDivisorType" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="none">No Divisor</option>
                        @if($selectedType === 'Yogurt')
                        <option value="auto">Auto (1,000 ÷ volume) — Yogurt</option>
                        @else
                        <option value="auto">Auto (20,000 ÷ volume)</option>
                        @endif
                    </select>
                </div>
                @if($newSizeDivisorType === 'auto')
                @if($selectedType === 'Yogurt')
                <div class="bg-purple-50 rounded-lg p-3">
                    <p class="text-xs font-medium text-purple-700">Auto Divisor (Yogurt): 1,000 ÷ {{ $newSizeVolume ?: '?' }} = <strong>{{ $newSizeDivisor ?: '?' }}</strong></p>
                    <p class="text-[10px] text-purple-500 mt-1">This divisor is specific for Yogurt type only</p>
                </div>
                @else
                <div class="bg-purple-50 rounded-lg p-3">
                    <p class="text-xs font-medium text-purple-700">Auto Divisor: 20,000 ÷ {{ $newSizeVolume ?: '?' }} = <strong>{{ $newSizeDivisor ?: '?' }}</strong></p>
                </div>
                @endif
                @endif
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="closeAddSizeModal" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Cancel</button>
                <button wire:click="saveNewSize" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">Save Size</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Daily & Monthly Summary Modal -->
    @if($showDailyMonthlyModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('showDailyMonthlyModal', false)"></div>
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Production Summary</h3>
                <button wire:click="$set('showDailyMonthlyModal', false)" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <!-- Daily Summary -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Daily Total ({{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }})</h4>
                    <div class="text-2xl font-bold text-blue-600">{{ $dailyTotal }}</div>
                    @foreach(['Bottle', 'Sachet', 'Cup', 'Yogurt', 'Batch'] as $type)
                    @php
                        $ds = $typeSummaries[$type] ?? null;
                        $typeColors = ['Bottle' => 'text-blue-600', 'Sachet' => 'text-amber-600', 'Cup' => 'text-green-600', 'Yogurt' => 'text-purple-600', 'Batch' => 'text-red-600'];
                    @endphp
                    @if($ds && !empty($ds['columns']))
                    <div class="mt-3 pt-2 border-t border-blue-200">
                        <div class="text-xs font-semibold {{ $typeColors[$type] }} mb-1">{{ $type }}</div>
                        <div class="space-y-0.5">
                            @foreach($ds['columns'] as $col)
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>{{ $col['label'] }}</span>
                                <span class="font-medium">{{ $ds['sizeBreakdown'][$col['id']] ?? 0 }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
                <!-- Monthly Summary -->
                <div class="bg-green-50 rounded-lg border border-green-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Monthly Total ({{ \Carbon\Carbon::parse($selectedDate)->format('F Y') }})</h4>
                    <div class="text-2xl font-bold text-green-600">{{ $monthlyTotal }}</div>
                    @foreach(['Bottle', 'Sachet', 'Cup', 'Yogurt', 'Batch'] as $type)
                    @php
                        $ms = $monthlyTypeSummaries[$type] ?? null;
                        $typeColors = ['Bottle' => 'text-blue-600', 'Sachet' => 'text-amber-600', 'Cup' => 'text-green-600', 'Yogurt' => 'text-purple-600', 'Batch' => 'text-red-600'];
                    @endphp
                    @if($ms && !empty($ms['columns']))
                    <div class="mt-3 pt-2 border-t border-green-200">
                        <div class="text-xs font-semibold {{ $typeColors[$type] }} mb-1">{{ $type }}</div>
                        <div class="space-y-0.5">
                            @foreach($ms['columns'] as $col)
                            <div class="flex justify-between text-xs text-gray-600">
                                <span>{{ $col['label'] }}</span>
                                <span class="font-medium">{{ $ms['sizeBreakdown'][$col['id']] ?? 0 }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<!-- Product type sync script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Read stored product type from localStorage
    const storedType = localStorage.getItem('selectedProductType');
    console.log('Stored type from localStorage:', storedType);
    
    if (storedType) {
        // Wait for Livewire to be fully initialized
        setTimeout(() => {
            // Find any select element that contains the type options
            const typeSelect = Array.from(document.querySelectorAll('select')).find(select => {
                return Array.from(select.options).some(option => 
                    ['Bottle', 'Sachet', 'Cup', 'Yogurt', 'Batch'].includes(option.value)
                );
            });
            
            console.log('Type selector found:', typeSelect);
            
            if (typeSelect) {
                // Set the value
                typeSelect.value = storedType;
                console.log('Set type to:', storedType);
                
                // Trigger Livewire update
                const event = new Event('change', { bubbles: true });
                typeSelect.dispatchEvent(event);
            }
        }, 500);
    }
    
    // Listen for changes to any type selector and update localStorage
    setTimeout(() => {
        const typeSelect = Array.from(document.querySelectorAll('select')).find(select => {
            return Array.from(select.options).some(option => 
                ['Bottle', 'Sachet', 'Cup', 'Yogurt', 'Batch'].includes(option.value)
            );
        });
        
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                console.log('Type changed to:', this.value);
                localStorage.setItem('selectedProductType', this.value);
            });
        }
    }, 500);
});
</script>
