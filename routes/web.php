<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredProducts = DB::table('Produkt as p')
        ->select('p.id', 'p.nazov')
        ->selectSub(function ($query) {
            $query->from('VariantProduktu as v')
                ->select('v.cena')
                ->whereColumn('v.produktId', 'p.id')
                ->where(function ($variantQuery) {
                    $variantQuery->whereNull('v.aktivny')->orWhere('v.aktivny', true);
                })
                ->orderBy('v.id')
                ->limit(1);
        }, 'cena')
        ->selectSub(function ($query) {
            $query->from('ProduktovyObrazok as po')
                ->select('po.url')
                ->whereColumn('po.produktId', 'p.id')
                ->orderByRaw('COALESCE(po.poradie, 9999)')
                ->orderBy('po.id')
                ->limit(1);
        }, 'image_url')
        ->where(function ($productQuery) {
            $productQuery->whereNull('p.aktivny')->orWhere('p.aktivny', true);
        })
        ->orderBy('p.id')
        ->limit(4)
        ->get();

    return view('pages.home', compact('featuredProducts'));
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
