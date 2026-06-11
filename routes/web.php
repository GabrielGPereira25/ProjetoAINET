<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::middleware(['auth', 'verified', 'notBlocked'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::middleware('can:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('colors', ColorController::class);
        Route::delete('categories/{category}/image', [CategoryController::class, 'destroyImage'])->name('categories.image.destroy');
        Route::get('prices', [PriceController::class, 'edit'])->name('prices.edit');
        Route::put('prices', [PriceController::class, 'update'])->name('prices.update');
    });
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// CART Related Routes
// Show the cart:
Route::get('cart', [CartController::class, 'show'])->name('cart.show');

// Add a discipline to the cart:
Route::post('cart/{discipline}', [CartController::class, 'addToCart'])->name('cart.add');

// Remove a discipline from the cart:
Route::delete('cart/{discipline}', [CartController::class, 'removeFromCart'])->name('cart.remove');


// Confirm (store) the cart and save disciplines registration on the database:
Route::post('cart', [CartController::class, 'confirm'])->name('cart.confirm');

// Clear the cart:
Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');



require __DIR__ . '/settings.php';
