<?php

namespace Tests\Feature;

use App\Models\Produkt;
use App\Models\VariantProduktu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_filtered_by_max_price(): void
    {
        $cheapProduct = $this->createProductWithPrice('Cheap sticker', 5);
        $matchingProduct = $this->createProductWithPrice('Matching pin', 20);
        $expensiveProduct = $this->createProductWithPrice('Expensive plushie', 45);

        $response = $this->get(route('products', [
            'max_price' => 30,
        ]));

        $response
            ->assertOk()
            ->assertSee($cheapProduct->nazov)
            ->assertSee($matchingProduct->nazov)
            ->assertDontSee($expensiveProduct->nazov);
    }

    public function test_reversed_price_range_is_normalized(): void
    {
        $matchingProduct = $this->createProductWithPrice('Normalized range product', 20);
        $outsideProduct = $this->createProductWithPrice('Outside range product', 45);

        $response = $this->get(route('products', [
            'min_price' => 30,
            'max_price' => 10,
        ]));

        $response
            ->assertOk()
            ->assertSee($matchingProduct->nazov)
            ->assertDontSee($outsideProduct->nazov);
    }

    private function createProductWithPrice(string $name, float $price): Produkt
    {
        $product = Produkt::create([
            'nazov' => $name,
            'popis' => $name.' description',
            'zakladna_cena' => $price,
            'aktivny' => true,
            'created_at' => now(),
        ]);

        VariantProduktu::create([
            'produkt_id' => $product->id,
            'nazov' => 'Default',
            'cena' => $price,
            'skladom' => 10,
            'aktivny' => true,
        ]);

        return $product;
    }
}
