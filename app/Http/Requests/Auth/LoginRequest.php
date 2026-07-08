<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Login Process
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(
            [
                'nip' => $this->nip,
                'password' => $this->password,
            ],
            $this->boolean('remember')
        )) {

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'nip' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Rate Limiter
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Throttle Key
     */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('nip')) . '|' . $this->ip()
        );
    }
}