<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('New Product') }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" class="mt-1 block w-full">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Recipe (select ingredients and set quantity per 1 product)</h3>
                        <div class="border border-gray-200 rounded overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Use</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty Needed</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($items as $item)
                                        @php
                                            $oldQty = old('recipe.' . $item->id, 0);
                                            $selected = (int) $oldQty > 0;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 w-20">
                                                <input
                                                    type="checkbox"
                                                    name="recipe_selected[]"
                                                    value="{{ $item->id }}"
                                                    class="recipe-toggle rounded"
                                                    data-target="recipe-qty-{{ $item->id }}"
                                                    {{ $selected ? 'checked' : '' }}
                                                >
                                            </td>
                                            <td class="px-4 py-2">{{ $item->name }}</td>
                                            <td class="px-4 py-2">{{ $item->unit?->name ?? '-' }}</td>
                                            <td class="px-4 py-2 w-44">
                                                <input
                                                    type="number"
                                                    id="recipe-qty-{{ $item->id }}"
                                                    name="recipe[{{ $item->id }}]"
                                                    min="0"
                                                    value="{{ $oldQty }}"
                                                    class="block w-full"
                                                    {{ $selected ? '' : 'disabled' }}
                                                >
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-3 text-sm text-gray-500">No items available. Add items first before defining a recipe.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @error('recipe')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <script>
                        (function () {
                            const toggles = document.querySelectorAll('.recipe-toggle');

                            toggles.forEach((toggle) => {
                                const targetId = toggle.dataset.target;
                                const qtyInput = document.getElementById(targetId);
                                if (!qtyInput) return;

                                const sync = () => {
                                    qtyInput.disabled = !toggle.checked;
                                    if (!toggle.checked) {
                                        qtyInput.value = 0;
                                    } else if (!qtyInput.value || Number(qtyInput.value) <= 0) {
                                        qtyInput.value = 1;
                                    }
                                };

                                toggle.addEventListener('change', sync);
                                sync();
                            });
                        })();
                    </script>

                    <div class="flex justify-end">
                        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded me-2">Cancel</a>
                        <button class="px-4 py-2 bg-primary-600 text-white rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
