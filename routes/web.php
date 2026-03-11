<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


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
    
    // Inventory resource routes
    Route::resource('items', App\Http\Controllers\ItemController::class)->only(['index','create','store','edit','update','destroy']);
    Route::resource('products', App\Http\Controllers\ProductController::class)->only(['index','create','store','edit','update','destroy']);
    Route::resource('produce', App\Http\Controllers\ProduceController::class)->only(['index','create','store','edit','update','destroy']);
    Route::resource('units', App\Http\Controllers\UnitController::class)->only(['index','create','store','edit','update','destroy']);
    Route::resource('restock', App\Http\Controllers\RestockController::class)->only(['index','create','store','edit','update','destroy']);

     // Export routes
    Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('/products/export/csv', [ProductController::class, 'exportCsv'])->name('products.export.csv');
    Route::get('/products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
    
    // Import routes
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/import/template', [ProductController::class, 'downloadTemplate'])->name('products.import.template');
});


require __DIR__.'/auth.php';
