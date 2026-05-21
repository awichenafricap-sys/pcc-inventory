<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ingredient Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li>
                            <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                Categories
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <a href="{{ route('categories.show', $ingredient->category_id) }}" class="ml-1 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white md:ml-2">
                                    {{ $ingredient->category->name ?? 'Category' }}
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">{{ $ingredient->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $ingredient->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">SKU: {{ $ingredient->sku }}</p>
                            </div>
                            @php $stockVal = $ingredient->released_used_items ?? 0; @endphp
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($stockVal <= 0) bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($stockVal <= $ingredient->minimum_stock) bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                @if($stockVal <= 0) Out of Stock
                                @elseif($stockVal <= $ingredient->minimum_stock) Low Stock
                                @else In Stock @endif
                            </span>
                        </div>

                        @if($ingredient->description)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Description</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $ingredient->description }}</p>
                            </div>
                        @endif

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ingredient->category->name ?? 'No Category' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Unit</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ingredient->unit_of_measurement }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Supplier</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ingredient->supplier ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Location</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ingredient->location ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock & Cost Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Stock Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Stock Information</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Stock</p>
                                    @php $stockVal = $ingredient->released_used_items ?? 0; @endphp
                                    <p class="text-3xl font-bold @if($stockVal <= 0) text-red-600 dark:text-red-400 @elseif($stockVal <= $ingredient->minimum_stock) text-yellow-600 dark:text-yellow-400 @else text-green-600 dark:text-green-400 @endif">
                                        {{ number_format($stockVal, 1) }} {{ $ingredient->unit_of_measurement }}
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Minimum Stock</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $ingredient->minimum_stock ?? 0 }} {{ $ingredient->unit_of_measurement }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Batches</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $ingredient->batches->whereIn('status', ['available', 'partial'])->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cost Information -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Cost Information</h3>
                            <div class="space-y-4">
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Cost per Unit</p>
                                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $ingredient->formatted_cost }}</p>
                                </div>
                                @if($ingredient->cost_per_unit && ($ingredient->released_used_items ?? 0) > 0)
                                    <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg text-center">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Value</p>
                                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                            &#8369;{{ number_format($ingredient->cost_per_unit * ($ingredient->released_used_items ?? 0), 2) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>