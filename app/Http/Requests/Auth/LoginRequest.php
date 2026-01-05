<?php

namespace App\Http\Requests\Auth;

use App\Models\UserPelapor;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $username = (string) $this->input('username');
        $remember = $this->boolean('remember');

        // 1) cek username ada atau tidak
        $user = UserPelapor::where('username', $username)->first();

        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            // reset semua input
            $this->session()->flashInput([]);

            throw ValidationException::withMessages([
                'username' => 'Username Anda tidak terdaftar atau salah.',
            ]);
        }

        // 2) username ada, cek password
        if (!Auth::attempt(['username' => $username, 'password' => $this->input('password')], $remember)) {
            RateLimiter::hit($this->throttleKey());

            // simpan username biar tidak kehapus
            $this->session()->flashInput($this->only('username', 'remember'));

            throw ValidationException::withMessages([
                'password' => 'Password salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('username')) . '|' . $this->ip());
    }
}
