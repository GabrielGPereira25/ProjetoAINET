<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TshirtImageController;
use App\Http\Controllers\UserController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('/', [TshirtImageController::class, 'showTshirts'])->name('home');
Route::get('/tshirt/{tshirt_image}', [TshirtImageController::class, 'showTshirt'])->name('show_tshirt');

Route::middleware(['auth', 'verified', 'notBlocked'])->group(function () {
    Route::middleware('can:admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('colors', ColorController::class);
        Route::delete('categories/{category}/image', [CategoryController::class, 'destroyImage'])->name('categories.image.destroy');
        Route::get('prices', [PriceController::class, 'edit'])->name('prices.edit');
        Route::put('prices', [PriceController::class, 'update'])->name('prices.update');
        Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/block', [UserController::class, 'block_unblock'])->name('users.block_unblock');
        Route::delete('users/{user}/photo', [UserController::class, 'destroyPhoto'])->name('users.photo.destroy');
    });
    Route::resource('tshirt_images', TshirtImageController::class);
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::middleware('can:customer')->group(function () {
        Route::post('cart', [CartController::class, 'confirm'])->name('cart.confirm');
        Route::get('cart/verify-paypal/{token}', [CartController::class, 'verifyPayPal'])->name('cart.verify-paypal');
    });

    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('orders/{order}/updateStatus', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
// CART Related Routes
// Show the cart:
Route::get('cart', [CartController::class, 'show'])->name('cart.show');

// Add a t-shirt to the cart:
Route::post('cart/{tshirt_image}', [CartController::class, 'addToCart'])->name('cart.add');

// Remove a t-shirt from the cart:
Route::delete('cart/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

// Clear the cart:
Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');

Route::patch('cart/{id}/size', [CartController::class, 'updateSize'])->name('cart.updateSize');
Route::patch('cart/{id}/color', [CartController::class, 'updateColor'])->name('cart.updateColor');
Route::patch('cart/{id}/qty', [CartController::class, 'updateQty'])->name('cart.updateQty');

require __DIR__.'/settings.php';
