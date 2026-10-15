<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private string $pwnedPasswordResponse = '';

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(fn () => Http::response($this->pwnedPasswordResponse, 200));
    }

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
            'password' => 'Mango-River-Maple-2026!',
            'password_confirmation' => 'Mango-River-Maple-2026!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertTrue(Hash::check('Mango-River-Maple-2026!', auth()->user()->password));
    }

    public function test_registration_rejects_passwords_shorter_than_twelve_characters(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'short-password@example.com',
            'password' => 'Short123!',
            'password_confirmation' => 'Short123!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_rejects_compromised_passwords(): void
    {
        $password = 'Compromised-Phrase-2026!';
        $hash = strtoupper(sha1($password));
        $hashSuffix = substr($hash, 5);
        $this->pwnedPasswordResponse = $hashSuffix.':1';

        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'compromised-password@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
