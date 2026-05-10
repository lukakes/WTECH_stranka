<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'CUSTOMER',
        ]);
    }

    public function test_users_cannot_register_with_existing_email(): void
    {
        User::factory()->create([
            'email' => 'taken@example.com',
            'role' => 'CUSTOMER',
        ]);

        $response = $this->from(route('register'))->post(route('register'), [
            'name' => 'Another User',
            'email' => 'taken@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(1, User::where('email', 'taken@example.com')->count());
    }

    public function test_registered_customer_can_log_in_with_registered_credentials(): void
    {
        $this->post(route('register'), [
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        auth()->logout();
        $this->assertGuest();

        $response = $this->post(route('login'), [
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
