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
    Route::resource('supplies', App\Http\Controllers\ItemController::class)->only(['index']);
    Route::resource('products', App\Http\Controllers\ProductController::class)->only(['index','create','store','edit','update','destroy']);

     // Export routes
    Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    
    // Import routes
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/import/template', [ProductController::class, 'downloadTemplate'])->name('products.import.template');
    Route::get('/products/{product}/ingredients', [ProductController::class, 'ingredientDetail'])->name('products.ingredients');
    Route::post('/products/{product}/ingredients/measurement', [ProductController::class, 'updateIngredientMeasurement'])->name('products.ingredients.measurement');
    Route::post('/products/{product}/ingredients/rules', [ProductController::class, 'saveBatchRules'])->name('products.ingredients.rules');
    Route::get('/products/{product}/ingredients/{ingredient}/rules', [ProductController::class, 'getBatchRules'])->name('products.ingredients.rules.get');
    Route::post('/product-flavors/{flavor}/measurement', [ProductController::class, 'updateFlavorMeasurement'])->name('product-flavors.measurement');
    Route::delete('/product-flavors/{flavor}', [ProductController::class, 'deleteFlavor'])->name('product-flavors.destroy');

    Route::resource('ingredients', IngredientController::class)->only(['show', 'store', 'edit', 'update', 'destroy']);
    Route::patch('/ingredients/{ingredient}/inventory', [IngredientController::class, 'updateInventory'])->name('ingredients.update-inventory');
    Route::resource('categories', CategoryController::class);
    Route::get('/batch', App\Livewire\BatchProduction::class)->name('batch.index');
    Route::get('/batch-production', App\Livewire\BatchProduction::class)->name('batch-production');
    Route::get('/supplies/ingredients', [App\Http\Controllers\SupplyController::class, 'ingredients'])->name('supplies.ingredients');
    Route::post('/supplies/ingredients', [App\Http\Controllers\SupplyController::class, 'updateIngredients'])->name('supplies.ingredients.update');
    Route::post('/supplies/ingredients/{ingredient}/field', [App\Http\Controllers\SupplyController::class, 'updateField'])->name('supplies.ingredients.field');
    Route::post('/supplies/ingredients/{ingredient}/daily-movement', [App\Http\Controllers\SupplyController::class, 'updateDailyMovement'])->name('supplies.ingredients.daily-movement');

    Route::get('/supplies/ingredients/{ingredient}', [App\Http\Controllers\SupplyController::class, 'ingredientDetail'])->name('supplies.ingredients.detail');

});



require __DIR__.'/auth.php';
