<?php

namespace App\Http\Controllers;

use App\Models\Produkt;
use App\Models\VariantProduktu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StorefrontController extends Controller
{
    public function home()
    {
        if (!Schema::hasTable('Produkt')) {
            return view('pages.home', ['featuredProducts' => collect()]);
        }

        $featuredProducts = Produkt::query()
            ->active()
            ->with([
                'variants' => function ($query) {
                    $query->active()->orderBy('id');
                },
                'images' => function ($query) {
                    $query->orderByRaw('COALESCE(poradie, 9999)')->orderBy('id');
                },
            ])
            ->orderBy('id')
            ->limit(4)
            ->get()
            ->map(function (Produkt $product) {
                $variant = $product->variants->first();
                $imageUrl = $this->sanitizeImagePath($product->images->first()?->url);

                return (object) [
                    'id' => (int) $product->id,
                    'nazov' => $product->nazov,
                    'cena' => (float) ($variant?->cena ?? $product->zakladna_cena ?? 0),
                    'image_url' => $imageUrl,
                ];
            });

        return view('pages.home', compact('featuredProducts'));
    }

    public function products()
    {
        $products = Produkt::query()
            ->active()
            ->with([
                'category:id,nazov',
                'variants' => function ($query) {
                    $query->active()->orderBy('id');
                },
                'images' => function ($query) {
                    $query->orderByRaw('COALESCE(poradie, 9999)')->orderBy('id');
                },
            ])
            ->orderBy('id')
            ->get()
            ->values()
            ->map(function (Produkt $product, int $index) {
                $variant = $product->variants->first();
                $stockTotal = (int) $product->variants->sum('skladom');
                $imageUrl = $this->sanitizeImagePath($product->images->first()?->url);

                return (object) [
                    'id' => (int) $product->id,
                    'nazov' => $product->nazov,
                    'kategoria_nazov' => $product->category?->nazov,
                    'cena' => (float) ($variant?->cena ?? $product->zakladna_cena ?? 0),
                    'stock_total' => $stockTotal,
                    'stock_status' => $stockTotal > 0 ? 'in-stock' : 'out-of-stock',
                    'sort_order' => $index + 1,
                    'image_url' => $imageUrl,
                    'image_path' => $imageUrl !== '' ? $imageUrl : 'images/Products/prod-img-1.png',
                ];
            });

        return view('pages.products', [
            'products' => $products,
            'realProductsCount' => $products->count(),
        ]);
    }

    public function showProduct(int $productId)
    {
        $productModel = Produkt::query()
            ->active()
            ->with([
                'category:id,nazov',
                'variants' => function ($query) {
                    $query->active()->orderBy('id');
                },
                'images' => function ($query) {
                    $query->orderByRaw('COALESCE(poradie, 9999)')->orderBy('id');
                },
            ])
            ->find($productId);

        abort_if(!$productModel, 404);

        $variant = $productModel->variants->first();
        $stockTotal = (int) $productModel->variants->sum('skladom');
        $imageUrl = $this->sanitizeImagePath($productModel->images->first()?->url);
        $descriptionText = trim((string) ($productModel->popis ?? ''));
        $descriptionText = $descriptionText !== ''
            ? $descriptionText
            : 'This product currently has no detailed description.';

        $product = (object) [
            'id' => (int) $productModel->id,
            'nazov' => $productModel->nazov,
            'popis' => $productModel->popis,
            'created_at' => $productModel->created_at,
            'kategoria_nazov' => $productModel->category?->nazov,
            'variant_id' => $variant?->id,
            'cena' => (float) ($variant?->cena ?? $productModel->zakladna_cena ?? 0),
            'stock_total' => $stockTotal,
            'stock_status' => $stockTotal > 0 ? 'In stock' : 'Out of stock',
            'image_url' => $imageUrl,
            'image_path' => $imageUrl !== '' ? $imageUrl : 'images/Products/prod-img-1.png',
            'description_text' => $descriptionText,
        ];

        return view('pages.product-detail', compact('product'));
    }

    public function cartIndex(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return view('pages.shopping-cart', [
                'cartItems' => collect(),
                'subtotal' => 0,
            ]);
        }

        $variantIds = array_keys($cart);

        $variantRows = VariantProduktu::query()
            ->active()
            ->with([
                'product' => function ($query) {
                    $query->active()->with([
                        'images' => function ($imageQuery) {
                            $imageQuery->orderByRaw('COALESCE(poradie, 9999)')->orderBy('id');
                        },
                    ]);
                },
            ])
            ->whereIn('id', $variantIds)
            ->get()
            ->filter(function (VariantProduktu $variant) {
                return $variant->product !== null;
            })
            ->keyBy('id');

        $cartItems = collect();
        $sanitizedCart = [];

        foreach ($cart as $variantId => $quantity) {
            $variantIdInt = (int) $variantId;

            if (!$variantRows->has($variantIdInt)) {
                continue;
            }

            /** @var VariantProduktu $row */
            $row = $variantRows->get($variantIdInt);
            $maxStock = max(0, (int) $row->skladom);

            if ($maxStock === 0) {
                continue;
            }

            $safeQty = max(1, min((int) $quantity, $maxStock));
            $sanitizedCart[$variantIdInt] = $safeQty;

            $price = (float) $row->cena;
            $imagePath = $this->sanitizeImagePath($row->product?->images->first()?->url);

            $cartItems->push((object) [
                'variant_id' => $variantIdInt,
                'product_id' => (int) $row->produktId,
                'name' => $row->product?->nazov ?? 'Product',
                'price' => $price,
                'quantity' => $safeQty,
                'line_total' => $price * $safeQty,
                'max_stock' => $maxStock,
                'image_url' => $imagePath,
                'image_path' => $imagePath !== '' ? $imagePath : 'images/Products/prod-img-1.png',
            ]);
        }

        $request->session()->put('cart', $sanitizedCart);

        return view('pages.shopping-cart', [
            'cartItems' => $cartItems,
            'subtotal' => (float) $cartItems->sum('line_total'),
        ]);
    }

    public function cartAdd(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Produkt::query()
            ->active()
            ->with([
                'variants' => function ($query) {
                    $query->active()->orderBy('id');
                },
            ])
            ->find($validated['product_id']);

        $variant = $product?->variants->first();

        if (!$variant) {
            return redirect()->back()->with('cart_error', 'Product variant was not found.');
        }

        $maxStock = max(0, (int) $variant->skladom);

        if ($maxStock === 0) {
            return redirect()->back()->with('cart_error', 'This product is currently out of stock.');
        }

        $cart = $request->session()->get('cart', []);
        $variantId = (int) $variant->id;
        $existingQty = (int) ($cart[$variantId] ?? 0);
        $desiredQty = $existingQty + (int) $validated['quantity'];
        $cart[$variantId] = min($desiredQty, $maxStock);

        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('cart_success', 'Product added to cart.');
    }

    public function cartUpdate(Request $request)
    {
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

        $variant = VariantProduktu::query()
            ->active()
            ->with([
                'product' => function ($query) {
                    $query->active();
                },
            ])
            ->find($variantId);

        if (!$variant || !$variant->product || (int) $variant->skladom <= 0) {
            unset($cart[$variantId]);
            $request->session()->put('cart', $cart);

            return redirect()->route('cart.index')->with('cart_error', 'Selected item is no longer available.');
        }

        $cart[$variantId] = min($quantity, (int) $variant->skladom);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('cart_success', 'Cart updated.');
    }

    public function cartRemove(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->session()->get('cart', []);
        unset($cart[(int) $validated['variant_id']]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('cart_success', 'Item removed from cart.');
    }

    private function sanitizeImagePath(?string $imagePath): string
    {
        $sanitizedPath = ltrim((string) $imagePath, '/');

        return (string) preg_replace('/^\.\.\//', '', $sanitizedPath);
    }
}