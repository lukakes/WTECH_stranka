<?php

namespace Tests\Feature;

use App\Models\Doprava;
use App\Models\CartItem;
use App\Models\Platba;
use App\Models\Produkt;
use App\Models\User;
use App\Models\VariantProduktu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_place_order_from_session_cart(): void
    {
        [$variant] = $this->createCheckoutCatalog();

        $response = $this->placeSessionCartOrder($variant);

        $response->assertRedirect(route('checkout.success', ['order' => 1]));
        $response->assertSessionHas('checkout_success');
        $response->assertSessionHas('claimable_order_ids', [1]);

        $this->assertDatabaseHas('zakaznici', [
            'email' => 'richard@example.com',
            'meno' => 'Richard Klein',
        ]);

        $this->assertDatabaseHas('objednavky', [
            'id' => 1,
            'user_id' => null,
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

    public function test_guest_order_is_linked_to_existing_account_after_login(): void
    {
        [$variant] = $this->createCheckoutCatalog();

        $this->placeSessionCartOrder($variant);

        $user = User::factory()->create([
            'role' => 'CUSTOMER',
            'email' => 'richard@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->post(route('login'), [
            'email' => 'richard@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('objednavky', [
            'id' => 1,
            'user_id' => $user->id,
        ]);
        $this->assertNull(session('claimable_order_ids'));
    }

    public function test_guest_order_is_linked_to_new_account_after_registration(): void
    {
        [$variant] = $this->createCheckoutCatalog();

        $this->placeSessionCartOrder($variant);

        $this->post(route('register'), [
            'name' => 'Richard Klein',
            'email' => 'richard@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'richard@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('objednavky', [
            'id' => 1,
            'user_id' => $user->id,
        ]);
        $this->assertNull(session('claimable_order_ids'));
    }

    public function test_logged_in_customer_order_is_linked_immediately(): void
    {
        [$variant] = $this->createCheckoutCatalog();
        $user = User::factory()->create([
            'role' => 'CUSTOMER',
        ]);

        $this->actingAs($user);

        CartItem::create([
            'user_id' => $user->id,
            'variant_id' => $variant->id,
            'quantity' => 2,
            'expires_at' => now()->addDay(),
        ]);

        $this->postCheckoutForm();

        $this->assertDatabaseHas('objednavky', [
            'id' => 1,
            'user_id' => $user->id,
        ]);
        $this->assertNull(session('claimable_order_ids'));
    }

    private function createCheckoutCatalog(): array
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

        return [$variant, $delivery, $payment];
    }

    private function placeSessionCartOrder(VariantProduktu $variant)
    {
        $delivery = Doprava::firstOrFail();
        $payment = Platba::firstOrFail();

        return $this
            ->withSession(['cart' => [$variant->id => 2]])
            ->post(route('checkout.store'), $this->checkoutFormData($delivery, $payment));
    }

    private function postCheckoutForm()
    {
        $delivery = Doprava::firstOrFail();
        $payment = Platba::firstOrFail();

        return $this->post(route('checkout.store'), $this->checkoutFormData($delivery, $payment));
    }

    private function checkoutFormData(Doprava $delivery, Platba $payment): array
    {
        return [
            'first_name' => 'Richard',
            'last_name' => 'Klein',
            'email' => 'richard@example.com',
            'phone' => '+421 900 000 000',
            'address' => 'Main street 12',
            'city' => 'Nitrianske Pravno',
            'postal' => '972 13',
            'delivery_id' => $delivery->id,
            'payment_id' => $payment->id,
        ];
    }
}
