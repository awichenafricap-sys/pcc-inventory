<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
});

require __DIR__.'/auth.php';
