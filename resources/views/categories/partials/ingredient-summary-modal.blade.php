<!-- Ingredient Summary Modal -->
<div id="ingredientModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeIngredientModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="modal-title">
                        {{ $category->name }} - Ingredients Summary
                    </h3>
                    <button type="button" onclick="closeIngredientModal()" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="bg-white dark:bg-gray-800 px-4 py-3 max-h-[70vh] overflow-y-auto">
                @foreach($category->ingredients as $ingredient)
                <div class="mb-2 p-3 bg-gray-50 dark:bg-gray-700 rounded border border-gray-200 dark:border-gray-600">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Name:</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100 ml-1">{{ $ingredient->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Category:</span>
                            <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $category->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Unit:</span>
                            <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $ingredient->unit_of_measurement }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Current Stock:</span>
                            <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $ingredient->current_stock }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Cost per Unit:</span>
                            <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $ingredient->formatted_cost }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Supplier:</span>
                            <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $ingredient->supplier ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Location:</span>
                            <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $ingredient->location ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Status:</span>
                            <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded 
                                @if($ingredient->stock_status_color === 'success') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($ingredient->stock_status_color === 'warning') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                {{ $ingredient->stock_status }}
                            </span>
                        </div>
                        @if($ingredient->description)
                        <div class="col-span-2 md:col-span-4">
                            <span class="text-gray-500 dark:text-gray-400">Description:</span>
                            <span class="text-gray-900 dark:text-gray-100 ml-1">{{ $ingredient->description }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end">
                <button type="button" onclick="closeIngredientModal()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openIngredientModal() {
        document.getElementById('ingredientModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeIngredientModal() {
        document.getElementById('ingredientModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeIngredientModal();
        }
    });
</script>
