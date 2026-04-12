<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
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

Route::get('/products', function () {
    $products = DB::table('Produkt as p')
        ->leftJoin('Kategoria as k', 'k.id', '=', 'p.kategoriaId')
        ->select('p.id', 'p.nazov', 'k.nazov as kategoria_nazov')
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
            $query->from('VariantProduktu as v')
                ->selectRaw('COALESCE(SUM(v.skladom), 0)')
                ->whereColumn('v.produktId', 'p.id')
                ->where(function ($variantQuery) {
                    $variantQuery->whereNull('v.aktivny')->orWhere('v.aktivny', true);
                });
        }, 'stock_total')
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
        ->get()
        ->map(function ($product, $index) {
            $product->image_url = ltrim((string) $product->image_url, '/');
            $product->stock_status = ((int) $product->stock_total) > 0 ? 'in-stock' : 'out-of-stock';
            $product->sort_order = $index + 1;

            return $product;
        });

    return view('pages.products', compact('products'));
})->name('products');

Route::get('/products/{productId}', function (int $productId) {
    $product = DB::table('Produkt as p')
        ->leftJoin('Kategoria as k', 'k.id', '=', 'p.kategoriaId')
        ->select('p.id', 'p.nazov', 'p.popis', 'p.created_at', 'k.nazov as kategoria_nazov')
        ->selectSub(function ($query) {
            $query->from('VariantProduktu as v')
                ->select('v.id')
                ->whereColumn('v.produktId', 'p.id')
                ->where(function ($variantQuery) {
                    $variantQuery->whereNull('v.aktivny')->orWhere('v.aktivny', true);
                })
                ->orderBy('v.id')
                ->limit(1);
        }, 'variant_id')
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
            $query->from('VariantProduktu as v')
                ->selectRaw('COALESCE(SUM(v.skladom), 0)')
                ->whereColumn('v.produktId', 'p.id')
                ->where(function ($variantQuery) {
                    $variantQuery->whereNull('v.aktivny')->orWhere('v.aktivny', true);
                });
        }, 'stock_total')
        ->selectSub(function ($query) {
            $query->from('ProduktovyObrazok as po')
                ->select('po.url')
                ->whereColumn('po.produktId', 'p.id')
                ->orderByRaw('COALESCE(po.poradie, 9999)')
                ->orderBy('po.id')
                ->limit(1);
        }, 'image_url')
        ->where('p.id', $productId)
        ->where(function ($productQuery) {
            $productQuery->whereNull('p.aktivny')->orWhere('p.aktivny', true);
        })
        ->first();

    abort_if(!$product, 404);

    $product->image_url = ltrim((string) $product->image_url, '/');
    $product->stock_total = (int) $product->stock_total;
    $product->stock_status = $product->stock_total > 0 ? 'In stock' : 'Out of stock';

    return view('pages.product-detail', compact('product'));
})->name('products.show');

Route::get('/cart', function (Request $request) {
    $cart = $request->session()->get('cart', []);

    if (empty($cart)) {
        return view('pages.shopping-cart', [
            'cartItems' => collect(),
            'subtotal' => 0,
        ]);
    }

    $variantIds = array_keys($cart);

    $variantRows = DB::table('VariantProduktu as v')
        ->join('Produkt as p', 'p.id', '=', 'v.produktId')
        ->select('v.id', 'v.produktId', 'v.nazov as variant_nazov', 'v.cena', 'v.skladom', 'p.nazov as produkt_nazov')
        ->selectSub(function ($query) {
            $query->from('ProduktovyObrazok as po')
                ->select('po.url')
                ->whereColumn('po.produktId', 'p.id')
                ->orderByRaw('COALESCE(po.poradie, 9999)')
                ->orderBy('po.id')
                ->limit(1);
        }, 'image_url')
        ->whereIn('v.id', $variantIds)
        ->where(function ($productQuery) {
            $productQuery->whereNull('p.aktivny')->orWhere('p.aktivny', true);
        })
        ->where(function ($variantQuery) {
            $variantQuery->whereNull('v.aktivny')->orWhere('v.aktivny', true);
        })
        ->get()
        ->keyBy('id');

    $cartItems = collect();
    $sanitizedCart = [];

    foreach ($cart as $variantId => $quantity) {
        $variantIdInt = (int) $variantId;

        if (!$variantRows->has($variantIdInt)) {
            continue;
        }

        $row = $variantRows->get($variantIdInt);
        $maxStock = max(0, (int) $row->skladom);

        if ($maxStock === 0) {
            continue;
        }

        $safeQty = max(1, min((int) $quantity, $maxStock));
        $sanitizedCart[$variantIdInt] = $safeQty;

        $price = (float) $row->cena;
        $imagePath = ltrim((string) $row->image_url, '/');

        $cartItems->push((object) [
            'variant_id' => $variantIdInt,
            'product_id' => (int) $row->produktId,
            'name' => $row->produkt_nazov,
            'price' => $price,
            'quantity' => $safeQty,
            'line_total' => $price * $safeQty,
            'max_stock' => $maxStock,
            'image_url' => $imagePath,
        ]);
    }

    $request->session()->put('cart', $sanitizedCart);

    return view('pages.shopping-cart', [
        'cartItems' => $cartItems,
        'subtotal' => (float) $cartItems->sum('line_total'),
    ]);
})->name('cart.index');

