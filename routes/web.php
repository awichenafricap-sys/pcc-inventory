<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Inventory resource routes (moved to supplies)
    Route::resource('supplies', App\Http\Controllers\ItemController::class)->only(['index','store','update','destroy']);
    Route::get('/supplies/{ingredient}/next', [App\Http\Controllers\ItemController::class, 'next'])->name('supplies.next');
    Route::post('/supplies/{ingredient}/inventory-tracking', [App\Http\Controllers\ItemController::class, 'storeInventoryTracking'])->name('supplies.inventory-tracking.store');
    Route::resource('products', App\Http\Controllers\ProductController::class)->only(['index','create','store','edit','update','destroy']);

     // Export routes
    Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    
    // Import routes
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/import/template', [ProductController::class, 'downloadTemplate'])->name('products.import.template');

    Route::resource('ingredients', IngredientController::class)->only(['show', 'store', 'edit', 'update', 'destroy']);
    Route::patch('/ingredients/{ingredient}/inventory', [IngredientController::class, 'updateInventory'])->name('ingredients.update-inventory');
    Route::resource('categories', CategoryController::class);
    Route::get('/batch', App\Livewire\BatchProduction::class)->name('batch.index');
    Route::get('/supplies/ingredients', [App\Http\Controllers\SupplyController::class, 'ingredients'])->name('supplies.ingredients');

});



require __DIR__.'/auth.php';
