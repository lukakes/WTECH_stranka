<?php

namespace Tests\Feature;

use App\Models\Produkt;
use App\Models\ProduktovyObrazok;
use App\Models\VariantProduktu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_shows_gallery_for_multiple_images(): void
    {
        $product = Produkt::create([
            'nazov' => 'Gallery product',
            'popis' => 'Product with multiple gallery images.',
            'zakladna_cena' => 14,
            'aktivny' => true,
            'created_at' => now(),
        ]);

        VariantProduktu::create([
            'produkt_id' => $product->id,
            'nazov' => 'Default',
            'cena' => 14,
            'skladom' => 4,
            'aktivny' => true,
        ]);

        ProduktovyObrazok::create([
            'produkt_id' => $product->id,
            'url' => 'images/Products/prod-img-1.png',
            'poradie' => 1,
        ]);

        ProduktovyObrazok::create([
            'produkt_id' => $product->id,
            'url' => 'images/Products/prod-img-2.png',
            'poradie' => 2,
        ]);

        $this->get(route('products.show', ['productId' => $product->id]))
            ->assertOk()
            ->assertSee('product-gallery-main')
            ->assertSee('product-gallery-prev')
            ->assertSee('product-gallery-next')
            ->assertSee('data-image="'.asset('images/Products/prod-img-2.png').'"', false);
    }
}
