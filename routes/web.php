<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/products', [StorefrontController::class, 'products'])->name('products');
Route::get('/products/{categorySlug}', [StorefrontController::class, 'products'])
    ->whereIn('categorySlug', ['stickers', 'pins', 'patches', 'plushies'])
    ->name('products.category');
Route::get('/products/{productId}', [StorefrontController::class, 'showProduct'])
    ->whereNumber('productId')
    ->name('products.show');

Route::get('/cart', [StorefrontController::class, 'cartIndex'])->name('cart.index');
Route::post('/cart/add', [StorefrontController::class, 'cartAdd'])->name('cart.add');
Route::post('/cart/update', [StorefrontController::class, 'cartUpdate'])->name('cart.update');
Route::post('/cart/remove', [StorefrontController::class, 'cartRemove'])->name('cart.remove');
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [StorefrontController::class, 'checkoutStore'])->name('checkout.store');
Route::get('/checkout/success/{order}', [StorefrontController::class, 'checkoutSuccess'])
    ->whereNumber('order')
    ->name('checkout.success');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
