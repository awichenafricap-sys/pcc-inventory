<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Batch Production Scheduler</h1>
        <p class="text-gray-600 mt-1">Manage and track daily production batches</p>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search Input -->
            <div>
                <label for="searchTerm" class="block text-sm font-medium text-gray-700 mb-2">
                    Search Product
                </label>
                <input 
                    type="text" 
                    id="searchTerm"
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Search by product name..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
            </div>

            <!-- Type Dropdown -->
            <div>
                <label for="selectedType" class="block text-sm font-medium text-gray-700 mb-2">
                    Type
                </label>
                <select 
                    id="selectedType"
                    wire:model.live="selectedType"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                    <option value="Bottle">Bottle</option>
                    <option value="Sachet">Sachet</option>
                    <option value="Cup">Cup</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="flex items-center justify-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <span class="ml-2 text-gray-600">Loading...</span>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Products Production for {{ $selectedType }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Date: {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
                </p>
            </div>
            <button wire:click="toggleColumnManager" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Manage Columns
            </button>
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
            
            <!-- Add New Column Form -->
            <div class="flex gap-2 mb-4">
                <input type="text" wire:model="newColumnName" placeholder="Column name (e.g., 300ml)" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" wire:model="newColumnLabel" placeholder="Label (e.g., 300ml)" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button wire:click="addColumn" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                    Add Column
                </button>
            </div>
            
            <!-- Existing Columns -->
            <div class="space-y-2">
                @foreach($allColumns as $column)
                @if($editingColumnId == $column->id)
                <!-- Edit Mode -->
                <div class="flex items-center gap-2 bg-white p-3 rounded-lg border border-blue-300">
                    <input type="text" wire:model="editColumnName" placeholder="Column name" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="text" wire:model="editColumnLabel" placeholder="Label" class="flex-1 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button wire:click="updateColumn" class="px-3 py-1 bg-green-500 text-white rounded text-sm hover:bg-green-600">Save</button>
                    <button wire:click="cancelEditColumn" class="px-3 py-1 bg-gray-300 text-gray-700 rounded text-sm hover:bg-gray-400">Cancel</button>
                </div>
                @else
                <!-- View Mode -->
                <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">{{ $column->column_label }}</span>
                        <span class="text-xs text-gray-400">({{ $column->column_name }})</span>
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
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">200ml</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">500ml</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">1000ml</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <!-- Product Row (Highlighted) -->
                        <tr class="bg-blue-100 font-semibold">
                            <td class="px-6 py-3 text-sm text-gray-900">
                                {{ $product['name'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-900">
                                {{ $product['total_batch_flavors'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-900">
                                {{ $product['total_200ml'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-900">
                                {{ $product['total_500ml'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm text-gray-900">
                                {{ $product['total_1000ml'] }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('edit-manage', ['productId' => $product['id'], 'type' => $selectedType]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Edit
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
                                <td class="px-6 py-3 text-center">
                                    <input type="number" 
                                        wire:change="updateFlavorField({{ $flavor['id'] }}, 'batch', $event.target.value)"
                                        value="{{ $flavor['batch'] }}"
                                        min="0"
                                        class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <input type="number" 
                                        wire:change="updateFlavorField({{ $flavor['id'] }}, 'qty_200ml', $event.target.value)"
                                        value="{{ $flavor['qty_200ml'] }}"
                                        min="0"
                                        class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <input type="number" 
                                        wire:change="updateFlavorField({{ $flavor['id'] }}, 'qty_500ml', $event.target.value)"
                                        value="{{ $flavor['qty_500ml'] }}"
                                        min="0"
                                        class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <input type="number" 
                                        wire:change="updateFlavorField({{ $flavor['id'] }}, 'qty_1000ml', $event.target.value)"
                                        value="{{ $flavor['qty_1000ml'] }}"
                                        min="0"
                                        class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-3 text-center text-gray-400 text-xs">
                                    Flavor
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <!-- Plain - No Flavor Row -->
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-sm text-gray-700 pl-12">
                                <span class="text-gray-400 mr-1">-</span> Plain - No Flavor
                            </td>
                            <td class="px-6 py-3 text-center">
                                <input type="number"
                                    wire:change="updatePlainFlavor({{ $product['id'] }}, 'batch', $event.target.value)"
                                    value="0"
                                    min="0"
                                    class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-3 text-center">
                                <input type="number"
                                    wire:change="updatePlainFlavor({{ $product['id'] }}, 'qty_200ml', $event.target.value)"
                                    value="0"
                                    min="0"
                                    class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-3 text-center">
                                <input type="number"
                                    wire:change="updatePlainFlavor({{ $product['id'] }}, 'qty_500ml', $event.target.value)"
                                    value="0"
                                    min="0"
                                    class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-3 text-center">
                                <input type="number"
                                    wire:change="updatePlainFlavor({{ $product['id'] }}, 'qty_1000ml', $event.target.value)"
                                    value="0"
                                    min="0"
                                    class="w-14 px-2 py-1 text-center border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-3 text-center text-gray-400 text-xs">
                                Flavor
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
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

</div>
