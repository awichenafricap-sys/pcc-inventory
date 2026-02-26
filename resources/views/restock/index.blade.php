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
                        <div class="bg-gray-50 p-3 rounded space-y-3">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Restock Date</label>
                                    <input name="restock_date" type="text" value="{{ date('Y-m-d') }}" class="date-picker block w-full" required>
                                </div>
                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Batch Code</label>
                                    <input id="batch-code" name="batch_code" type="text" class="block w-full bg-gray-100" readonly>
                                </div>
                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Batch Date</label>
                                    <input id="batch-date" name="batch_date" type="text" value="{{ date('Y-m-d') }}" class="date-picker block w-full" required>
                                </div>
                                <div class="col-span-12 sm:col-span-3">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Item Chooser</label>
                                    <div class="flex items-center gap-2">
                                        <select id="row-item-picker" class="block w-full">
                                            <option value="">-- choose item --</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="button" id="add-restock-row" class="px-3 py-2 text-xs bg-white border border-gray-300 rounded text-gray-700 whitespace-nowrap">+ Add</button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-semibold text-gray-700">Items in this restock batch</h3>
                            </div>

                            <div id="restock-rows" class="space-y-3"></div>

                            <div class="col-span-full flex justify-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded">Save All</button>
                                <button type="button" onclick="document.getElementById('inline-add-form').classList.add('hidden'); document.getElementById('inline-add-form').reset(); document.getElementById('restock-rows').innerHTML='';" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded">Cancel</button>
                            </div>
                        </div>
                    </form>

                    <template id="restock-row-template">
                        <div class="restock-row grid grid-cols-12 gap-2 border border-gray-200 bg-white rounded p-2">
                            <div class="col-span-12 sm:col-span-5">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Item</label>
                                <select name="restocks[__INDEX__][item_id]" class="block w-full" required>
                                    <option value="">-- select item --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Qty</label>
                                <input name="restocks[__INDEX__][quantity]" type="number" min="1" value="1" class="block w-full text-center" required>
                            </div>

                            <div class="col-span-12 sm:col-span-4">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                                <input name="restocks[__INDEX__][notes]" type="text" placeholder="Optional notes" class="block w-full">
                            </div>

                            <div class="col-span-6 sm:col-span-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Remove</label>
                                <button type="button" class="remove-restock-row w-full px-2 py-2 text-xs bg-red-50 text-red-600 border border-red-200 rounded">✕</button>
                            </div>
                        </div>
                    </template>

                    @if($restock->count())
                        <div class="overflow-x-auto">
                        <table id="restock-table" class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restock Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Qty</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($restock as $item)
                                    @php
                                        $batchKey = $item->batch_code . '|' . ($item->batch_date ? \Carbon\Carbon::parse($item->batch_date)->format('Y-m-d') : '');
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $item->batch_code }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $item->batch_date ? \Carbon\Carbon::parse($item->batch_date)->format('M d, Y') : '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ $item->restock_date ? \Carbon\Carbon::parse($item->restock_date)->format('M d, Y') : '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ (int) $item->entries_count }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">{{ (int) $item->total_quantity }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <button type="button" class="batch-info-btn px-2 py-1 text-xs border border-gray-300 rounded" data-batch-key="{{ $batchKey }}">Info</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                        <div class="mt-4">{{ $restock->links() }}</div>
                    @else
                        <div class="overflow-x-auto">
                        <table id="restock-table" class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restock Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Qty</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            </tbody>
                        </table>
                        </div>
                        <div id="no-restock-msg" class="text-center text-gray-400 p-6">No restock yet. Click <a href="javascript:void(0);" onclick="document.getElementById('inline-add-form').classList.toggle('hidden');" class="text-primary-600">New Restock</a> to get started.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div id="batch-info-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded shadow-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-800">Batch Info</h3>
            <button id="batch-info-close" type="button" class="text-gray-500">✕</button>
        </div>
        <div id="batch-info-content" class="text-sm text-gray-700 space-y-1 max-h-72 overflow-auto"></div>
    </div>
</div>

