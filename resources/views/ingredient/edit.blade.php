<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Ingredient') }}
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
                                <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">Edit</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('ingredients.update', $ingredient->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Ingredient Name')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                                  value="{{ old('name', $ingredient->name) }}" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- SKU -->
                                <div>
                                    <x-input-label for="sku" :value="__('SKU')" />
                                    <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" 
                                                  value="{{ old('sku', $ingredient->sku) }}" required />
                                    <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                                </div>

                                <!-- Category -->
                                <div>
                                    <x-input-label for="category_id" :value="__('Category')" />
                                    <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" required>
                                        <option value="">{{ __('Select a category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $ingredient->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <!-- Unit of Measurement -->
                                <div>
                                    <x-input-label for="unit_of_measurement" :value="__('Unit of Measurement')" />
                                    <x-text-input id="unit_of_measurement" name="unit_of_measurement" type="text" 
                                                  class="mt-1 block w-full" value="{{ old('unit_of_measurement', $ingredient->unit_of_measurement) }}" required 
                                                  placeholder="e.g., kg, liters, pieces" />
                                    <x-input-error :messages="$errors->get('unit_of_measurement')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Stock Information -->
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Stock Information</h3>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="current_stock" :value="__('Current Stock')" />
                                            <x-text-input id="current_stock" name="current_stock" type="number" 
                                                          step="0.01" class="mt-1 block w-full" 
                                                          value="{{ old('current_stock', $ingredient->current_stock) }}" required />
                                            <x-input-error :messages="$errors->get('current_stock')" class="mt-2" />
                                        </div>

                                        <div>
                                            <x-input-label for="minimum_stock" :value="__('Minimum Stock')" />
                                            <x-text-input id="minimum_stock" name="minimum_stock" type="number" 
                                                          step="0.01" class="mt-1 block w-full" 
                                                          value="{{ old('minimum_stock', $ingredient->minimum_stock) }}" required />
                                            <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Cost Information -->
                                <div>
                                    <x-input-label for="cost_per_unit" :value="__('Cost per Unit')" />
                                    <x-text-input id="cost_per_unit" name="cost_per_unit" type="number" 
                                                  step="0.01" class="mt-1 block w-full" 
                                                  value="{{ old('cost_per_unit', $ingredient->cost_per_unit) }}" placeholder="0.00" />
                                    <x-input-error :messages="$errors->get('cost_per_unit')" class="mt-2" />
                                </div>

                                <!-- Supplier -->
                                <div>
                                    <x-input-label for="supplier" :value="__('Supplier')" />
                                    <x-text-input id="supplier" name="supplier" type="text" 
                                                  class="mt-1 block w-full" value="{{ old('supplier', $ingredient->supplier) }}" />
                                    <x-input-error :messages="$errors->get('supplier')" class="mt-2" />
                                </div>

                                <!-- Location -->
                                <div>
                                    <x-input-label for="location" :value="__('Storage Location')" />
                                    <x-text-input id="location" name="location" type="text" 
                                                  class="mt-1 block w-full" value="{{ old('location', $ingredient->location) }}" 
                                                  placeholder="e.g., Warehouse A, Shelf 1" />
                                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                                </div>

                                <!-- Expiry Date -->
                                <div>
                                    <x-input-label for="expiry_date" :value="__('Expiry Date')" />
                                    <x-text-input id="expiry_date" name="expiry_date" type="date" 
                                                  class="mt-1 block w-full" value="{{ old('expiry_date', $ingredient->expiry_date ? $ingredient->expiry_date->format('Y-m-d') : '') }}" />
                                    <x-input-error :messages="$errors->get('expiry_date')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-6">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" 
                                      class="mt-1 block w-full border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                                      placeholder="Optional description about this ingredient">{{ old('description', $ingredient->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('categories.show', $ingredient->category_id) }}" 
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                Update Ingredient
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>