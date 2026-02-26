<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Restock') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <button onclick="document.getElementById('inline-add-form').classList.toggle('hidden');" type="button" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded">+ New Restock</button>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div id="success-msg" class="mb-4 text-green-600">{{ session('success') }}</div>
                    @endif

                    <!-- Inline add form -->
                    <form id="inline-add-form" action="{{ route('restock.store') }}" method="POST" class="mb-6 hidden">
                        @csrf
                        <div class="grid grid-cols-12 gap-4 bg-gray-50 p-4 rounded">
                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                                <select name="item_id" class="block w-full" required>
                                    <option value="">-- select item --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-12 sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input name="quantity" type="number" min="1" value="1" class="block w-full text-center" required>
                            </div>

                            <div class="col-span-12 sm:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input name="restock_date" type="text" value="{{ date('Y-m-d') }}" class="date-picker block w-full" required>
                            </div>

                            <div class="col-span-12 sm:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <input name="notes" type="text" placeholder="Optional notes" class="block w-full">
                            </div>

                            <div class="col-span-full flex justify-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded">Add</button>
                                <button type="button" onclick="document.getElementById('inline-add-form').classList.add('hidden'); document.getElementById('inline-add-form').reset();" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded">Cancel</button>
                            </div>
                        </div>
                    </form>

                    @if($restock->count())
                        <table id="restock-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($restock as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->item->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->restock_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->notes ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('restock.edit', $item) }}" class="text-primary-600 me-2">Edit</a>
                                            <form action="{{ route('restock.destroy', $item) }}" method="POST" class="inline">@csrf @method('DELETE')<button class="text-red-600" onclick="return confirm('Delete restock record?')">Delete</button></form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $restock->links() }}</div>
                    @else
                        <table id="restock-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                        <div id="no-restock-msg" class="text-center text-gray-400 p-6">No restock yet. Click <a href="javascript:void(0);" onclick="document.getElementById('inline-add-form').classList.toggle('hidden');" class="text-primary-600">New Restock</a> to get started.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    (function(){
        const form = document.getElementById('inline-add-form');
        const table = document.querySelector('#restock-table tbody');
        const successMsg = document.getElementById('success-msg');

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

                const restock = await res.json();

                // build new row
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">${restock.item_name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${restock.quantity}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${restock.formatted_date}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${restock.notes || '-'}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="/restock/${restock.id}/edit" class="text-primary-600 me-2">Edit</a>
                        <form action="/restock/${restock.id}" method="POST" class="inline">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                            <button class="text-red-600" onclick="return confirm('Delete restock record?')">Delete</button>
                        </form>
                    </td>
                `;
                // prepend new row
                if (table) table.insertAdjacentElement('afterbegin', tr);

                // hide "no restock" message
                const noRestockMsg = document.getElementById('no-restock-msg');
                if (noRestockMsg) noRestockMsg.classList.add('hidden');

                // reset form
                form.reset();
                form.classList.add('hidden');

                // show success
                if (successMsg) {
                    successMsg.textContent = 'Restock record created.';
                    setTimeout(() => successMsg.textContent = '', 3000);
                }

            } catch (err) {
                console.error(err);
                alert('An error occurred while creating the restock record.');
            }
        });
    })();
</script>
