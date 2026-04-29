<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produkt;
use Illuminate\Http\Request;

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
}
