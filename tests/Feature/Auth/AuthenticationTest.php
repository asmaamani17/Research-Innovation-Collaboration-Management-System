<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
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
            'staff_id' => $user->ic,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_be_remembered_when_the_option_is_selected(): void
    {
        $user = User::factory()->create(['remember_token' => null]);

        $response = $this->post('/login', [
            'staff_id' => $user->ic,
            'password' => 'password',
            'remember' => 'on',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertCookie('remember_web_'.sha1(SessionGuard::class));
        $this->assertNotNull($user->fresh()->remember_token);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'staff_id' => $user->ic,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_login_error_does_not_reveal_whether_the_staff_id_exists(): void
    {
        $user = User::factory()->create();

        $knownAccountResponse = $this->post('/login', [
            'staff_id' => $user->ic,
            'password' => 'incorrect-password',
        ]);
        $unknownAccountResponse = $this->post('/login', [
            'staff_id' => 'unknown-staff-id',
            'password' => 'incorrect-password',
        ]);

        $this->assertSame(
            $knownAccountResponse->getSession()->get('errors')->getBag('default')->first('staff_id'),
            $unknownAccountResponse->getSession()->get('errors')->getBag('default')->first('staff_id')
        );
    }

    public function test_failed_logins_are_limited_per_account_across_ip_addresses(): void
    {
        $staffId = 'account-limit-test';

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postInvalidLogin($staffId, '192.0.2.'.$attempt);
        }

        $response = $this->postInvalidLogin($staffId, '192.0.2.20');

        $response->assertSessionHasErrors('staff_id');
        $this->assertStringContainsString(
            'Too many login attempts',
            $response->getSession()->get('errors')->getBag('default')->first('staff_id')
        );
        $this->assertSame(
            5,
            RateLimiter::attempts('login:account:'.hash('sha256', $staffId))
        );
    }

    public function test_failed_logins_are_limited_per_ip_across_account_identifiers(): void
    {
        $ipAddress = '192.0.2.30';

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postInvalidLogin('account-'.$attempt, $ipAddress);
        }

        $response = $this->postInvalidLogin('another-account', $ipAddress);

        $response->assertSessionHasErrors('staff_id');
        $this->assertStringContainsString(
            'Too many login attempts',
            $response->getSession()->get('errors')->getBag('default')->first('staff_id')
        );
        $this->assertSame(
            5,
            RateLimiter::attempts('login:ip:'.hash('sha256', $ipAddress))
        );
    }

    private function postInvalidLogin(string $staffId, string $ipAddress)
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])->post('/login', [
            'staff_id' => $staffId,
            'password' => 'incorrect-password',
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
