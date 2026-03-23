<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Category Details') }}
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
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                </svg>
                                <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">{{ $category->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="space-y-6">
                <!-- Category Info Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                @if($category->color)
                                    <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                                @endif
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $category->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Slug: {{ $category->slug }}</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $category->ingredients->count() }} ingredient{{ $category->ingredients->count() != 1 ? 's' : '' }}
                            </span>
                        </div>

                        @if($category->description)
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Description</h4>
                                <p class="text-gray-600 dark:text-gray-400">{{ $category->description }}</p>
                            </div>
                        @endif

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Ingredients</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $category->ingredients->count() }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Total Stock Value</p>
                                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    ₱{{ number_format($category->ingredients->sum(function($ingredient) {
                                        return $ingredient->cost_per_unit * $ingredient->current_stock;
                                    }), 2) }}
                                </p>
                            </div>
                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg text-center border-2 border-yellow-200 dark:border-yellow-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5 text-yellow-500 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Low Stock Items</p>
                                </div>
                                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $category->ingredients->where('status', 'low_stock')->count() }}
                                </p>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900/30 rounded-lg text-center border-2 border-red-200 dark:border-red-700 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5 text-red-500 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-red-700 dark:text-red-300">Out of Stock</p>
                                </div>
                                <p class="text-3xl font-bold text-red-600 dark:text-red-400">
                                    {{ $category->ingredients->where('status', 'out_of_stock')->count() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients in this Category -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ingredients in this Category</h3>
                            @if($category->ingredients->count() > 0)
                                <button type="button" 
                                   onclick="openIngredientModal()"
                                   class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    View Summary ({{ $category->ingredients->count() }})
                                </button>
                                @endif
                        </div>
                            
                            @if($category->ingredients->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Stock</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($category->ingredients as $ingredient)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="px-4 py-3">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $ingredient->name }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $ingredient->sku }}</div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $ingredient->current_stock }} {{ $ingredient->unit_of_measurement }}</div>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            @if($ingredient->status === 'in_stock') bg-green-100 text-green-800
                                                            @elseif($ingredient->status === 'low_stock') bg-yellow-100 text-yellow-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ $ingredient->stock_status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex space-x-2">
                                                            <a href="{{ route('ingredients.show', $ingredient->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-sm inline-flex items-center" title="View">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('ingredients.edit', $ingredient->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 text-sm inline-flex items-center" title="Edit">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </a>
                                                            <form method="POST" action="{{ route('ingredients.destroy', $ingredient->id) }}" onsubmit="return confirm('Are you sure you want to delete this ingredient?')" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 text-sm inline-flex items-center" title="Delete">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('categories.partials.ingredient-summary-modal')
</x-app-layout>
