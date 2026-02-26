<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Produce') }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('produce.update', $produce) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" value="{{ old('name', $produce->name) }}" class="block w-full" placeholder="Produce name" required>
                            @error('name')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <input type="text" name="category" value="{{ old('category', $produce->category) }}" class="block w-full" placeholder="Produce category" required>
                            @error('category')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" class="block w-full" placeholder="Optional description" rows="4">{{ old('description', $produce->description) }}</textarea>
                            @error('description')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12 border border-gray-200 rounded p-4 bg-gray-50">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Production Calculator (based on product recipes + item stock)</h3>
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                    <select id="calc-product" class="block w-full">
                                        <option value="">-- select product --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-12 sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Max possible now</label>
                                    <div id="calc-max" class="block w-full px-3 py-2 border border-gray-300 rounded bg-white font-semibold">-</div>
                                </div>
                                <div class="col-span-12 sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Planned quantity</label>
                                    <input id="calc-qty" type="number" min="1" value="1" class="block w-full">
                                </div>
                                <div class="col-span-12">
                                    <div id="calc-details" class="text-sm text-gray-600">Select a product to view ingredient stock limits.</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-full flex justify-end space-x-2">
                            <a href="{{ route('produce.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded">Cancel</a>
                            <button class="px-4 py-2 bg-primary-600 text-white rounded">Update</button>
                        </div>
                    </div>
                </form>

                @php
                    $calculatorProducts = $products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'recipe' => $product->items->map(function ($item) {
                                return [
                                    'name' => $item->name,
                                    'stock' => (int) $item->stock,
                                    'required' => (int) $item->pivot->quantity_required,
                                ];
                            })->values(),
                        ];
                    })->values();
                @endphp

                <script>
                    (function () {
                        const products = @json($calculatorProducts);

                        const select = document.getElementById('calc-product');
                        const maxEl = document.getElementById('calc-max');
                        const qtyEl = document.getElementById('calc-qty');
                        const detailsEl = document.getElementById('calc-details');

                        const byId = new Map(products.map((p) => [String(p.id), p]));

                        function render() {
                            const selected = byId.get(select.value);
                            if (!selected) {
                                maxEl.textContent = '-';
                                detailsEl.textContent = 'Select a product to view ingredient stock limits.';
                                return;
                            }

                            if (!selected.recipe.length) {
                                maxEl.textContent = 'No recipe';
                                detailsEl.textContent = 'This product has no ingredients configured yet.';
                                return;
                            }

                            const possibleList = selected.recipe.map((r) => Math.floor(r.stock / Math.max(1, r.required)));
                            const maxPossible = Math.min(...possibleList);
                            const plannedQty = Math.max(1, Number(qtyEl.value || 1));

                            maxEl.textContent = String(maxPossible);
                            const rows = selected.recipe
                                .map((r) => {
                                    const neededTotal = r.required * plannedQty;
                                    const after = r.stock - neededTotal;
                                    const status = after < 0 ? 'text-red-600' : 'text-green-700';
                                    return `<tr>
                                        <td class="px-3 py-2">${r.name}</td>
                                        <td class="px-3 py-2">${r.stock}</td>
                                        <td class="px-3 py-2">${neededTotal}</td>
                                        <td class="px-3 py-2 ${status}">${after}</td>
                                    </tr>`;
                                })
                                .join('');

                            detailsEl.innerHTML = `
                                <div class="mb-2 text-sm text-gray-700">Breakdown for ${plannedQty} unit(s)</div>
                                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Item</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Before</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Needed</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 uppercase">After</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">${rows}</tbody>
                                </table>
                            `;
                        }

                        select.addEventListener('change', render);
                        qtyEl.addEventListener('input', render);
                    })();
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
