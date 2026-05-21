<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Categories Management') }}
        </h2>
    </x-slot>

    <!-- Flash Messages -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    timer: 4000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li aria-current="page">
                            <span class="text-gray-500 dark:text-gray-400">Categories</span>
                        </li>
                    </ol>
                </nav>
            </div>
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <!-- Total Categories -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Categories</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $categories->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Ingredients -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Ingredients</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalIngredientsCount }}</p>
                        </div>
                    </div>
                </div>

                <!-- Low Stock -->
                <a href="{{ route('categories.index', ['filter' => 'low_stock']) }}" 
                   class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5 block hover:ring-2 hover:ring-yellow-400 transition-all {{ request('filter') == 'low_stock' ? 'ring-2 ring-yellow-400' : '' }}">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-lg p-3">
                            <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Low Stock</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $lowStockCount }}</p>
                        </div>
                    </div>
                </a>

            </div>

            <!-- ADD INGREDIENT FORM - Hidden by default -->
            <div id="ingredientFormContainer" style="display: none;" class="mb-4">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-md font-bold text-gray-800 dark:text-gray-200">Add New Ingredient</h3>
                        <button onclick="toggleIngredientForm()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('ingredients.store') }}">
                        @csrf
                        <!-- Basic Info -->
                        <div class="grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="Ingredient name" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">SKU <span class="text-red-500">*</span></label>
                                <input type="text" name="sku" value="{{ old('sku') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="SKU" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Category <span class="text-red-500">*</span></label>
                                <select name="category_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    required>
                                    <option value="">Select</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Unit <span class="text-red-500">*</span></label>
                                <select name="unit_of_measurement"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    required>
                                    <option value="">Select Unit</option>
                                    <option value="Pieces" {{ old('unit_of_measurement') == 'Pieces' ? 'selected' : '' }}>Pieces</option>
                                    <option value="Kilos" {{ old('unit_of_measurement') == 'Kilos' ? 'selected' : '' }}>Kilos</option>
                                    <option value="Grams" {{ old('unit_of_measurement') == 'Grams' ? 'selected' : '' }}>Grams</option>
                                    <option value="Liters" {{ old('unit_of_measurement') == 'Liters' ? 'selected' : '' }}>Liters</option>
                                    <option value="Packs" {{ old('unit_of_measurement') == 'Packs' ? 'selected' : '' }}>Packs</option>
                                    <option value="Cans" {{ old('unit_of_measurement') == 'Cans' ? 'selected' : '' }}>Cans</option>
                                    <option value="Bags" {{ old('unit_of_measurement') == 'Bags' ? 'selected' : '' }}>Bags</option>
                                    <option value="Boxes" {{ old('unit_of_measurement') == 'Boxes' ? 'selected' : '' }}>Boxes</option>
                                    <option value="Bottles" {{ old('unit_of_measurement') == 'Bottles' ? 'selected' : '' }}>Bottles</option>
                                    <option value="Cups" {{ old('unit_of_measurement') == 'Cups' ? 'selected' : '' }}>Cups</option>
                                    <option value="Sachets" {{ old('unit_of_measurement') == 'Sachets' ? 'selected' : '' }}>Sachets</option>
                                    <option value="Milliliters" {{ old('unit_of_measurement') == 'Milliliters' ? 'selected' : '' }}>Milliliters</option>
                                    <option value="Dozen" {{ old('unit_of_measurement') == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Inventory -->
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Minimum Stock</label>
                                <input type="number" name="minimum_stock" value="{{ old('minimum_stock', 0) }}"
                                    step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Cost per Unit</label>
                                <input type="number" name="cost_per_unit" value="{{ old('cost_per_unit', 0) }}"
                                    step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="0.00">
                            </div>
                        </div>
                        
                        <!-- Details -->
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                <input type="text" name="supplier" value="{{ old('supplier') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="Supplier name">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Location</label>
                                <input type="text" name="location" value="{{ old('location') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="Storage location">
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <input type="text" name="description" value="{{ old('description') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                placeholder="Optional description">
                        </div>
                        <div class="flex justify-end space-x-2 pt-3">
                            <button type="button" onclick="toggleIngredientForm()"
                                class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ingredients Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <!-- Search and Actions Row -->
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Ingredients List</h3>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                               onclick="toggleIngredientForm()"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Ingredient
                            </button>
                            @if($categories->count() > 0)
                            <button type="button" 
                               onclick="openCategorySummaryModal()"
                               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                View Categories ({{ $categories->count() }})
                            </button>
                            @else
                            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Category
                            </a>
                            @endif
                        </div>
                    </div>
                    <!-- Search Bar -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <form method="GET" action="{{ route('categories.index') }}" class="flex gap-2 w-full sm:w-auto flex-wrap items-center">
                            @if(request('filter') == 'low_stock')
                            <input type="hidden" name="filter" value="low_stock">
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full text-sm font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                Low Stock Filter
                                <a href="{{ route('categories.index') }}" class="ml-1 hover:text-yellow-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            </span>
                            @endif
                            <div class="relative flex-1 sm:w-80">
                                <input type="text" 
                                       name="search" 
                                       id="searchInput"
                                       value="{{ request('search') }}"
                                       placeholder="Search by Name or Unit " 
                                       class="w-full pl-10 pr-24 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <button type="submit" 
                                        class="absolute right-1 top-1 px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                                    Search
                                </button>
                            </div>
                            @if(request('search') || request('filter'))
                            <a href="{{ route('categories.index') }}" 
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-150 ease-in-out text-sm">
                                Clear All
                            </a>
                            @endif
                        </form>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    @if($ingredients->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($ingredients as $ingredient)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ingredient->name }}</div>
                                        @if($ingredient->description)
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($ingredient->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $catColor = $ingredient->category->color ?? null;
                                            if ($catColor) {
                                                $r = hexdec(substr($catColor, 1, 2));
                                                $g = hexdec(substr($catColor, 3, 2));
                                                $b = hexdec(substr($catColor, 5, 2));
                                                $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
                                                $textColor = $luminance > 0.5 ? '#1a1a1a' : '#ffffff';
                                            }
                                        @endphp
                                        @if($catColor)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $catColor }}; color: {{ $textColor }};">
                                            {{ $ingredient->category->name ?? 'No Category' }}
                                        </span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $ingredient->category->name ?? 'No Category' }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $stockVal = $ingredient->released_used_items ?? 0; @endphp
                                        @if($stockVal <= 0)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 animate-pulse">
                                            Out of Stock
                                        </span>
                                        @elseif($stockVal <= $ingredient->minimum_stock)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            Low Stock
                                        </span>
                                        @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            In Stock
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($ingredient->released_used_items ?? 0, 1) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Min: {{ $ingredient->minimum_stock }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->unit_of_measurement }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        No ingredients found.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Summary Modal -->
    <div id="category-summary-modal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" onclick="closeCategorySummaryModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            
            <div class="inline-block w-full max-w-5xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Categories Summary</h3>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('categories.create') }}" onclick="closeCategorySummaryModal()" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Category
                        </a>
                        <button onclick="closeCategorySummaryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($categories as $category)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border-l-4 {{ $category->color ? '' : 'border-blue-500' }}" {{ $category->color ? 'style="border-color: ' . $category->color . '"' : '' }}>
                            <div class="flex justify-between items-start">
                                <div class="flex items-center">
                                    @if($category->color)
                                        <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                                    @endif
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $category->name }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->slug }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ $category->ingredients_count }} item{{ $category->ingredients_count != 1 ? 's' : '' }}
                                </span>
                            </div>
                            @if($category->description)
                                <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 line-clamp-2">{{ Str::limit($category->description, 80) }}</p>
                            @endif
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 flex justify-end space-x-2">
                                <a href="{{ route('categories.show', $category->id) }}" onclick="closeCategorySummaryModal()" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 inline-flex items-center" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('categories.edit', $category->id) }}" onclick="closeCategorySummaryModal()" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 inline-flex items-center" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form id="delete-category-form-{{ $category->id }}" method="POST" action="{{ route('categories.destroy', $category->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDeleteCategory('{{ $category->id }}', '{{ $category->name }}')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 inline-flex items-center" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $categories->count() }}</span> categories total
                        </div>
                        <button onclick="closeCategorySummaryModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openCategorySummaryModal() {
            document.getElementById('category-summary-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeCategorySummaryModal() {
            document.getElementById('category-summary-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        function toggleIngredientForm() {
            const form = document.getElementById('ingredientFormContainer');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
        
        function clearSearch() {
            window.location.href = "{{ route('categories.index') }}";
        }
        
        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCategorySummaryModal();
                document.getElementById('ingredientFormContainer').style.display = 'none';
            }
        });

        function confirmDeleteCategory(id, name) {
            Swal.fire({
                title: 'Delete Category?',
                text: 'Are you sure you want to delete "' + name + '"? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-category-form-' + id).submit();
                }
            });
        }
    </script>
</x-app-layout>
