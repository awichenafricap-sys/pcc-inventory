<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Items') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <button onclick="document.getElementById('inline-add-form').classList.toggle('hidden');" type="button" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded">+ New Item</button>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div id="success-msg" class="mb-4 text-green-600">{{ session('success') }}</div>
                    @endif

                    <!-- Inline add form -->
                    <form id="inline-add-form" action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="mb-6 hidden">
                        @csrf
                        <div class="grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded">
                            <div class="col-span-1">
                                <label for="Plus" class="block text-sm font-black text-gray-700 mb-2">Image</label>
                                <label id="inline-image-picker" for="inline-image-input" title="Click to select an image" class="w-12 h-12 bg-gray-200 border border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer overflow-hidden">
                                    <img id="inline-image-placeholder" src="{{ asset('placeholder-icon.svg') }}" alt="Plus" class="w-6 h-6">
                                    <img id="inline-image-preview" src="" alt="Preview" class="hidden object-cover w-full h-full" />
                                </label>
                                <input id="inline-image-input" type="file" name="image" accept="image/*" class="sr-only">
                            </div>

                            <div class="col-span-12 sm:col-span-4">
                                <label class="block text-sm font-black text-gray-700 mb-2">Name</label>
                                <input name="name" type="text" placeholder="Item name" class="block w-full" required>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-sm font-black text-gray-700 mb-2">Unit</label>
                                <select name="unit_id" class="block w-full">
                                    <option value=""> none </option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-sm font-black text-gray-700 mb-2">Cost / Unit</label>
                                <input name="cost_per_unit" type="number" step="0.01" value="0" class="block w-full" placeholder="0">
                            </div>

                            <div class="col-span-6 sm:col-span-1">
                                <label class="block text-sm font-black text-gray-700 mb-2">Default</label>
                                <input name="default_quantity" type="number" min="1" value="1" class="block w-full text-center" placeholder="1" required>
                                <div class="flex justify-center mt-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_default" value="1" class="rounded">
                                        <span class="text-sm font-medium ms-2">Default</span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-sm font-black text-gray-700 mb-2">Stock</label>
                                <input name="stock" type="number" value="0" class="block w-full text-center" placeholder="0">
                            </div>

                            <div class="col-span-full flex justify-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded">Add</button>
                                <button type="button" onclick="document.getElementById('inline-add-form').classList.add('hidden'); document.getElementById('inline-add-form').reset();" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded">Cancel</button>
                            </div>
                        </div>
                    </form>

                    @if($items->count())
                        <table id="items-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost/Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $item)
                                    <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($item->image)
                                                    <img src="{{ asset('storage/' . $item->image) }}" alt="image" class="h-12 w-12 object-cover rounded">
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->unit?->name ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->cost_per_unit,2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->default_quantity ?? 0 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->stock }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('items.edit', $item) }}" class="text-primary-600 me-2">Edit</a>
                                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-600" onclick="return confirm('Delete item?')">Delete</button></form>
                                            </td>
                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $items->links() }}</div>
                    @else
                        <table id="items-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3"></th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost/Unit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                        <div id="no-items-msg" class="text-center text-gray-400 p-6">No items yet. Click <a href="javascript:void(0);" onclick="document.getElementById('inline-add-form').classList.toggle('hidden');" class="text-primary-600">New Item</a> to get started.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    (function(){
        const input = document.getElementById('inline-image-input');
        const preview = document.getElementById('inline-image-preview');
        const placeholder = document.getElementById('inline-image-placeholder');
        const form = document.getElementById('inline-add-form');
        const table = document.querySelector('#items-table tbody');
        const successMsg = document.getElementById('success-msg');

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) {
                preview.src = '';
                preview.classList.add('hidden');
                if (placeholder) placeholder.classList.remove('hidden');
                return;
            }
            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            preview.onload = () => URL.revokeObjectURL(url);
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: fd
                });

                if (res.status === 422) {
                    const data = await res.json();
                    alert(Object.values(data.errors).flat().join('\n'));
                    return;
                }

                if (!res.ok) throw new Error('Server error');

                const item = await res.json();

                // build new row
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">${item.image_url ? `<img src="${item.image_url}" alt="image" class="h-12 w-12 object-cover rounded">` : ''}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${item.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${item.unit ?? '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${item.cost_per_unit}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${item.default_quantity}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${item.stock}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="/items/${item.id}/edit" class="text-primary-600 me-2">Edit</a>
                        <form action="/items/${item.id}" method="POST" class="inline">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                            <button class="text-red-600" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                        </form>
                    </td>
                `;
                // prepend new row
                if (table) table.insertAdjacentElement('afterbegin', tr);

                // hide "no items" message
                const noItemsMsg = document.getElementById('no-items-msg');
                if (noItemsMsg) noItemsMsg.classList.add('hidden');

                // reset form
                form.reset();
                preview.src = '';
                preview.classList.add('hidden');
                if (placeholder) placeholder.classList.remove('hidden');
                form.classList.add('hidden');

                // show success
                if (successMsg) {
                    successMsg.textContent = 'Item successfully created.';
                    setTimeout(() => successMsg.textContent = '', 3000);
                }

            } catch (err) {
                alert('Could not save item. Please set a default quantity.');
                console.error(err);
            }
        });
    })();
</script>
