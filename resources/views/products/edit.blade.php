<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            Edit Product
        </h2>
    </x-slot>

    <div class="py-10 flex justify-center">
        <div class="w-full max-w-xl">

            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-8">

                <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Product Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Product Code
                        </label>
                        <input type="text" name="code"
                            value="{{ old('code', $product->code) }}"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Product Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Product Name
                        </label>
                        <input type="text" name="name"
                            value="{{ old('name', $product->name) }}"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Category
                        </label>
                        <input type="text" name="category"
                            value="{{ old('category', $product->category) }}"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Unit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Unit
                        </label>
                        <input type="text" name="unit"
                            value="{{ old('unit', $product->unit) }}"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>

                    <!-- Stock Fields -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Current Stock
                            </label>
                            <input type="number" name="current_stock"
                                value="{{ old('current_stock', $product->current_stock) }}"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="0" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Reorder Level
                            </label>
                            <input type="number" name="reorder_level"
                                value="{{ old('reorder_level', $product->reorder_level) }}"
                                class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="0" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description
                        </label>
                        <textarea name="description" rows="3"
                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <a href="{{ route('products.index') }}"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>

                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">
                            Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>