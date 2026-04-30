<?php

namespace Tests\Feature;

use App\Models\Produkt;
use App\Models\User;
use App\Models\VariantProduktu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_products(): void
    {
        $this->get(route('admin.products.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_cannot_open_admin_products(): void
    {
        $customer = User::factory()->create([
            'role' => 'CUSTOMER',
        ]);

        $this->actingAs($customer)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_open_product_list(): void
    {
        $admin = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        Produkt::create([
            'nazov' => 'Admin visible product',
            'popis' => 'Visible in the admin product list.',
            'zakladna_cena' => 12.50,
            'aktivny' => true,
            'created_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Admin visible product');
    }

    public function test_admin_login_does_not_keep_customer_session_cart(): void
    {
        $admin = User::factory()->create([
            'role' => 'ADMIN',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = Produkt::create([
            'nazov' => 'Cart product',
            'popis' => 'Should not stay in an admin cart.',
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

        $this
            ->withSession(['cart' => [$variant->id => 2]])
            ->post(route('login'), [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticatedAs($admin);
        $this->assertSame([], session('cart', []));
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $admin->id,
            'variant_id' => $variant->id,
        ]);
    }

    public function test_admin_product_search_does_not_fill_store_header_search(): void
    {
        $admin = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.products.index', ['q' => 'admin-query']));

        $response->assertOk();
        $response->assertSee('id="store-product-search" placeholder="Search the store" autocomplete="off" value=""', false);
        $response->assertSee('name="q" value="admin-query" placeholder="Search products"', false);
    }
}
