<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Products') }}</h2>
        <!-- Add this in your <head> section or layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </x-slot>

    <!-- Include external JavaScript -->
    <script>
        const sizesData = @json($sizes);
        function updateFlavorSizes(prefix) {
            const typeEl = document.getElementById(prefix + '_type');
            const flavorEl = document.getElementById(prefix + '_flavors');
            const container = document.getElementById(prefix + '_flavor_sizes');
            const rowsEl = document.getElementById(prefix + '_flavor_sizes_rows');
            if (!typeEl || !flavorEl || !container || !rowsEl) return;

            const type = typeEl.value;
            const selectedFlavors = Array.from(flavorEl.selectedOptions).map(o => o.value);
            rowsEl.innerHTML = '';

            if (!type || !sizesData[type] || selectedFlavors.length === 0) {
                container.style.display = 'none';
                return;
            }

            container.style.display = 'block';
            const sizes = sizesData[type];

            selectedFlavors.forEach(function(flavorName) {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-3 flex-wrap';

                const label = document.createElement('span');
                label.className = 'text-sm font-medium text-gray-700 dark:text-gray-300 min-w-[100px]';
                label.textContent = flavorName;
                row.appendChild(label);

                sizes.forEach(function(size) {
                    const cb = document.createElement('input');
                    cb.type = 'checkbox';
                    cb.name = 'flavor_sizes[' + flavorName + '][]';
                    cb.value = size.column_name;
                    cb.id = prefix + '_size_' + flavorName + '_' + size.column_name;
                    cb.className = 'rounded border-gray-300 text-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-600 dark:border-gray-500';

                    const cbLabel = document.createElement('label');
                    cbLabel.htmlFor = cb.id;
                    cbLabel.className = 'text-xs text-gray-600 dark:text-gray-400 mr-2 cursor-pointer';
                    cbLabel.textContent = size.column_label;

                    const wrap = document.createElement('span');
                    wrap.className = 'inline-flex items-center gap-1';
                    wrap.appendChild(cb);
                    wrap.appendChild(cbLabel);
                    row.appendChild(wrap);
                });

                rowsEl.appendChild(row);
            });
        }
    </script>
    <script src="{{ asset('js/products.js') }}"></script>
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add Button na magsa-show ng form -->
           <!-- Add this after the New Product button and before the create form -->
<div class="flex justify-end mb-4 space-x-2">
    <!-- Import/Export Dropdown -->
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" 
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-150 ease-in-out">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Import/Export
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <!-- Dropdown menu -->
        <div x-show="open" @click.away="open = false" 
            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
            style="display: none;">
            <div class="py-1">
                <!-- Export Options -->
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                    Export
                </div>
                <a href="{{ route('products.export.excel', request()->query()) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-file-excel mr-2 text-green-600"></i> Export to Excel
                </a>
                <a href="{{ route('products.export.csv', request()->query()) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-file-csv mr-2 text-blue-600"></i> Export to CSV
                </a>
                <a href="{{ route('products.export.pdf', request()->query()) }}" 
                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-file-pdf mr-2 text-red-600"></i> Export to PDF
                </a>
                
                <!-- Import Options -->
                <div class="border-t border-gray-200 dark:border-gray-700 mt-1"></div>
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Import
                </div>
                
                <!-- Import Button that triggers modal -->
                <button onclick="showImportModal()" 
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-upload mr-2 text-purple-600"></i> Import Products
                </button>
                
                <a href="{{ route('products.import.template') }}" 
                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-download mr-2 text-gray-600"></i> Download Template
                </a>
            </div>
        </div>
    </div>
    
    <!-- Your existing New Product button -->
    <button onclick="toggleCreateForm()" id="showCreateBtn"
        class="inline-flex items-center px-4 py-2 text-white font-medium rounded-lg transition duration-150 ease-in-out"
        style="background-color: #5839a3; hover:background-color: #4a2f8c;">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        New Product
    </button>
