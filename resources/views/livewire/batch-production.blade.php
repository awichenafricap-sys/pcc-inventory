<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Batch Production Scheduler</h1>
        <p class="text-gray-600 mt-1">Manage and track daily production batches</p>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <!-- Category Dropdown -->
            <div>
                <label for="selectedCategory" class="block text-sm font-medium text-gray-700 mb-2">
                    Category
                </label>
                <select 
                    id="selectedCategory"
                    wire:model.live="selectedCategory"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
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
                    <option value="">All Types</option>
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
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Products Production Schedule</h2>
            <p class="text-sm text-gray-500 mt-1">
                Date: {{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}
            </p>
        </div>

        <!-- Responsive Table Wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No.
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Product Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Batch/Day
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Batch
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $products->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $product['name'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">
                                    {{ $product['category'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $product['batch_per_day'] ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $product['total_batch'] ?? 0 }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
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
