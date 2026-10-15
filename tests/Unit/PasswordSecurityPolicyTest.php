<?php

namespace Tests\Unit;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

class PasswordSecurityPolicyTest extends TestCase
{
    private string $pwnedPasswordResponse = '';

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(fn () => Http::response($this->pwnedPasswordResponse, 200));
    }

    public function test_password_policy_requires_twelve_characters_and_rejects_breached_passwords(): void
    {
        $passwordRule = Password::defaults();
        $uncompromised = new \ReflectionProperty($passwordRule, 'uncompromised');
        $this->assertTrue($uncompromised->getValue($passwordRule));
        $rules = ['password' => ['required', $passwordRule]];

        $this->assertTrue(
            Validator::make(['password' => 'Bright-Morning-Maple-2042!'], $rules)->passes()
        );
        $this->assertFalse(
            Validator::make(['password' => 'Short123!'], $rules)->passes()
        );

        $password = 'Compromised-Phrase-2042!';
        $hash = strtoupper(sha1($password));
        $hashSuffix = substr($hash, 5);
        $this->pwnedPasswordResponse = $hashSuffix.':1';

        $compromisedPasswordIsValid = Validator::make(['password' => $password], $rules)->passes();
        Http::assertSent(fn (ClientRequest $request): bool => str_ends_with($request->url(), substr($hash, 0, 5)));
        $this->assertFalse($compromisedPasswordIsValid);
    }

    public function test_login_rate_limit_keys_track_account_and_ip_independently(): void
    {
        $firstRequest = LoginRequest::create('/login', 'POST', ['staff_id' => 'RICM-STAFF']);
        $firstRequest->server->set('REMOTE_ADDR', '192.0.2.10');

        $sameAccountDifferentIp = LoginRequest::create('/login', 'POST', ['staff_id' => 'RICM-STAFF']);
        $sameAccountDifferentIp->server->set('REMOTE_ADDR', '192.0.2.11');

        $differentAccountSameIp = LoginRequest::create('/login', 'POST', ['staff_id' => 'OTHER-STAFF']);
        $differentAccountSameIp->server->set('REMOTE_ADDR', '192.0.2.10');

        [$accountKey, $ipKey] = $firstRequest->throttleKeys();
        [$otherIpAccountKey, $otherIpKey] = $sameAccountDifferentIp->throttleKeys();
        [$otherAccountKey, $sameIpKey] = $differentAccountSameIp->throttleKeys();

        $this->assertSame($accountKey, $otherIpAccountKey);
        $this->assertNotSame($ipKey, $otherIpKey);
        $this->assertNotSame($accountKey, $otherAccountKey);
        $this->assertSame($ipKey, $sameIpKey);
    }
}
