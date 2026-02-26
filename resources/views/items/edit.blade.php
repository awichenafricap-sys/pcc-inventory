<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Item') }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                            <label id="edit-image-picker" for="edit-image-input" class="w-12 h-12 bg-gray-200 border border-dashed border-gray-300 rounded flex items-center justify-center cursor-pointer overflow-hidden relative">
                                @if($item->image)
                                    <img id="edit-image-preview" src="{{ asset('storage/' . $item->image) }}" alt="item" class="object-cover w-full h-full">
                                @else
                                    <img id="edit-image-placeholder" src="{{ asset('placeholder-icon.svg') }}" alt="Plus" class="w-6 h-6">
                                    <img id="edit-image-preview" src="" alt="Preview" class="hidden object-cover w-full h-full">
                                @endif
                            </label>
                            <input id="edit-image-input" type="file" name="image" accept="image/*" class="sr-only">
                        </div>

                        <div class="col-span-12 sm:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" value="{{ old('name', $item->name) }}" class="block w-full" required>
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                            <select name="unit_id" class="block w-full">
                                <option value="">-- none --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ (old('unit_id', $item->unit_id) == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cost / unit</label>
                            <input type="number" step="0.01" name="cost_per_unit" value="{{ old('cost_per_unit', $item->cost_per_unit) }}" class="block w-full">
                        </div>

                        <div class="col-span-6 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default</label>
                            <input type="number" min="1" required name="default_quantity" value="{{ old('default_quantity', $item->default_quantity) }}" class="block w-full text-center">
                            <div class="flex justify-center mt-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $item->is_default) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium ms-2">Default</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', $item->stock) }}" class="block w-full text-center">
                        </div>

                        <div class="col-span-full flex justify-end space-x-2">
                            <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded">Cancel</a>
                            <button class="px-4 py-2 bg-primary-600 text-white rounded">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
