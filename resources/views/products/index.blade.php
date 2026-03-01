<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Products') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add Button na magsa-show ng form -->
            <div class="flex justify-end mb-4">
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
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-md font-bold text-gray-800 dark:text-gray-200">Add New Product</h3>
                        <button onclick="toggleCreateForm()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('products.store') }}" method="POST" class="space-y-2">
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

                        <div class="flex justify-end space-x-2 pt-1">
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
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Products Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($products->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Code</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Category</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Unit</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Stock</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Reorder</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    @foreach ($products as $product)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $product->code }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                {{ $product->name }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                {{ $product->category }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                {{ $product->unit }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                {{ $product->current_stock }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                                {{ $product->reorder_level }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($product->current_stock == 0)
                                                    <span
                                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-800 text-white">
                                                        Out of Stock
                                                    </span>
                                                @elseif($product->current_stock <= $product->reorder_level)
                                                    <span
                                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">
                                                        Low Stock
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        In Stock
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button
                                                    onclick="showEditModal(
    '{{ $product->id }}',
    '{{ $product->code }}',
    '{{ $product->name }}',
    '{{ $product->category }}',
    '{{ $product->unit }}',
    '{{ $product->current_stock }}',
    '{{ $product->reorder_level }}'
)"
                                                    class="inline-flex items-center px-3 py-1.5 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out"
                                                    style="background-color: #5839a3;"
                                                    onmouseover="this.style.backgroundColor='#4a2f8c'"
                                                    onmouseout="this.style.backgroundColor='#5839a3'">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                    Edit
                                                </button>

                                               <button type="button" onclick="confirmDelete({{ $product->id }})" 
        class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition duration-150 ease-in-out">
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
    </svg>
    Delete
</button>

<!-- Hidden delete form para sa bawat product -->
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

                        <div class="mt-6">
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No products</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new
                                product.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL (same as before) -->
    <div id="simpleEditModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999;">
        <!-- ... edit modal content same as before ... -->
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 25px; border-radius: 12px; width: 450px; max-width: 95%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px;">
                <h3 style="font-size: 20px; font-weight: bold; color: #111827;">Edit Product</h3>
                <button onclick="hideSimpleModal()" style="color: #9ca3af;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="simpleEditForm" method="POST">
                @csrf @method('PUT')

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

                <!-- Edit Modal Update Button -->
                <div style="text-align: right; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    <button type="button" onclick="hideSimpleModal()"
                        style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; margin-right: 8px; cursor: pointer;">Cancel</button>
                    <button type="submit"
                        style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; hover:background: #218838;">Update
                        Product</button>
                </div>
            </form>
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

        // Show edit modal
        function showEditModal(id, code, name, category, unit, stock, reorder) {
            document.getElementById('simpleEditForm').action = '/products/' + id;
            document.getElementById('simple_code').value = code;
            document.getElementById('simple_name').value = name;
            document.getElementById('simple_category').value = category;
            document.getElementById('simple_unit').value = unit;
            document.getElementById('simple_stock').value = stock;
            document.getElementById('simple_reorder').value = reorder;
            document.getElementById('simpleEditModal').style.display = 'block';
        }

        // Hide edit modal
        function hideSimpleModal() {
            document.getElementById('simpleEditModal').style.display = 'none';
        }

        // Click outside to close edit modal
        window.onclick = function(event) {
            const modal = document.getElementById('simpleEditModal');
            if (event.target == modal) {
                modal.style.display = 'none';
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
        confirmButtonColor: '#dc2626', // red-600
        cancelButtonColor: '#6b7280', // gray-500
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        showClass: {
            popup: 'animate__animated animate__fadeInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the delete form
            document.getElementById('delete-form-' + productId).submit();
            
            // Show success message (optional - kung gusto mo ng loading state)
            Swal.fire({
                title: 'Deleted!',
                text: 'Product has been deleted.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}
    </script>
</x-app-layout>
