<?php

namespace Tests\Feature;

use App\Models\Doprava;
use App\Models\Platba;
use App\Models\Produkt;
use App\Models\VariantProduktu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order_from_session_cart(): void
    {
        $product = Produkt::create([
            'nazov' => 'Test product',
            'popis' => 'A product for checkout testing.',
            'zakladna_cena' => 10,
            'aktivny' => true,
            'created_at' => now(),
        ]);

        $variant = VariantProduktu::create([
            'produkt_id' => $product->id,
            'nazov' => 'Default',
            'cena' => 10,
            'skladom' => 5,
            'aktivny' => true,
        ]);

        $delivery = Doprava::create([
            'nazov' => 'Courier delivery',
            'cena' => 4.90,
            'odhad_dni' => 3,
            'aktivna' => true,
        ]);

        $payment = Platba::create([
            'sposob_platby' => 'Card payment',
            'poplatok' => 0,
            'aktivna' => true,
        ]);

        $response = $this
            ->withSession(['cart' => [$variant->id => 2]])
            ->post(route('checkout.store'), [
                'first_name' => 'Richard',
                'last_name' => 'Klein',
                'email' => 'richard@example.com',
                'phone' => '+421 900 000 000',
                'address' => 'Main street 12',
                'city' => 'Nitrianske Pravno',
                'postal' => '972 13',
                'delivery_id' => $delivery->id,
                'payment_id' => $payment->id,
            ]);

        $response->assertRedirect(route('checkout.success', ['order' => 1]));
        $response->assertSessionHas('checkout_success');

        $this->assertDatabaseHas('zakaznici', [
            'email' => 'richard@example.com',
            'meno' => 'Richard Klein',
        ]);

        $this->assertDatabaseHas('objednavky', [
            'id' => 1,
            'subtotal' => 20,
            'doprava_cena' => 4.90,
            'total' => 24.90,
        ]);

        $this->assertDatabaseHas('polozky_objednavky', [
            'objednavka_id' => 1,
            'variant_id' => $variant->id,
            'mnozstvo' => 2,
            'celkova_cena' => 20,
        ]);

        $this->assertDatabaseHas('varianty_produktu', [
            'id' => $variant->id,
            'skladom' => 3,
        ]);

        $this->assertSame([], session('cart', []));
    }
}