Route::post('/cart/add', function (Request $request) {
    $validated = $request->validate([
        'product_id' => ['required', 'integer', 'min:1'],
        'quantity' => ['required', 'integer', 'min:1', 'max:99'],
    ]);

    $variant = DB::table('VariantProduktu as v')
        ->join('Produkt as p', 'p.id', '=', 'v.produktId')
        ->select('v.id', 'v.skladom')
        ->where('p.id', $validated['product_id'])
        ->where(function ($productQuery) {
            $productQuery->whereNull('p.aktivny')->orWhere('p.aktivny', true);
        })
        ->where(function ($variantQuery) {
            $variantQuery->whereNull('v.aktivny')->orWhere('v.aktivny', true);
        })
        ->orderBy('v.id')
        ->first();

    if (!$variant) {
        return redirect()->back()->with('cart_error', 'Product variant was not found.');
    }

    $maxStock = max(0, (int) $variant->skladom);

    if ($maxStock === 0) {
        return redirect()->back()->with('cart_error', 'This product is currently out of stock.');
    }

    $cart = $request->session()->get('cart', []);
    $existingQty = (int) ($cart[$variant->id] ?? 0);
    $desiredQty = $existingQty + (int) $validated['quantity'];
    $cart[$variant->id] = min($desiredQty, $maxStock);

    $request->session()->put('cart', $cart);

    return redirect()->route('cart.index')->with('cart_success', 'Product added to cart.');
})->name('cart.add');

Route::post('/cart/update', function (Request $request) {
    $validated = $request->validate([
        'variant_id' => ['required', 'integer', 'min:1'],
        'quantity' => ['required', 'integer', 'min:0', 'max:99'],
    ]);

    $cart = $request->session()->get('cart', []);
    $variantId = (int) $validated['variant_id'];
    $quantity = (int) $validated['quantity'];

    if (!array_key_exists($variantId, $cart)) {
        return redirect()->route('cart.index');
    }

    if ($quantity === 0) {
        unset($cart[$variantId]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('cart_success', 'Item removed from cart.');
    }

    $variant = DB::table('VariantProduktu as v')
        ->select('v.skladom')
        ->where('v.id', $variantId)
        ->where(function ($variantQuery) {
            $variantQuery->whereNull('v.aktivny')->orWhere('v.aktivny', true);
        })
        ->first();

    if (!$variant || (int) $variant->skladom <= 0) {
        unset($cart[$variantId]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('cart_error', 'Selected item is no longer available.');
    }

    $cart[$variantId] = min($quantity, (int) $variant->skladom);
    $request->session()->put('cart', $cart);

    return redirect()->route('cart.index')->with('cart_success', 'Cart updated.');
})->name('cart.update');

Route::post('/cart/remove', function (Request $request) {
    $validated = $request->validate([
        'variant_id' => ['required', 'integer', 'min:1'],
    ]);

    $cart = $request->session()->get('cart', []);
    unset($cart[(int) $validated['variant_id']]);
    $request->session()->put('cart', $cart);

    return redirect()->route('cart.index')->with('cart_success', 'Item removed from cart.');
})->name('cart.remove');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