</div>
            
            <!-- CREATE PRODUCT FORM - Hidden by default -->
            <div id="createFormContainer" style="display: none;" class="mb-4">
                <div
                    class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <!-- ✅ ERROR DISPLAY -->
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-md font-bold text-gray-800 dark:text-gray-200">Add New Product</h3>
                        <button onclick="toggleCreateForm()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                        {{-- CREATE --}}
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-2">
                        @csrf

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Code</label>
                                <input type="text" name="code" value="{{ old('code') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="P-001" required>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    placeholder="Product name" required>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <select name="category"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->name }}" {{ old('category', 'Flavor') == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Type</label>
                                <select name="type" id="create_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    onchange="updateFlavorSizes('create'); storeSelectedType(this.value)">
                                    <option value="">Select Type</option>
                                    <option value="Bottle" {{ old('type') == 'Bottle' ? 'selected' : '' }}>Bottle</option>
                                    <option value="Sachet" {{ old('type') == 'Sachet' ? 'selected' : '' }}>Sachet</option>
                                    <option value="Cup" {{ old('type') == 'Cup' ? 'selected' : '' }}>Cup</option>
                                    <option value="Yogurt" {{ old('type') == 'Yogurt' ? 'selected' : '' }}>Yogurt</option>
                                    <option value="Batch" {{ old('type') == 'Batch' ? 'selected' : '' }}>Batch</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Flavor</label>
                                <select name="flavors[]" id="create_flavors" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    size="5"
                                    onchange="updateFlavorSizes('create')">
                                    @foreach($flavors as $flavor)
                                        <option value="{{ $flavor->name }}" {{ in_array($flavor->name, old('flavors', [])) ? 'selected' : '' }}>{{ $flavor->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Unit</label>
                                <select name="unit"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                    required>
                                    <option value="">Select Unit</option>
                                    <option value="Pieces" {{ old('unit') == 'Pieces' ? 'selected' : '' }}>Pieces</option>
                                    <option value="Kilos" {{ old('unit') == 'Kilos' ? 'selected' : '' }}>Kilos</option>
                                    <option value="Grams" {{ old('unit') == 'Grams' ? 'selected' : '' }}>Grams</option>
                                    <option value="Liters" {{ old('unit') == 'Liters' ? 'selected' : '' }}>Liters</option>
                                    <option value="Packs" {{ old('unit') == 'Packs' ? 'selected' : '' }}>Packs</option>
                                    <option value="Cans" {{ old('unit') == 'Cans' ? 'selected' : '' }}>Cans</option>
                                    <option value="Bags" {{ old('unit') == 'Bags' ? 'selected' : '' }}>Bags</option>
                                    <option value="Boxes" {{ old('unit') == 'Boxes' ? 'selected' : '' }}>Boxes</option>
                                    <option value="Bottles" {{ old('unit') == 'Bottles' ? 'selected' : '' }}>Bottles</option>
                                    <option value="Cups" {{ old('unit') == 'Cups' ? 'selected' : '' }}>Cups</option>
                                    <option value="Sachets" {{ old('unit') == 'Sachets' ? 'selected' : '' }}>Sachets</option>
                                    <option value="Milliliters" {{ old('unit') == 'Milliliters' ? 'selected' : '' }}>Milliliters</option>
                                    <option value="Dozen" {{ old('unit') == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                                </select>
                            </div>

                        </div>

                        <!-- Per-Flavor Sizes Section -->
                        <div id="create_flavor_sizes" class="mt-3" style="display:none;">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Sizes per Flavor</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 space-y-2" id="create_flavor_sizes_rows">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Select type and flavors above to configure sizes per flavor</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Active</label>
                                <select name="is_active"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <input type="text" name="description" value="{{ old('description') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                placeholder="Optional description">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Ingredients</label>
                            <select name="ingredients[]" multiple
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-2"
                                size="5">
                                @foreach($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                        </div>

                        <!-- ✅ NEW: Image Upload Section -->
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Product
                                Image</label>
                            <div class="flex items-start space-x-4">
                                <div class="flex-1">
                                    <input type="file" name="image" id="create_image_input" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#5839a3] file:text-white hover:file:bg-[#4a2f8c] cursor-pointer">
                                    <p class="text-xs text-gray-500 mt-1">Accepted: JPEG, PNG, JPG, GIF (Max 2MB)</p>
                                </div>
                                <!-- Image Preview -->
                                <div class="flex-shrink-0">
                                    <img id="create_image_preview" src="#" alt="Preview"
                                        class="hidden w-16 h-16 object-cover rounded-lg border border-gray-300"
                                        onerror="this.style.display='none'">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 pt-3">
                            <button type="button" onclick="toggleCreateForm()"
                                class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-3 py-1.5 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out"
                                style="background-color: #28a745; hover:background-color: #218838;">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Success Message -->
            @if (session('success'))
                <div id="successMessage"
                    class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg flex items-center"
                    role="alert">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- After your existing success message section, add this -->
@if (session('error'))
    <div id="errorMessage"
        class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg flex items-center"
        role="alert">
        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif

            <!-- Products Section with Filters -->
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
    <div class="p-6 text-gray-900 dark:text-gray-100">
        
       <!-- FILTERS SECTION -->
<div class="mb-6 flex flex-col md:flex-row gap-4 items-center justify-between">
    <!-- Search Bar with Button -->
    <div class="w-full md:w-96">
        <form method="GET" action="{{ route('products.index') }}" id="searchForm" class="flex gap-2">
            <div class="relative flex-1">
                <input type="text" 
                       name="search" 
                       id="searchInput"
                       value="{{ request('search') }}"
                       placeholder="Search by code, name, category..." 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-[#5839a3] focus:ring-[#5839a3] text-sm">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <!-- Clear button (shows only if there's a search query) -->
                @if(request('search'))
                <button type="button" 
                        onclick="clearSearch()"
                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                @endif
            </div>
            
            <!-- Search Button -->
            <button type="submit" 
                    class="px-4 py-2 bg-[#5839a3] text-white rounded-lg hover:bg-[#4a2f8c] transition duration-150 ease-in-out flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Search</span>
            </button>
            
            <!-- Hidden inputs to preserve filters -->
            <input type="hidden" name="category" value="{{ request('category') }}">
        </form>
    </div>

    <!-- Category Filter Dropdown -->
    <div class="flex items-center gap-2">
        <label class="text-sm text-gray-600 dark:text-gray-400">Category:</label>
        <select onchange="window.location.href=this.value" 
                class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-[#5839a3] focus:ring-[#5839a3]">
            <option value="{{ route('products.index', array_merge(request()->except('category'), ['category' => ''])) }}" 
                    {{ !request('category') ? 'selected' : '' }}>
                All Categories
            </option>
            @foreach($categories as $category)
                <option value="{{ route('products.index', array_merge(request()->except('category'), ['category' => $category->name])) }}" 
                        {{ request('category') == $category->name ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Results count and Clear All button -->
    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
        <span>{{ $products->total() }} product(s) found</span>
        @if(request('search') || request('category'))
        <a href="{{ route('products.index') }}" 
           class="text-[#5839a3] hover:text-[#4a2f8c] font-medium">
            Clear All Filters
        </a>
        @endif
    </div>
</div>

        @if ($products->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Active</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ingredients</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                <!-- Image Column -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             alt="{{ $product->name }}"
                                             onclick="showImagePreview('{{ asset('storage/' . $product->image) }}', '{{ $product->name }}')"
                                             class="w-12 h-12 object-cover rounded-lg cursor-pointer border border-gray-200 hover:opacity-80 transition duration-150">
                                    @else
                                        <div onclick="showImagePreview('{{ asset('images/no-image.png') }}', '{{ $product->name }}')"
                                             class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 cursor-pointer border border-gray-200">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $product->code }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->name }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($product->category == 'Solid') bg-blue-100 text-blue-800
                                        @elseif($product->category == 'Liquid') bg-green-100 text-green-800
                                        @elseif($product->category == 'Yogurt') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $product->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->type ?? '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->unit }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    @if($product->is_active)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    <a href="{{ route('products.ingredients', $product->id) }}" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 hover:bg-purple-200 transition cursor-pointer">{{ $product->ingredients->count() }}</a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <!-- Edit button -->
                                    <button onclick="showEditModal(
                                        '{{ $product->id }}',
                                        '{{ $product->code }}',
                                        '{{ $product->name }}',
                                        '{{ $product->category }}',
                                        '{{ $product->flavors->pluck('flavor_name')->implode(',') }}',
                                        '{{ $product->type ?? '' }}',
                                        '{{ $product->unit }}',
                                        '{{ $product->is_active ? '1' : '0' }}',
                                        '{{ $product->image }}',
                                        '{{ addslashes($product->description) }}',
                                        '{{ $product->ingredients->pluck('id')->implode(',') }}',
                                        '{{ $product->flavors->mapWithKeys(fn($f) => [$f->flavor_name => $f->sizes->pluck('column_config.column_name')->filter()->values()])->toJson() }}'
                                    )" class="inline-flex items-center px-3 py-1.5 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out"
                                        style="background-color: #5839a3;">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </button>

                                    <!-- Delete button -->
                                    <button type="button" onclick="confirmDelete({{ $product->id }})" 
                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>

                                    <!-- Hidden delete form -->
                                    <form id="delete-form-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination with filters preserved -->
            <div class="mt-6">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No products found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if(request('search') || request('category'))
                        No products match your filters. 
                        <a href="{{ route('products.index') }}" class="text-[#5839a3] hover:underline">Clear filters</a>
                    @else
                        Get started by creating a new product.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
        </div>
    </div>

    <!-- EDIT MODAL (same as before) -->
    <!-- EDIT MODAL - With Scrollable Content -->
    <div id="simpleEditModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
        <div
            style="position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div
                style="background: white; padding: 25px; border-radius: 12px; width: 500px; max-width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); margin: auto;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; position: sticky; top: 0; background: white; z-index: 10;">
                    <h3 style="font-size: 20px; font-weight: bold; color: #111827;">Edit Product</h3>
                    <button onclick="hideSimpleModal()"
                        style="color: #9ca3af; background: none; border: none; cursor: pointer;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- ✅ IMPORTANT: enctype="multipart/form-data" para sa image upload -->
                <form id="simpleEditForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- ✅ CURRENT IMAGE DISPLAY -->
                    <div class="mb-4 text-center">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Current Image</label>
                        <img id="current_image_preview" src="#" alt="Current Image"
                            class="w-24 h-24 object-cover rounded-lg mx-auto border border-gray-300"
                            onerror="this.src='{{ asset('images/no-image.png') }}'">
                    </div>

                    <!-- ✅ NEW IMAGE UPLOAD -->
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Change Image (Optional)</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <input type="file" name="image" id="edit_image_input" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#5839a3] file:text-white hover:file:bg-[#4a2f8c] cursor-pointer">
                            </div>
                            <!-- ✅ NEW IMAGE PREVIEW -->
                            <div class="flex-shrink-0">
                                <img id="edit_image_preview" src="#" alt="New Preview"
                                    class="hidden w-16 h-16 object-cover rounded-lg border border-gray-300">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                    </div>

                    <!-- Product fields -->
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Product Code</label>
                        <input type="text" id="simple_code" name="code"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Product Name</label>
                        <input type="text" id="simple_name" name="name"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Category</label>
                        <select id="simple_category" name="category"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Type</label>
                        <select id="simple_type" name="type"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            onchange="updateFlavorSizes('simple')">
                            <option value="">Select Type</option>
                            <option value="Bottle">Bottle</option>
                            <option value="Sachet">Sachet</option>
                            <option value="Cup">Cup</option>
                            <option value="Yogurt">Yogurt</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Flavor</label>
                        <select id="simple_flavors" name="flavors[]" multiple
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            size="5"
                            onchange="updateFlavorSizes('simple')">
                            @foreach($flavors as $flavor)
                                <option value="{{ $flavor->name }}">{{ $flavor->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                    </div>

                    <!-- Per-Flavor Sizes Section -->
                    <div id="simple_flavor_sizes" style="margin-bottom: 15px; display:none;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Sizes per Flavor</label>
                        <div class="bg-gray-50 rounded-lg p-3 space-y-2" id="simple_flavor_sizes_rows">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Select type and flavors above to configure sizes per flavor</p>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Unit</label>
                        <select id="simple_unit" name="unit"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                            <option value="">Select Unit</option>
                            <option value="Pieces">Pieces</option>
                            <option value="Kilos">Kilos</option>
                            <option value="Grams">Grams</option>
                            <option value="Liters">Liters</option>
                            <option value="Packs">Packs</option>
                            <option value="Cans">Cans</option>
                            <option value="Bags">Bags</option>
                            <option value="Boxes">Boxes</option>
                            <option value="Bottles">Bottles</option>
                            <option value="Cups">Cups</option>
                            <option value="Sachets">Sachets</option>
                            <option value="Milliliters">Milliliters</option>
                            <option value="Dozen">Dozen</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Active</label>
                            <select id="simple_is_active" name="is_active"
                                style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Description</label>
                        <textarea name="description" id="simple_description" rows="3"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"
                            placeholder="Optional description"></textarea>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Ingredients</label>
                        <select id="simple_ingredients" name="ingredients[]" multiple
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            size="5">
                            @foreach($ingredients as $ingredient)
                                <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                            @endforeach
                        </select>
                        <p style="font-size: 12px; color: #6b7280; margin-top: 4px;">Hold Ctrl/Cmd to select multiple</p>
                    </div>

                    <div
                        style="text-align: right; border-top: 1px solid #e5e7eb; padding-top: 20px; position: sticky; bottom: 0; background: white; z-index: 10;">
                        <button type="button" onclick="hideSimpleModal()"
                            style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; margin-right: 8px; cursor: pointer;">Cancel</button>
                        <button type="submit"
                            style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer;">Update
                            Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal (Zoomed View) -->
    <div id="imagePreviewModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.8); z-index: 10000; backdrop-filter: blur(5px);">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 16px; max-width: 90%; max-height: 90%;">
            <!-- Close Button -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 id="previewModalTitle" style="font-size: 18px; font-weight: bold; color: #333;">Product Image</h3>
                <button onclick="hideImagePreview()"
                    style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
            </div>
            <!-- Image -->
            <img id="previewModalImage" src="" alt="Preview"
                style="max-width: 100%; max-height: 70vh; object-fit: contain; border-radius: 8px;">
            <!-- Download Button -->
            <div style="text-align: center; margin-top: 15px;">
                <a id="downloadImageBtn" href="#" download
                    style="display: inline-flex; align-items: center; padding: 8px 20px; background-color: #5839a3; color: white; text-decoration: none; border-radius: 8px; font-size: 14px;">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Image
                </a>
            </div>
        </div>
    </div>
    <!-- Import Modal -->
<!-- Import Modal -->
<div id="importModal" 
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 10001; overflow-y: auto;">
    <div style="position: relative; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; padding: 25px; border-radius: 12px; width: 450px; max-width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 20px; font-weight: bold; color: #111827;">Import Products</h3>
                <button onclick="hideImportModal()" style="color: #9ca3af; background: none; border: none; cursor: pointer;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Choose File (Excel/CSV)</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#5839a3] file:text-white hover:file:bg-[#4a2f8c] cursor-pointer">
                    <p class="text-xs text-gray-500 mt-2">Accepted formats: .xlsx, .xls, .csv (Max 5MB)</p>
                </div>

                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">Instructions:</h4>
                    <ul class="text-xs text-blue-700 list-disc list-inside space-y-1">
                        <li>First row should contain column headers</li>
                        <li>Required columns: <strong>name, category, unit, current_stock, reorder_level</strong></li>
                        <li>Optional columns: code, description</li>
                        <li>Download the template for the correct format</li>
                    </ul>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideImportModal()"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-[#28a745] hover:bg-[#218838] text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
                        Import Products
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>

<!-- Handle validation errors - show create form if there are errors -->
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('createFormContainer').style.display = 'block';
        document.getElementById('showCreateBtn').style.display = 'none';
    });
</script>
@endif

<!-- Product type storage script -->
<script>
function storeSelectedType(type) {
    if (type) {
        localStorage.setItem('selectedProductType', type);
    }
}

// Also store the initial value on page load if there's a selected type
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('create_type');
    if (typeSelect && typeSelect.value) {
        localStorage.setItem('selectedProductType', typeSelect.value);
    }
});
</script>
