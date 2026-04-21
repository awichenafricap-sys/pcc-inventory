<?php
// app/View/Components/ProductModal.php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductModal extends Component
{
    public $modalId;
    public $title;
    public $size;
    
    /**
     * Create a new component instance.
     */
    public function __construct($modalId = 'productModal', $title = 'Product Form', $size = 'md')
    {
        $this->modalId = $modalId;
        $this->title = $title;
        $this->size = $size;
    }
    
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.product-modal');
    }
    
    /**
     * Determine if the modal is open based on session or errors
     */
    public function shouldBeOpen()
    {
        return session()->has('errors') || old('_modal') == $this->modalId;
    }
}