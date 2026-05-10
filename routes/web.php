<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
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
    $orders = auth()->user()?->orders()
        ->with(['polozky.variant.product'])
        ->orderByDesc('created_at')
        ->orderByDesc('id')
        ->limit(5)
        ->get() ?? collect();

    return view('dashboard', compact('orders'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('/products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])->name('products.images.destroy');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
