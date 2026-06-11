<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\TshirtImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TshirtImageController::class, 'showTshirts'])->name('home');
Route::get('/tshirt/{tshirt_image}', [TshirtImageController::class, 'showTshirt'])->name('show_tshirt');

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

    Route::middleware('can:customer')->group(function () {
        Route::post('cart', [CartController::class, 'confirm'])->name('cart.confirm');
    });

    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('orders/{order}/updateStatus', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});

Route::get('pdf/{order}', function(){
    $order = \App\Models\Order::findOrFail(request('order'));
    return view('orders.order-to-pdf', compact('order'));
})->name('orders.invoice');
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

require __DIR__ . '/settings.php';
