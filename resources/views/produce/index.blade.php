<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Produce') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <button onclick="document.getElementById('inline-add-form').classList.toggle('hidden');" type="button" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded">+ Produce Product</button>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif

                    <form id="inline-add-form" action="{{ route('produce.store') }}" method="POST" class="mb-6 hidden">
                        @csrf
                        <div class="grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded">
                            <div class="col-span-12 sm:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                <select id="produce-product" name="product_id" class="block w-full" required>
                                    <option value="">-- select product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input id="produce-quantity" type="number" name="quantity" min="1" value="1" class="block w-full" required>
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input type="text" name="produced_at" value="{{ date('Y-m-d') }}" class="date-picker block w-full" required>
                            </div>

                            <div class="col-span-12 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                                <input type="time" name="produced_time" value="{{ now()->format('H:i') }}" class="block w-full" required>
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <input type="text" name="notes" class="block w-full" placeholder="Optional notes">
                            </div>

                            <div class="col-span-12">
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-12 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Max possible now</label>
                                        <div id="produce-max" class="px-3 py-2 border border-gray-300 rounded bg-white font-semibold">-</div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-9">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ingredient Breakdown (Before → After)</label>
                                        <div id="produce-breakdown" class="text-sm text-gray-600">Select a product to view stock breakdown.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-full flex justify-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded">Produce</button>
                                <button type="button" onclick="document.getElementById('inline-add-form').classList.add('hidden'); document.getElementById('inline-add-form').reset();" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded">Cancel</button>
                            </div>
                        </div>
                    </form>

                    @if($errors->any())
                        <div class="mb-4 text-red-600 text-sm">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if($produce->count())
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($produce as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->product?->name ?? $item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->produced_at?->format('M d, Y') ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->produced_at_datetime?->format('h:i a') ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ Str::limit($item->notes ?? $item->description, 80) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('produce.destroy', $item) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-600" onclick="return confirm('Delete produce?')">Delete</button></form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">{{ $produce->links() }}</div>
                    @else
                        <div class="text-center text-gray-400">No production records yet. Click <a href="javascript:void(0);" onclick="document.getElementById('inline-add-form').classList.toggle('hidden');" class="text-primary-600">Produce Product</a> to get started.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $recipeProducts = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'recipe' => $product->items->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'stock' => (int) ($item->current_stock ?? 0),
                        'required' => (int) $item->pivot->quantity_required,
                    ];
                })->values(),
            ];
        })->values();
    @endphp

    <script>
        (function () {
            const products = @json($recipeProducts);
            const select = document.getElementById('produce-product');
            const qtyInput = document.getElementById('produce-quantity');
            const maxEl = document.getElementById('produce-max');
            const breakdownEl = document.getElementById('produce-breakdown');

            if (!select || !qtyInput || !maxEl || !breakdownEl) {
                return;
            }

            const byId = new Map(products.map((p) => [String(p.id), p]));
            let currentMaxPossible = null;

            function renderBreakdown() {
                const selected = byId.get(select.value);
                const qty = Math.max(1, Number(qtyInput.value || 1));

                if (!selected) {
                    maxEl.textContent = '-';
                    qtyInput.removeAttribute('max');
                    currentMaxPossible = null;
                    breakdownEl.textContent = 'Select a product to view stock breakdown.';
                    return;
                }

                if (!selected.recipe.length) {
                    maxEl.textContent = 'No recipe';
                    qtyInput.removeAttribute('max');
                    currentMaxPossible = null;
                    breakdownEl.textContent = 'This product has no recipe configured.';
                    return;
                }

                const maxPossible = Math.min(...selected.recipe.map((r) => Math.floor(r.stock / Math.max(1, r.required))));
                currentMaxPossible = maxPossible;
                maxEl.textContent = String(maxPossible);
                qtyInput.setAttribute('max', String(Math.max(0, maxPossible)));

                if (maxPossible <= 0) {
                    qtyInput.value = 1;
                    breakdownEl.textContent = 'Insufficient batch stock for this product recipe.';
                    return;
                }

                if (qty > maxPossible) {
                    qtyInput.value = maxPossible;
                }

                const rows = selected.recipe
                    .map((r) => {
                        const needed = r.required * qty;
                        const after = r.stock - needed;
                        const afterClass = after < 0 ? 'text-red-600' : 'text-green-700';

                        return `<tr>
                            <td class="px-3 py-2">${r.name}</td>
                            <td class="px-3 py-2">${r.stock}</td>
                            <td class="px-3 py-2">${needed}</td>
                            <td class="px-3 py-2 ${afterClass}">${after}</td>
                        </tr>`;
                    })
                    .join('');

                breakdownEl.innerHTML = `
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

            select.addEventListener('change', renderBreakdown);
            qtyInput.addEventListener('input', renderBreakdown);

            document.getElementById('inline-add-form')?.addEventListener('submit', (e) => {
                const qty = Math.max(1, Number(qtyInput.value || 1));

                if (currentMaxPossible === null) {
                    e.preventDefault();
                    alert('Please select a product first.');
                    return;
                }

                if (currentMaxPossible <= 0) {
                    e.preventDefault();
                    alert('Cannot produce this product. Batch stock is insufficient.');
                    return;
                }

                if (qty > currentMaxPossible) {
                    e.preventDefault();
                    qtyInput.value = currentMaxPossible;
                    alert(`Quantity exceeds max possible. Maximum is ${currentMaxPossible}.`);
                }
            });

            renderBreakdown();
        })();
    </script>
</x-app-layout>
