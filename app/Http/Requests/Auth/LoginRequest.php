<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 60;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'staff_id' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt([
            'ic' => $this->input('staff_id'),
            'password' => $this->input('password'),
        ], $this->boolean('remember'))) {
            foreach ($this->throttleKeys() as $key) {
                RateLimiter::hit($key, self::DECAY_SECONDS);
            }

            throw ValidationException::withMessages([
                'staff_id' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKeys()[0]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $limitedKeys = array_filter(
            $this->throttleKeys(),
            fn (string $key): bool => RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)
        );

        if ($limitedKeys === []) {
            return;
        }

        event(new Lockout($this));

        $seconds = max(array_map(
            fn (string $key): int => RateLimiter::availableIn($key),
            $limitedKeys
        ));

        throw ValidationException::withMessages([
            'staff_id' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKeys(): array
    {
        $identifier = Str::transliterate(Str::lower($this->string('staff_id')->toString()));

        return [
            'login:account:'.hash('sha256', $identifier),
            'login:ip:'.hash('sha256', (string) $this->ip()),
        ];
    }
}
