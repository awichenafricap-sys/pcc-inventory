<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Products') }}</h2>
        <!-- Add this in your <head> section or layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </x-slot>
    
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

                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-2">
                        @csrf

                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Code</label>
                                <input type="text" name="code" value="{{ old('code') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-1.5"
                                    placeholder="P-001" required>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-1.5"
                                    placeholder="Product name" required>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <input type="text" name="category" value="{{ old('category') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-1.5"
                                    placeholder="Dairy" required>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Unit</label>
                                <input type="text" name="unit" value="{{ old('unit') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-1.5"
                                    placeholder="Pieces" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Current
                                    Stock</label>
                                <input type="number" name="current_stock" value="{{ old('current_stock') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-1.5"
                                    min="0" required>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">Reorder
                                    Level</label>
                                <input type="number" name="reorder_level" value="{{ old('reorder_level') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-1.5"
                                    min="0" required>
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <input type="text" name="description" value="{{ old('description') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#5839a3] focus:ring-[#5839a3] dark:bg-gray-700 dark:border-gray-600 text-sm p-1.5"
                                    placeholder="Optional description">
                            </div>
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

                <script>
                    // Auto-hide success message after 3 seconds
                    setTimeout(function() {
                        const successMessage = document.getElementById('successMessage');
                        if (successMessage) {
                            successMessage.style.transition = 'opacity 0.5s ease';
                            successMessage.style.opacity = '0';
                            setTimeout(function() {
                                successMessage.style.display = 'none';
                            }, 500);
                        }
                    }, 3000); // 3000 milliseconds = 3 seconds
                </script>
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

    <script>
        setTimeout(function() {
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 500);
            }
        }, 5000);
    </script>
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

    <!-- Category Filter Buttons -->
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => ''])) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out {{ !request('category') ? 'bg-[#5839a3] text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
            All
        </a>
        <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => 'Solid'])) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out {{ request('category') == 'Solid' ? 'bg-[#5839a3] text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
            Solid
        </a>
        <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => 'Liquid'])) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out {{ request('category') == 'Liquid' ? 'bg-[#5839a3] text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
            Liquid
        </a>
        <a href="{{ route('products.index', array_merge(request()->except('category'), ['category' => 'Yogurt'])) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out {{ request('category') == 'Yogurt' ? 'bg-[#5839a3] text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
            Yogurt
        </a>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reorder</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $product->code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($product->category == 'Solid') bg-blue-100 text-blue-800
                                        @elseif($product->category == 'Liquid') bg-green-100 text-green-800
                                        @elseif($product->category == 'Yogurt') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $product->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->current_stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $product->reorder_level }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($product->current_stock == 0)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-800 text-white">
                                            Out of Stock
                                        </span>
                                    @elseif($product->current_stock <= $product->reorder_level)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">
                                            Low Stock
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            In Stock
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <!-- Edit button -->
                                    <button onclick="showEditModal(
                                        '{{ $product->id }}',
                                        '{{ $product->code }}',
                                        '{{ $product->name }}',
                                        '{{ $product->category }}',
                                        '{{ $product->unit }}',
                                        '{{ $product->current_stock }}',
                                        '{{ $product->reorder_level }}',
                                        '{{ $product->image }}',
                                        '{{ addslashes($product->description) }}'
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
                        <input type="text" id="simple_category" name="category"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                    </div>

                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Unit</label>
                        <input type="text" id="simple_unit" name="unit"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                            required>
                    </div>

                    <div style="margin-bottom: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Current Stock</label>
                            <input type="number" id="simple_stock" name="current_stock"
                                style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                                min="0" required>
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Reorder Level</label>
                            <input type="number" id="simple_reorder" name="reorder_level"
                                style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;"
                                min="0" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Description</label>
                        <textarea name="description" id="simple_description" rows="3"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; resize: vertical;"
                            placeholder="Optional description"></textarea>
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
    <script>
      // Toggle create form
function toggleCreateForm() {
    const form = document.getElementById('createFormContainer');
    const btn = document.getElementById('showCreateBtn');

    if (form.style.display === 'none') {
        form.style.display = 'block';
        btn.style.display = 'none';
    } else {
        form.style.display = 'none';
        btn.style.display = 'flex';
    }
}

// Image Preview for Create Form
document.getElementById('create_image_input')?.addEventListener('change', function(e) {
    const preview = document.getElementById('create_image_preview');
    const file = e.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Show edit modal with image
function showEditModal(id, code, name, category, unit, stock, reorder, imagePath, description) {
    // Set form action
    document.getElementById('simpleEditForm').action = '/products/' + id;
    
    // Set form fields
    document.getElementById('simple_code').value = code;
    document.getElementById('simple_name').value = name;
    document.getElementById('simple_category').value = category;
    document.getElementById('simple_unit').value = unit;
    document.getElementById('simple_stock').value = stock;
    document.getElementById('simple_reorder').value = reorder;
    
    // Set description if available
    const descField = document.getElementById('simple_description');
    if (descField) {
        descField.value = description || '';
    }
    
    // Set current image
    const currentImage = document.getElementById('current_image_preview');
    if (imagePath && imagePath !== '') {
        // Check if imagePath already contains 'storage/'
        if (imagePath.includes('storage/')) {
            currentImage.src = '{{ asset('') }}' + imagePath;
        } else {
            currentImage.src = '{{ asset('storage') }}/' + imagePath;
        }
    } else {
        currentImage.src = '{{ asset('images/no-image.png') }}';
    }
    
    // Reset file input and preview
    document.getElementById('edit_image_input').value = '';
    document.getElementById('edit_image_preview').style.display = 'none';
    
    // Show modal - prevent body scrolling
    document.getElementById('simpleEditModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Image Preview for Edit Form
document.getElementById('edit_image_input')?.addEventListener('change', function(e) {
    const preview = document.getElementById('edit_image_preview');
    const file = e.target.files[0];
    
    if (file) {
        // Validate file size (5MB limit)
        const fileSizeInMB = file.size / (1024 * 1024);
        if (fileSizeInMB > 5) {
            alert('File size exceeds 5MB. Please choose a smaller file.');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Hide edit modal
function hideSimpleModal() {
    document.getElementById('simpleEditModal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore body scrolling
}

// Click outside to close edit modal
window.onclick = function(event) {
    const modal = document.getElementById('simpleEditModal');
    if (event.target == modal) {
        hideSimpleModal();
    }
}

// Auto-hide create form if there are validation errors
@if ($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('createFormContainer').style.display = 'block';
        document.getElementById('showCreateBtn').style.display = 'none';
    });
@endif

// SweetAlert Delete Confirmation
function confirmDelete(productId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + productId).submit();
        }
    });
}

// Image Preview Modal Functions
function showImagePreview(imageUrl, productName) {
    document.getElementById('previewModalImage').src = imageUrl;
    document.getElementById('previewModalTitle').textContent = productName + ' - Image';
    document.getElementById('downloadImageBtn').href = imageUrl;
    document.getElementById('imagePreviewModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function hideImagePreview() {
    document.getElementById('imagePreviewModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close preview modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('imagePreviewModal');
    if (event.target == modal) {
        hideImagePreview();
    }
});

// Close with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideImagePreview();
        hideSimpleModal();
    }
});

// Clear search function
function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchForm').submit();
}

// Optional: Allow Enter key to submit form (mas ok pa rin ito)
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('searchForm').submit();
    }
});

// Import Modal Functions
function showImportModal() {
    document.getElementById('importModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function hideImportModal() {
    document.getElementById('importModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close import modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('importModal');
    if (event.target == modal) {
        hideImportModal();
    }
});
    </script>
</x-app-layout>
