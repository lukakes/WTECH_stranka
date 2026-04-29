<?php

namespace Tests\Feature;

use App\Models\Produkt;
use App\Models\User;
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
}
