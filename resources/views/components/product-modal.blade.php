{{-- resources/views/components/product-modal.blade.php --}}
{{-- (same as before, pero baguhin ang submitProductForm) --}}

@push('scripts')
<script>
    // Global modal functions
    window.openModal = function(modalId) {
        var modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    }
    
    window.closeModal = function(modalId) {
        var modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if(modal) {
            modal.hide();
        }
    }
    
    // I-override niyo ito sa inyong page kung gusto ng custom behavior
    window.submitProductForm = function() {
        console.warn('submitProductForm is not implemented in this page');
    }
    
    // Auto-open modal if there are errors
    @if($shouldBeOpen())
        document.addEventListener('DOMContentLoaded', function() {
            openModal('{{ $modalId }}');
        });
    @endif
</script>
@endpush