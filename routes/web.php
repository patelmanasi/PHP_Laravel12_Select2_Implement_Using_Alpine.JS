<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/


// List all products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Create product
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

// Show single product
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// Edit product
Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');

// Delete product
Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

// Trash
Route::get('/products-trash', [ProductController::class, 'trash'])->name('products.trash');

// Restore
Route::get('/products-restore/{id}', [ProductController::class, 'restore'])->name('products.restore');

// Force Delete
Route::delete('/products-force-delete/{id}', [ProductController::class, 'forceDelete'])->name('products.forceDelete');