<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Restock') }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('restock.update', $restock) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                            <select name="item_id" class="block w-full" required>
                                <option value="">-- select item --</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id', $restock->item_id) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input type="number" name="quantity" min="1" value="{{ old('quantity', $restock->quantity) }}" class="block w-full" required>
                            @error('quantity')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="text" name="restock_date" value="{{ old('restock_date', $restock->restock_date->format('Y-m-d')) }}" class="date-picker block w-full" required>
                            @error('restock_date')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <input type="text" name="notes" value="{{ old('notes', $restock->notes) }}" placeholder="Optional notes" class="block w-full">
                        </div>

                        <div class="col-span-full flex justify-end space-x-2">
                            <a href="{{ route('restock.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded">Cancel</a>
                            <button class="px-4 py-2 bg-primary-600 text-white rounded">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
