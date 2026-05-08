<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategoria;
use App\Models\CartItem;
use App\Models\Produkt;
use App\Models\ProduktovyObrazok;
use App\Models\VariantProduktu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $products = Produkt::query()
            ->with([
                'category:id,nazov',
                'variants' => function ($query) {
                    $query->orderBy('id');
                },
                'images' => function ($query) {
                    $query->orderByRaw('COALESCE(poradie, 9999)')->orderBy('id');
                },
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('nazov', 'like', "%{$search}%")
                        ->orWhere('popis', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'categories' => Kategoria::query()->orderBy('nazov')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nazov' => ['required', 'string', 'max:150'],
            'popis' => ['required', 'string'],
            'kategoria_id' => ['required', 'integer', Rule::exists('kategorie', 'id')],
            'cena' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'skladom' => ['required', 'integer', 'min:0', 'max:999999'],
            'aktivny' => ['nullable', 'boolean'],
            'images' => ['required', 'array', 'min:2'],
            'images.*' => ['required', 'image', 'max:4096'],
        ]);

        $storedPaths = [];

        try {
            $product = DB::transaction(function () use ($request, $validated, &$storedPaths) {
                $product = Produkt::create([
                    'nazov' => $validated['nazov'],
                    'popis' => $validated['popis'],
                    'zakladna_cena' => $validated['cena'],
                    'kategoria_id' => $validated['kategoria_id'],
                    'aktivny' => $request->boolean('aktivny'),
                    'created_at' => now(),
                ]);

                VariantProduktu::create([
                    'produkt_id' => $product->id,
                    'nazov' => 'Default',
                    'cena' => $validated['cena'],
                    'skladom' => $validated['skladom'],
                    'aktivny' => $request->boolean('aktivny'),
                ]);

                foreach ($request->file('images', []) as $index => $image) {
                    $path = $image->store('products', 'public');
                    $storedPaths[] = $path;

                    ProduktovyObrazok::create([
                        'produkt_id' => $product->id,
                        'url' => 'storage/'.$path,
                        'poradie' => $index + 1,
                    ]);
                }

                return $product;
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.products.index')
            ->with('admin_success', 'Product '.$product->nazov.' was created.');
    }

    public function edit(Produkt $product)
    {
        $product->load([
            'category:id,nazov',
            'variants' => function ($query) {
                $query->orderBy('id');
            },
            'images' => function ($query) {
                $query->orderByRaw('COALESCE(poradie, 9999)')->orderBy('id');
            },
        ]);

        return view('admin.products.edit', [
            'product' => $product,
            'variant' => $product->variants->first(),
            'categories' => Kategoria::query()->orderBy('nazov')->get(),
        ]);
    }

    public function update(Request $request, Produkt $product)
    {
        $validated = $request->validate([
            'nazov' => ['required', 'string', 'max:150'],
            'popis' => ['required', 'string'],
            'kategoria_id' => ['required', 'integer', Rule::exists('kategorie', 'id')],
            'cena' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'skladom' => ['required', 'integer', 'min:0', 'max:999999'],
            'aktivny' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['required', 'image', 'max:4096'],
        ]);

        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $validated, $product, &$storedPaths) {
                $product->update([
                    'nazov' => $validated['nazov'],
                    'popis' => $validated['popis'],
                    'zakladna_cena' => $validated['cena'],
                    'kategoria_id' => $validated['kategoria_id'],
                    'aktivny' => $request->boolean('aktivny'),
                ]);

                VariantProduktu::updateOrCreate(
                    ['produkt_id' => $product->id, 'nazov' => 'Default'],
                    [
                        'cena' => $validated['cena'],
                        'skladom' => $validated['skladom'],
                        'aktivny' => $request->boolean('aktivny'),
                    ]
                );

                $nextOrder = ((int) $product->images()->max('poradie')) + 1;

                foreach ($request->file('images', []) as $index => $image) {
                    $path = $image->store('products', 'public');
                    $storedPaths[] = $path;

                    ProduktovyObrazok::create([
                        'produkt_id' => $product->id,
                        'url' => 'storage/'.$path,
                        'poradie' => $nextOrder + $index,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.products.index')
            ->with('admin_success', 'Product was updated.');
    }

    public function destroyImage(Produkt $product, ProduktovyObrazok $image)
    {
        abort_if((int) $image->produkt_id !== (int) $product->id, 404);

        if ($product->images()->count() <= 2) {
            return redirect()
                ->route('admin.products.edit', ['product' => $product->id])
                ->with('admin_error', 'Product must keep at least two images.');
        }

        $this->deleteProductImageFile($image);
        $image->delete();

        return redirect()
            ->route('admin.products.edit', ['product' => $product->id])
            ->with('admin_success', 'Image was removed.');
    }

    public function destroy(Produkt $product)
    {
        $product->load(['images', 'variants']);

        if ($product->orderItems()->exists()) {
            return redirect()
                ->route('admin.products.index')
                ->with('admin_error', 'Product cannot be deleted because it already appears in an order.');
        }

        DB::transaction(function () use ($product) {
            $variantIds = $product->variants->pluck('id')->all();

            if (!empty($variantIds)) {
                CartItem::whereIn('variant_id', $variantIds)->delete();
            }

            foreach ($product->images as $image) {
                $this->deleteProductImageFile($image);
                $image->delete();
            }

            $product->variants()->delete();
            $product->delete();
        });

        return redirect()
            ->route('admin.products.index')
            ->with('admin_success', 'Product was deleted.');
    }

    private function deleteProductImageFile(ProduktovyObrazok $image): void
    {
        $path = ltrim((string) $image->url, '/');

        if (!str_starts_with($path, 'storage/products/')) {
            return;
        }

        Storage::disk('public')->delete(substr($path, strlen('storage/')));
    }
}
