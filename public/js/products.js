// Products Index Page Scripts

// Toggle form visibility
function toggleCreateForm() {
    const form = document.getElementById('createForm');
    const button = document.getElementById('toggleFormBtn');
    
    if (form.classList.contains('hidden')) {
        form.classList.remove('hidden');
        button.textContent = 'Cancel';
        button.classList.remove('bg-green-600', 'hover:bg-green-700');
        button.classList.add('bg-gray-600', 'hover:bg-gray-700');
    } else {
        form.classList.add('hidden');
        button.textContent = 'Add New Product';
        button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
    }
}

// Import/Export dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdown functionality
    const dropdownButtons = document.querySelectorAll('[x-data="{ open: false }"]');
    
    dropdownButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = button.getAttribute('x-open') === 'true';
            button.setAttribute('x-open', !isOpen);
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        dropdownButtons.forEach(button => {
            button.setAttribute('x-open', 'false');
        });
    });
});

// Search functionality
function searchProducts() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    
    if (searchInput && categoryFilter) {
        searchInput.addEventListener('input', function() {
            performSearch();
        });
        
        categoryFilter.addEventListener('change', function() {
            performSearch();
        });
    }
}

function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const categoryValue = document.getElementById('categoryFilter').value;
    
    // Build URL with parameters
    const url = new URL(window.location);
    if (searchValue) url.searchParams.set('search', searchValue);
    if (categoryValue) url.searchParams.set('category', categoryValue);
    
    // Redirect to filtered results
    window.location.href = url.toString();
}

// Form validation
function validateProductForm() {
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const code = document.getElementById('productCode').value;
            const name = document.getElementById('productName').value;
            const category = document.getElementById('productCategory').value;
            const unit = document.getElementById('productUnit').value;
            const stock = document.getElementById('currentStock').value;
            const reorderLevel = document.getElementById('reorderLevel').value;
            
            // Basic validation
            if (!code || !name || !category || !unit || stock === '' || reorderLevel === '') {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Stock validation
            if (parseInt(stock) < 0 || parseInt(reorderLevel) < 0) {
                e.preventDefault();
                alert('Stock and reorder level must be positive numbers.');
                return false;
            }
        });
    }
}

// Initialize all functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    searchProducts();
    validateProductForm();
    autoHideMessages();
    handleImportFile();
    initializeImagePreviews();
    initializeModalHandlers();
    initializeKeyboardHandlers();
    initializeSearchHandlers();
});

// Initialize image preview handlers
function initializeImagePreviews() {
    // Create form image preview
    const createImageInput = document.getElementById('create_image_input');
    if (createImageInput) {
        createImageInput.addEventListener('change', function(e) {
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
    }
    
    // Edit form image preview
    const editImageInput = document.getElementById('edit_image_input');
    if (editImageInput) {
        editImageInput.addEventListener('change', function(e) {
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
    }
}

// Initialize modal handlers
function initializeModalHandlers() {
    // Click outside to close edit modal
    window.onclick = function(event) {
        const editModal = document.getElementById('simpleEditModal');
        if (event.target == editModal) {
            hideSimpleModal();
        }
    };
    
    // Close preview modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('imagePreviewModal');
        if (event.target == modal) {
            hideImagePreview();
        }
    });
    
    // Close import modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('importModal');
        if (event.target == modal) {
            hideImportModal();
        }
    });
}

// Initialize keyboard handlers
function initializeKeyboardHandlers() {
    // Close with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideImagePreview();
            hideSimpleModal();
            hideImportModal();
        }
    });
}

// Initialize search handlers
function initializeSearchHandlers() {
    // Allow Enter key to submit search form
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchForm = document.getElementById('searchForm');
                if (searchForm) {
                    searchForm.submit();
                }
            }
        });
    }
}

// Auto-hide success and error messages
function autoHideMessages() {
    // Auto-hide success message after 3 seconds
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.transition = 'opacity 0.5s ease';
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 500);
        }, 3000);
    }
    
    // Auto-hide error message after 5 seconds
    const errorMessage = document.getElementById('errorMessage');
    if (errorMessage) {
        setTimeout(function() {
            errorMessage.style.transition = 'opacity 0.5s ease';
            errorMessage.style.opacity = '0';
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }
}

// Export functions
function exportProducts(format) {
    const searchValue = document.getElementById('searchInput')?.value || '';
    const categoryValue = document.getElementById('categoryFilter')?.value || '';
    
    let url = `/products/export/${format}`;
    const params = new URLSearchParams();
    
    if (searchValue) params.append('search', searchValue);
    if (categoryValue) params.append('category', categoryValue);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
}

// Import functionality
function handleImportFile() {
    const fileInput = document.getElementById('importFile');
    const form = document.getElementById('importForm');
    
    if (fileInput && form) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid Excel or CSV file.');
                    fileInput.value = '';
                    return;
                }
                
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB.');
                    fileInput.value = '';
                    return;
                }
            }
        });
        
        form.addEventListener('submit', function(e) {
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Please select a file to import.');
                return;
            }
        });
    }
}