<script>
    (function(){
        const batchDetails = @json($batchDetails ?? []);
        const form = document.getElementById('inline-add-form');
        const table = document.querySelector('#restock-table tbody');
        const successMsg = document.getElementById('success-msg');
        const rowsContainer = document.getElementById('restock-rows');
        const rowTemplate = document.getElementById('restock-row-template');
        const addRowBtn = document.getElementById('add-restock-row');
        const rowItemPicker = document.getElementById('row-item-picker');
        const batchCodeInput = document.getElementById('batch-code');
        const batchDateInput = document.getElementById('batch-date');
        const batchInfoModal = document.getElementById('batch-info-modal');
        const batchInfoContent = document.getElementById('batch-info-content');
        const batchInfoClose = document.getElementById('batch-info-close');
        let rowIndex = 0;

        const openBatchInfo = (details) => {
            if (!details || !details.length) {
                batchInfoContent.innerHTML = '<div>No item details found for this batch.</div>';
            } else {
                batchInfoContent.innerHTML = details.map((row) => {
                    const notes = row.notes ? ` — ${row.notes}` : '';
                    return `<div><span class="font-medium">${row.item_name}</span>: ${row.quantity}${notes}</div>`;
                }).join('');
            }

            batchInfoModal.classList.remove('hidden');
            batchInfoModal.classList.add('flex');
        };

        const closeBatchInfo = () => {
            batchInfoModal.classList.add('hidden');
            batchInfoModal.classList.remove('flex');
        };

        batchInfoClose?.addEventListener('click', closeBatchInfo);
        batchInfoModal?.addEventListener('click', (e) => {
            if (e.target === batchInfoModal) closeBatchInfo();
        });

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.batch-info-btn');
            if (!btn) return;

            const key = btn.getAttribute('data-batch-key');
            openBatchInfo(batchDetails[key] || []);
        });

        const getSelectedRowItemIds = () => {
            return Array.from(rowsContainer.querySelectorAll('select[name*="[item_id]"]'))
                .map((el) => el.value)
                .filter((v) => v !== '');
        };

        const syncItemPickerOptions = () => {
            if (!rowItemPicker) return;

            const selectedIds = new Set(getSelectedRowItemIds());

            Array.from(rowItemPicker.options).forEach((opt) => {
                if (!opt.value) return;

                const isUsed = selectedIds.has(opt.value);
                opt.hidden = isUsed;
                opt.disabled = isUsed;
            });

            if (rowItemPicker.value && selectedIds.has(rowItemPicker.value)) {
                rowItemPicker.value = '';
            }
        };

        const generateBatchCode = () => {
            const raw = (batchDateInput?.value || '').trim();
            let date = raw ? new Date(raw) : new Date();
            if (Number.isNaN(date.getTime())) {
                date = new Date();
            }

            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            const stamp = `${yyyy}${mm}${dd}`;
            const random = String(Math.floor(Math.random() * 1000)).padStart(3, '0');

            if (batchCodeInput) {
                batchCodeInput.value = `BATCH-${stamp}-${random}`;
            }
        };

        const addRow = (selectedItemId = '') => {
            const html = rowTemplate.innerHTML.replaceAll('__INDEX__', rowIndex++);
            rowsContainer.insertAdjacentHTML('beforeend', html);

            const lastRow = rowsContainer.lastElementChild;
            const removeBtn = lastRow?.querySelector('.remove-restock-row');
            const select = lastRow?.querySelector('select[name*="[item_id]"]');

            if (select && selectedItemId) {
                select.value = selectedItemId;
            }

            select?.addEventListener('change', syncItemPickerOptions);

            removeBtn?.addEventListener('click', () => {
                lastRow.remove();
                syncItemPickerOptions();
            });

            if (window.initDatePickers) {
                window.initDatePickers();
            }

            syncItemPickerOptions();
        };

        addRowBtn.addEventListener('click', () => {
            const selectedItemId = rowItemPicker?.value || '';

            if (!selectedItemId) {
                alert('Please choose an item first.');
                return;
            }

            const selectedItems = Array.from(rowsContainer.querySelectorAll('select[name*="[item_id]"]'))
                .map((el) => el.value)
                .filter((v) => v !== '');

            if (selectedItems.includes(selectedItemId)) {
                alert('That item is already in this restock batch.');
                return;
            }

            addRow(selectedItemId);
            rowItemPicker.value = '';
            syncItemPickerOptions();
        });

        batchDateInput?.addEventListener('change', generateBatchCode);
        generateBatchCode();
        syncItemPickerOptions();

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!rowsContainer.querySelector('.restock-row')) {
                alert('Please add at least one restock row.');
                return;
            }

            const selectedItems = Array.from(rowsContainer.querySelectorAll('select[name*="[item_id]"]'))
                .map((el) => el.value)
                .filter((v) => v !== '');

            const uniqueItems = new Set(selectedItems);
            if (selectedItems.length !== uniqueItems.size) {
                alert('Duplicate item found in the same restock batch. Please keep each item only once.');
                return;
            }

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

                const payload = await res.json();
                const batch = payload.batch;

                if (batch) {
                    const batchKey = `${batch.batch_code}|${batch.batch_date_key || ''}`;
                    if (batch.details) {
                        batchDetails[batchKey] = batch.details;
                    }

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-4 py-3 whitespace-nowrap">${batch.batch_code || '-'}</td>
                        <td class="px-4 py-3 whitespace-nowrap">${batch.formatted_batch_date || '-'}</td>
                        <td class="px-4 py-3 whitespace-nowrap">${batch.formatted_restock_date || '-'}</td>
                        <td class="px-4 py-3 whitespace-nowrap">${batch.entries_count ?? 0}</td>
                        <td class="px-4 py-3 whitespace-nowrap">${batch.total_quantity ?? 0}</td>
                        <td class="px-4 py-3 whitespace-nowrap"><button type="button" class="batch-info-btn px-2 py-1 text-xs border border-gray-300 rounded" data-batch-key="${batchKey}">Info</button></td>
                    `;

                    if (table) table.insertAdjacentElement('afterbegin', tr);
                }

                // hide "no restock" message
                const noRestockMsg = document.getElementById('no-restock-msg');
                if (noRestockMsg) noRestockMsg.classList.add('hidden');

                // reset form
                form.reset();
                rowsContainer.innerHTML = '';
                generateBatchCode();
                form.classList.add('hidden');

                // show success
                if (successMsg) {
                    successMsg.textContent = 'Restock record(s) created.';
                    setTimeout(() => successMsg.textContent = '', 3000);
                }

            } catch (err) {
                console.error(err);
                alert('An error occurred while creating the restock record.');
            }
        });
    })();
</script>
