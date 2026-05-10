<?php

namespace Tests\Feature\Auth;

use App\Models\CartItem;
use App\Models\Produkt;
use App\Models\User;
use App\Models\VariantProduktu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_customer_session_cart_is_merged_into_persistent_cart_after_login(): void
    {
        $user = User::factory()->create([
            'role' => 'CUSTOMER',
            'password' => bcrypt('password'),
        ]);

        $product = Produkt::create([
            'nazov' => 'Portable cart product',
            'popis' => 'A product for cart portability testing.',
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

        CartItem::create([
            'user_id' => $user->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'expires_at' => now()->addDay(),
        ]);

        $this
            ->withSession(['cart' => [$variant->id => 2]])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticatedAs($user);
        $this->assertSame([$variant->id => 3], session('cart'));
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'variant_id' => $variant->id,
            'quantity' => 3,
        ]);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