// Initialize import functionality
document.addEventListener('DOMContentLoaded', function() {
    handleImportFile();
});

// Toggle create form visibility
function toggleCreateForm() {
    const formContainer = document.getElementById('createFormContainer');
    const button = document.getElementById('showCreateBtn');
    
    if (formContainer.style.display === 'none') {
        formContainer.style.display = 'block';
        button.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            Cancel
        `;
        button.classList.remove('bg-purple-600', 'hover:bg-purple-700');
        button.classList.add('bg-gray-600', 'hover:bg-gray-700');
    } else {
        formContainer.style.display = 'none';
        button.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Product
        `;
        button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
        button.classList.add('bg-purple-600', 'hover:bg-purple-700');
    }
}

// Clear search function
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        // Submit form to clear search
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.submit();
        }
    }
}

// Show edit modal
function showEditModal(id, code, name, category, unit, stock, reorder, imagePath, description, beginning, cost, credit, other) {
    // Set form action
    const form = document.getElementById('simpleEditForm');
    if (form) {
        form.action = '/products/' + id;
    }
    
    // Set form fields
    const codeField = document.getElementById('simple_code');
    const nameField = document.getElementById('simple_name');
    const categoryField = document.getElementById('simple_category');
    const unitField = document.getElementById('simple_unit');
    const stockField = document.getElementById('simple_stock');
    const reorderField = document.getElementById('simple_reorder');
    const beginningField = document.getElementById('simple_beginning');
    const costField = document.getElementById('simple_cost');
    const creditField = document.getElementById('simple_credit');
    const otherField = document.getElementById('simple_other');
    
    if (codeField) codeField.value = code;
    if (nameField) nameField.value = name;
    if (categoryField) categoryField.value = category;
    if (unitField) unitField.value = unit;
    if (stockField) stockField.value = stock;
    if (reorderField) reorderField.value = reorder;
    if (beginningField) beginningField.value = beginning || 0;
    if (costField) costField.value = cost || 0;
    if (creditField) creditField.value = credit || 0;
    if (otherField) otherField.value = other || '';
    
    // Set description if available
    const descField = document.getElementById('simple_description');
    if (descField) {
        descField.value = description || '';
    }
    
    // Set current image
    const currentImage = document.getElementById('current_image_preview');
    if (currentImage) {
        if (imagePath && imagePath !== '') {
            // Check if imagePath already contains 'storage/'
            if (imagePath.includes('storage/')) {
                currentImage.src = '/' + imagePath;
            } else {
                currentImage.src = '/storage/' + imagePath;
            }
        } else {
            currentImage.src = '/images/no-image.png';
        }
    }
    
    // Reset file input and preview
    const editImageInput = document.getElementById('edit_image_input');
    const editImagePreview = document.getElementById('edit_image_preview');
    if (editImageInput) editImageInput.value = '';
    if (editImagePreview) editImagePreview.style.display = 'none';
    
    // Show modal - prevent body scrolling
    const modal = document.getElementById('simpleEditModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

// Hide simple modal
function hideSimpleModal() {
    const modal = document.getElementById('simpleEditModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Confirm delete
function confirmDelete(productId) {
    // Check if SweetAlert is available, otherwise use native confirm
    if (typeof Swal !== 'undefined') {
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
                const deleteForm = document.getElementById(`delete-form-${productId}`);
                if (deleteForm) {
                    deleteForm.submit();
                }
            }
        });
    } else {
        // Fallback to native confirm
        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            const deleteForm = document.getElementById(`delete-form-${productId}`);
            if (deleteForm) {
                deleteForm.submit();
            }
        }
    }
}

// Show image preview
function showImagePreview(imageUrl, productName) {
    // Check if modal exists in DOM
    const modal = document.getElementById('imagePreviewModal');
    if (modal) {
        // Use existing modal
        const modalImage = document.getElementById('previewModalImage');
        const modalTitle = document.getElementById('previewModalTitle');
        const downloadBtn = document.getElementById('downloadImageBtn');
        
        if (modalImage) modalImage.src = imageUrl;
        if (modalTitle) modalTitle.textContent = productName + ' - Image';
        if (downloadBtn) downloadBtn.href = imageUrl;
        
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    } else {
        // Create modal dynamically
        const dynamicModal = document.createElement('div');
        dynamicModal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        `;
        
        dynamicModal.innerHTML = `
            <div style="position: relative; max-width: 90%; max-height: 90%;">
                <img src="${imageUrl}" alt="${productName}" style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">
                <button style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 24px; cursor: pointer;">×</button>
            </div>
        `;
        
        dynamicModal.onclick = function() {
            document.body.removeChild(dynamicModal);
        };
        
        document.body.appendChild(dynamicModal);
    }
}

// Hide image preview
function hideImagePreview() {
    const modal = document.getElementById('imagePreviewModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Show import modal
function showImportModal() {
    const modal = document.getElementById('importModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    } else {
        // Create import modal dynamically or show alert
        alert('Import functionality - this will open an import modal');
    }
}

// Hide import modal
function hideImportModal() {
    const modal = document.getElementById('importModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}
