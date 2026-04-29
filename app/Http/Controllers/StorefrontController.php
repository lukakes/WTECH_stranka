<?php

namespace App\Http\Controllers;

use App\Models\Adresa;
use App\Models\Doprava;
use App\Models\Objednavka;
use App\Models\Platba;
use App\Models\PolozkaObjednavky;
use App\Models\Produkt;
use App\Models\VariantProduktu;
use App\Models\Zakaznik;
use App\Services\CartService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StorefrontController extends Controller
{
    public function home()
    {
        if (!Schema::hasTable('produkty')) {
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

    public function products(Request $request, ?string $categorySlug = null)
    {
        $categoryOptions = [
            'stickers' => 'Stickers',
            'pins' => 'Pins',
            'patches' => 'Patches',
            'plushies' => 'Plushies',
        ];

        $forcedCategory = $categorySlug !== null
            ? strtolower(trim($categorySlug))
            : null;

        if ($forcedCategory !== null && !array_key_exists($forcedCategory, $categoryOptions)) {
            abort(404);
        }

        $category = strtolower((string) $request->query('category', 'all'));
        $availability = strtolower((string) $request->query('availability', 'all'));
        $sort = strtolower((string) $request->query('sort', 'featured'));
        $search = trim((string) $request->query('q', ''));

        $allowedAvailability = ['all', 'in-stock', 'out-of-stock'];
        $allowedSort = ['featured', 'name-asc', 'name-desc', 'price-asc', 'price-desc', 'newest'];

        if ($forcedCategory !== null) {
            $category = $forcedCategory;
        } elseif ($category !== 'all' && !array_key_exists($category, $categoryOptions)) {
            $category = 'all';
        }

        if (!in_array($availability, $allowedAvailability, true)) {
            $availability = 'all';
        }

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'featured';
        }

        $query = Produkt::query()
            ->active()
            ->with([
                'category:id,nazov',
                'images' => function ($query) {
                    $query->orderByRaw('COALESCE(poradie, 9999)')->orderBy('id');
                },
            ])
            ->withMin([
                'variants as min_variant_price' => function (Builder $query) {
                    $query->active();
                },
            ], 'cena')
            ->withSum([
                'variants as active_stock_total' => function (Builder $query) {
                    $query->active();
                },
            ], 'skladom');

        if ($search !== '') {
            $query->where(function (Builder $searchQuery) use ($search) {
                $searchQuery
                    ->where('nazov', 'like', "%{$search}%")
                    ->orWhere('popis', 'like', "%{$search}%");
            });
        }

        if ($category !== 'all') {
            $query->whereHas('category', function (Builder $categoryQuery) use ($category, $categoryOptions) {
                $categoryQuery->where('nazov', $categoryOptions[$category]);
            });
        }

        if ($availability === 'in-stock') {
            $query->whereHas('variants', function (Builder $variantQuery) {
                $variantQuery->active()->where('skladom', '>', 0);
            });
        }

        if ($availability === 'out-of-stock') {
            $query->whereDoesntHave('variants', function (Builder $variantQuery) {
                $variantQuery->active()->where('skladom', '>', 0);
            });
        }

        if ($sort === 'name-asc') {
            $query->orderBy('nazov')->orderBy('id');
        } elseif ($sort === 'name-desc') {
            $query->orderByDesc('nazov')->orderBy('id');
        } elseif ($sort === 'price-asc') {
            $query->orderBy('zakladna_cena', 'asc')->orderBy('id');
        } elseif ($sort === 'price-desc') {
            $query->orderByDesc('zakladna_cena')->orderBy('id');
        } elseif ($sort === 'newest') {
            $query->orderByDesc('created_at')->orderByDesc('id');
        } else {
            $query->orderBy('id');
        }

        $products = $query
            ->paginate(12)
            ->withQueryString()
            ->through(function (Produkt $product) {
                $imageUrl = $this->sanitizeImagePath($product->images->first()?->url);
                $stockTotal = max(0, (int) ($product->active_stock_total ?? 0));

                return (object) [
                    'id' => (int) $product->id,
                    'nazov' => $product->nazov,
                    'kategoria_nazov' => $product->category?->nazov,
                    'cena' => (float) ($product->min_variant_price ?? $product->zakladna_cena ?? 0),
                    'stock_total' => $stockTotal,
                    'stock_status' => $stockTotal > 0 ? 'in-stock' : 'out-of-stock',
                    'image_url' => $imageUrl,
                    'image_path' => $imageUrl !== '' ? $imageUrl : 'images/Products/prod-img-1.png',
                ];
            });

        return view('pages.products', [
            'products' => $products,
            'realProductsCount' => $products->total(),
            'categoryOptions' => $categoryOptions,
            'filters' => [
                'q' => $search,
                'category' => $category,
                'availability' => $availability,
                'sort' => $sort,
            ],
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

    public function cartIndex(Request $request, CartService $cartService)
    {
        return view('pages.shopping-cart', $this->buildCartData($request, $cartService));
    }

    public function checkout(Request $request, CartService $cartService)
    {
        $cartData = $this->buildCartData($request, $cartService);
        $deliveryOptions = $this->activeDeliveryOptions();
        $paymentOptions = $this->activePaymentOptions();
        $selectedDeliveryId = (int) old('delivery_id', $deliveryOptions->first()?->id ?? 0);
        $selectedPaymentId = (int) old('payment_id', $paymentOptions->first()?->id ?? 0);
        $selectedDelivery = $deliveryOptions->firstWhere('id', $selectedDeliveryId) ?? $deliveryOptions->first();
        $selectedPayment = $paymentOptions->firstWhere('id', $selectedPaymentId) ?? $paymentOptions->first();
        $deliveryFee = $cartData['cartItems']->isEmpty() ? 0.0 : (float) ($selectedDelivery?->cena ?? 0);
        $paymentFee = $cartData['cartItems']->isEmpty() ? 0.0 : (float) ($selectedPayment?->poplatok ?? 0);
        $total = (float) $cartData['subtotal'] + $deliveryFee + $paymentFee;

        return view('pages.checkout', $cartData + [
            'deliveryOptions' => $deliveryOptions,
            'paymentOptions' => $paymentOptions,
            'selectedDeliveryId' => $selectedDeliveryId,
            'selectedPaymentId' => $selectedPaymentId,
            'deliveryFee' => $deliveryFee,
            'paymentFee' => $paymentFee,
            'total' => $total,
        ]);
    }

    public function checkoutStore(Request $request, CartService $cartService)
    {
        $deliveryOptions = $this->activeDeliveryOptions();
        $paymentOptions = $this->activePaymentOptions();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:100'],
            'postal' => ['required', 'string', 'max:20'],
            'delivery_id' => ['required', 'integer', Rule::in($deliveryOptions->pluck('id')->all())],
            'payment_id' => ['required', 'integer', Rule::in($paymentOptions->pluck('id')->all())],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = $cartService->getCart($request);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('cart_error', 'Your cart is empty.');
        }

        $delivery = $deliveryOptions->firstWhere('id', (int) $validated['delivery_id']);
        $payment = $paymentOptions->firstWhere('id', (int) $validated['payment_id']);

        try {
            $order = DB::transaction(function () use ($cart, $cartService, $delivery, $payment, $request, $validated) {
                $variants = VariantProduktu::query()
                    ->active()
                    ->with([
                        'product' => function ($query) {
                            $query->active();
                        },
                    ])
                    ->whereIn('id', array_keys($cart))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = 0.0;
                $orderLines = [];

                foreach ($cart as $variantId => $quantity) {
                    $variant = $variants->get((int) $variantId);

                    if (!$variant || !$variant->product || (int) $variant->skladom < (int) $quantity) {
                        throw new \RuntimeException('Some cart items are no longer available in the requested quantity.');
                    }

                    $price = (float) $variant->cena;
                    $lineTotal = $price * (int) $quantity;
                    $subtotal += $lineTotal;

                    $orderLines[] = [
                        'variant' => $variant,
                        'quantity' => (int) $quantity,
                        'price' => $price,
                        'line_total' => $lineTotal,
                    ];
                }

                $fullName = trim($validated['first_name'].' '.$validated['last_name']);

                $customer = Zakaznik::updateOrCreate(
                    ['email' => $validated['email']],
                    [
                        'meno' => $fullName,
                        'telefon' => $validated['phone'] ?? null,
                        'created_at' => Date::now(),
                    ]
                );

                $address = Adresa::create([
                    'zakaznik_id' => $customer->id,
                    'meno' => $fullName,
                    'ulica' => $validated['address'],
                    'mesto' => $validated['city'],
                    'psc' => $validated['postal'],
                    'stat' => 'Slovakia',
                    'created_at' => Date::now(),
                ]);

                $order = Objednavka::create([
                    'zakaznik_id' => $customer->id,
                    'adresa_id' => $address->id,
                    'doprava_id' => $delivery->id,
                    'platba_id' => $payment->id,
                    'stav' => 'PENDING',
                    'subtotal' => $subtotal,
                    'doprava_cena' => (float) $delivery->cena,
                    'platba_poplatok' => (float) $payment->poplatok,
                    'total' => $subtotal + (float) $delivery->cena + (float) $payment->poplatok,
                    'created_at' => Date::now(),
                ]);

                foreach ($orderLines as $line) {
                    PolozkaObjednavky::create([
                        'objednavka_id' => $order->id,
                        'variant_id' => $line['variant']->id,
                        'mnozstvo' => $line['quantity'],
                        'jednotkova_cena' => $line['price'],
                        'celkova_cena' => $line['line_total'],
                    ]);

                    $line['variant']->decrement('skladom', $line['quantity']);
                }

                $cartService->clearCart($request);

                return $order;
            });
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route('cart.index')
                ->with('cart_error', $exception->getMessage());
        }

        return redirect()
            ->route('checkout.success', ['order' => $order->id])
            ->with('checkout_success', 'Order was created successfully.');
    }

    public function checkoutSuccess(int $order)
    {
        $orderModel = Objednavka::query()
            ->with(['zakaznik', 'polozky.variant.product'])
            ->findOrFail($order);

        return view('pages.checkout-success', [
            'order' => $orderModel,
        ]);
    }

    public function cartAdd(Request $request, CartService $cartService)
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

        $cart = $cartService->getCart($request);
        $variantId = (int) $variant->id;
        $existingQty = (int) ($cart[$variantId] ?? 0);
        $desiredQty = $existingQty + (int) $validated['quantity'];
        $cart[$variantId] = min($desiredQty, $maxStock);

        $cartService->storeCart($request, $cart);

        return redirect()->route('cart.index')->with('cart_success', 'Product added to cart.');
    }

    public function cartUpdate(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $cart = $cartService->getCart($request);
        $variantId = (int) $validated['variant_id'];
        $quantity = (int) $validated['quantity'];

        if (!array_key_exists($variantId, $cart)) {
            return redirect()->route('cart.index');
        }

        if ($quantity === 0) {
            unset($cart[$variantId]);
            $cartService->storeCart($request, $cart);

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
            $cartService->storeCart($request, $cart);

            return redirect()->route('cart.index')->with('cart_error', 'Selected item is no longer available.');
        }

        $cart[$variantId] = min($quantity, (int) $variant->skladom);
        $cartService->storeCart($request, $cart);

        return redirect()->route('cart.index')->with('cart_success', 'Cart updated.');
    }

    public function cartRemove(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $cartService->getCart($request);
        unset($cart[(int) $validated['variant_id']]);
        $cartService->storeCart($request, $cart);

        return redirect()->route('cart.index')->with('cart_success', 'Item removed from cart.');
    }

    private function buildCartData(Request $request, CartService $cartService): array
    {
        $cart = $cartService->getCart($request);

        if (empty($cart)) {
            return [
                'cartItems' => collect(),
                'subtotal' => 0,
            ];
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
                'product_id' => (int) $row->produkt_id,
                'name' => $row->product?->nazov ?? 'Product',
                'price' => $price,
                'quantity' => $safeQty,
                'line_total' => $price * $safeQty,
                'max_stock' => $maxStock,
                'image_url' => $imagePath,
                'image_path' => $imagePath !== '' ? $imagePath : 'images/Products/prod-img-1.png',
            ]);
        }

        $cartService->storeCart($request, $sanitizedCart);

        return [
            'cartItems' => $cartItems,
            'subtotal' => (float) $cartItems->sum('line_total'),
        ];
    }

    private function activeDeliveryOptions()
    {
        $options = Doprava::query()
            ->active()
            ->orderBy('cena')
            ->orderBy('id')
            ->get();

        if ($options->isNotEmpty()) {
            return $options;
        }

        Doprava::updateOrCreate(
            ['nazov' => 'Courier delivery'],
            ['cena' => 4.90, 'odhad_dni' => 3, 'aktivna' => true]
        );

        Doprava::updateOrCreate(
            ['nazov' => 'Pickup point'],
            ['cena' => 2.90, 'odhad_dni' => 4, 'aktivna' => true]
        );

        return Doprava::query()
            ->active()
            ->orderBy('cena')
            ->orderBy('id')
            ->get();
    }

    private function activePaymentOptions()
    {
        $options = Platba::query()
            ->active()
            ->orderBy('poplatok')
            ->orderBy('id')
            ->get();

        if ($options->isNotEmpty()) {
            return $options;
        }

        Platba::updateOrCreate(
            ['sposob_platby' => 'Card payment'],
            ['poplatok' => 0.00, 'aktivna' => true]
        );

        Platba::updateOrCreate(
            ['sposob_platby' => 'Cash on delivery'],
            ['poplatok' => 1.50, 'aktivna' => true]
        );

        return Platba::query()
            ->active()
            ->orderBy('poplatok')
            ->orderBy('id')
            ->get();
    }

    private function sanitizeImagePath(?string $imagePath): string
    {
        $sanitizedPath = ltrim((string) $imagePath, '/');

        return (string) preg_replace('/^\.\.\//', '', $sanitizedPath);
    }
}
