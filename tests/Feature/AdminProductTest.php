<?php

namespace Tests\Feature;

use App\Models\Produkt;
use App\Models\Adresa;
use App\Models\Doprava;
use App\Models\Kategoria;
use App\Models\Objednavka;
use App\Models\Platba;
use App\Models\PolozkaObjednavky;
use App\Models\ProduktovyObrazok;
use App\Models\User;
use App\Models\VariantProduktu;
use App\Models\Zakaznik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_open_create_product_form(): void
    {
        $admin = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        Kategoria::create([
            'nazov' => 'Pins',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Create product')
            ->assertSee('Pins');
    }

    public function test_admin_can_create_product_with_two_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        $category = Kategoria::create([
            'nazov' => 'Stickers',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'nazov' => 'New admin product',
                'popis' => 'Created from the admin product form.',
                'kategoria_id' => $category->id,
                'cena' => 12.50,
                'skladom' => 15,
                'aktivny' => '1',
                'images' => [
                    UploadedFile::fake()->create('front.jpg', 100, 'image/jpeg'),
                    UploadedFile::fake()->create('back.jpg', 100, 'image/jpeg'),
                ],
            ]);

        $response
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('admin_success');

        $product = Produkt::where('nazov', 'New admin product')->firstOrFail();

        $this->assertDatabaseHas('produkty', [
            'id' => $product->id,
            'popis' => 'Created from the admin product form.',
            'kategoria_id' => $category->id,
            'aktivny' => true,
        ]);

        $this->assertDatabaseHas('varianty_produktu', [
            'produkt_id' => $product->id,
            'nazov' => 'Default',
            'skladom' => 15,
            'aktivny' => true,
        ]);

        $this->assertSame(2, $product->images()->count());

        foreach ($product->images as $image) {
            $this->assertStringStartsWith('storage/products/', $image->url);
            Storage::disk('public')->assertExists(str_replace('storage/', '', $image->url));
        }
    }

    public function test_admin_product_create_requires_at_least_two_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'ADMIN',
        ]);

        $category = Kategoria::create([
            'nazov' => 'Pins',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), [
                'nazov' => 'Single image product',
                'popis' => 'This should not pass validation.',
                'kategoria_id' => $category->id,
                'cena' => 9.90,
                'skladom' => 3,
                'aktivny' => '1',
                'images' => [
                    UploadedFile::fake()->create('only.jpg', 100, 'image/jpeg'),
                ],
            ]);

        $response
            ->assertRedirect(route('admin.products.create'))
            ->assertSessionHasErrors('images');

        $this->assertDatabaseMissing('produkty', [
            'nazov' => 'Single image product',
        ]);
    }

    public function test_admin_can_update_product_and_add_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'ADMIN']);
        $category = Kategoria::create(['nazov' => 'Pins']);
        $newCategory = Kategoria::create(['nazov' => 'Plushies']);
        $product = $this->createAdminProduct($category);

        $response = $this->actingAs($admin)
            ->patch(route('admin.products.update', ['product' => $product->id]), [
                'nazov' => 'Updated product',
                'popis' => 'Updated description',
                'kategoria_id' => $newCategory->id,
                'cena' => 22.25,
                'skladom' => 8,
                'aktivny' => '1',
                'images' => [
                    UploadedFile::fake()->create('extra.jpg', 100, 'image/jpeg'),
                ],
            ]);

        $response
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('admin_success');

        $this->assertDatabaseHas('produkty', [
            'id' => $product->id,
            'nazov' => 'Updated product',
            'popis' => 'Updated description',
            'kategoria_id' => $newCategory->id,
            'aktivny' => true,
        ]);

        $this->assertDatabaseHas('varianty_produktu', [
            'produkt_id' => $product->id,
            'cena' => 22.25,
            'skladom' => 8,
        ]);

        $this->assertSame(3, $product->fresh()->images()->count());
    }

    public function test_admin_can_delete_product_image_and_physical_file(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'ADMIN']);
        $category = Kategoria::create(['nazov' => 'Pins']);
        $product = $this->createAdminProduct($category);
        $image = ProduktovyObrazok::create([
            'produkt_id' => $product->id,
            'url' => 'storage/products/delete-extra.jpg',
            'poradie' => 3,
        ]);

        Storage::disk('public')->put('products/delete-extra.jpg', 'fake image content');

        $response = $this->actingAs($admin)
            ->delete(route('admin.products.images.destroy', [
                'product' => $product->id,
                'image' => $image->id,
            ]));

        $response
            ->assertRedirect(route('admin.products.edit', ['product' => $product->id]))
            ->assertSessionHas('admin_success');

        $this->assertDatabaseMissing('produktove_obrazky', [
            'id' => $image->id,
        ]);
        Storage::disk('public')->assertMissing('products/delete-extra.jpg');
        $this->assertSame(2, $product->fresh()->images()->count());
    }

    public function test_admin_can_delete_product_without_orders(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'ADMIN']);
        $category = Kategoria::create(['nazov' => 'Pins']);
        $product = $this->createAdminProduct($category);
        $variantId = $product->variants()->firstOrFail()->id;

        Storage::disk('public')->put('products/delete-me.jpg', 'fake image content');

        $response = $this->actingAs($admin)
            ->delete(route('admin.products.destroy', ['product' => $product->id]));

        $response
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('admin_success');

        $this->assertDatabaseMissing('produkty', ['id' => $product->id]);
        $this->assertDatabaseMissing('varianty_produktu', ['id' => $variantId]);
        $this->assertSame(0, ProduktovyObrazok::where('produkt_id', $product->id)->count());
        Storage::disk('public')->assertMissing('products/delete-me.jpg');
    }

    public function test_admin_cannot_delete_product_that_is_used_in_order(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'ADMIN']);
        $category = Kategoria::create(['nazov' => 'Pins']);
        $product = $this->createAdminProduct($category);
        $variant = $product->variants()->firstOrFail();
        $order = $this->createOrderForVariant($variant);

        PolozkaObjednavky::create([
            'objednavka_id' => $order->id,
            'variant_id' => $variant->id,
            'mnozstvo' => 1,
            'jednotkova_cena' => 10,
            'celkova_cena' => 10,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.products.destroy', ['product' => $product->id]));

        $response
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('admin_error');

        $this->assertDatabaseHas('produkty', ['id' => $product->id]);
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

    private function createAdminProduct(Kategoria $category): Produkt
    {
        $product = Produkt::create([
            'nazov' => 'Editable product',
            'popis' => 'Editable product description.',
            'zakladna_cena' => 10,
            'kategoria_id' => $category->id,
            'aktivny' => true,
            'created_at' => now(),
        ]);

        VariantProduktu::create([
            'produkt_id' => $product->id,
            'nazov' => 'Default',
            'cena' => 10,
            'skladom' => 5,
            'aktivny' => true,
        ]);

        ProduktovyObrazok::create([
            'produkt_id' => $product->id,
            'url' => 'storage/products/delete-me.jpg',
            'poradie' => 1,
        ]);

        ProduktovyObrazok::create([
            'produkt_id' => $product->id,
            'url' => 'images/Products/prod-img-2.png',
            'poradie' => 2,
        ]);

        return $product;
    }

    private function createOrderForVariant(VariantProduktu $variant): Objednavka
    {
        $customer = Zakaznik::create([
            'meno' => 'Order Customer',
            'email' => 'order-customer@example.com',
            'telefon' => '+421 900 000 000',
            'created_at' => now(),
        ]);

        $delivery = Doprava::create([
            'nazov' => 'Courier',
            'cena' => 4.90,
            'odhad_dni' => 3,
            'aktivna' => true,
        ]);

        $payment = Platba::create([
            'sposob_platby' => 'Card',
            'poplatok' => 0,
            'aktivna' => true,
        ]);

        $address = Adresa::create([
            'zakaznik_id' => $customer->id,
            'meno' => 'Order Customer',
            'ulica' => 'Main street 1',
            'mesto' => 'Bratislava',
            'psc' => '81101',
            'stat' => 'Slovakia',
            'created_at' => now(),
        ]);

        return Objednavka::create([
            'zakaznik_id' => $customer->id,
            'adresa_id' => $address->id,
            'doprava_id' => $delivery->id,
            'platba_id' => $payment->id,
            'stav' => 'PENDING',
            'subtotal' => $variant->cena,
            'doprava_cena' => $delivery->cena,
            'platba_poplatok' => $payment->poplatok,
            'total' => (float) $variant->cena + (float) $delivery->cena,
            'created_at' => now(),
        ]);
    }
}
