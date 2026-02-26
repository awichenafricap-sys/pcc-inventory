<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('New Item') }}</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                            <label id="image-picker" for="image-input" title="Click to select an image" class="w-12 h-12 sm:w-12 sm:h-12 bg-gray-200 dark:bg-gray-700 border border-dashed border-gray-300 dark:border-gray-600 rounded flex items-center justify-center cursor-pointer overflow-hidden relative">
                                <img id="image-placeholder" src="{{ asset('placeholder-icon.svg') }}" alt="Plus" class="w-6 h-6">
                                <img id="image-preview" src="" alt="Preview" class="hidden object-cover w-full h-full" />
                            </label>
                            <input id="image-input" type="file" name="image" accept="image/*" class="sr-only">
                        </div>

                        <div class="col-span-12 sm:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="block w-full" placeholder="Item name" required>
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                            <select name="unit_id" class="block w-full">
                                <option value="">-- none --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cost / unit</label>
                            <input type="number" step="0.01" name="cost_per_unit" value="{{ old('cost_per_unit', 0) }}" class="block w-full">
                        </div>

                        <div class="col-span-6 sm:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default</label>
                            <input type="number" name="default_quantity" min="1" required value="{{ old('default_quantity', 0) }}" class="block w-full text-center">
                        </div>

                        <div class="col-span-6 sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
                            <input type="number" name="stock" value="0" class="block w-full text-center">
                        </div>

                        <div class="col-span-full flex justify-end space-x-2">
                            <button class="px-4 py-2 bg-primary-600 text-white rounded">Add</button>
                            <a href="{{ route('items.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded">Cancel</a>
                        </div>
                    </div>
                </form>

                <script>
                    (function(){
                        const input = document.getElementById('image-input');
                        const picker = document.getElementById('image-picker');
                        const preview = document.getElementById('image-preview');
                        const placeholder = document.getElementById('image-placeholder');
                        const info = document.getElementById('image-info');

                        // label has `for` attribute so clicking the picker opens the file dialog

                        input.addEventListener('change', (e) => {
                            const file = input.files && input.files[0];
                            if (!file) {
                                preview.src = '';
                                preview.classList.add('hidden');
                                placeholder.classList.remove('hidden');
                                info.textContent = 'No file selected';
                                return;
                            }

                            // Show filename
                            info.textContent = file.name;

                            // Show preview
                            const url = URL.createObjectURL(file);
                            preview.src = url;
                            preview.classList.remove('hidden');
                            placeholder.classList.add('hidden');

                            // Revoke when image loads to free memory
                            preview.onload = () => URL.revokeObjectURL(url);
                        });
                    })();
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
