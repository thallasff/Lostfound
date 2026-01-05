<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserPelapor;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:pelapor,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:pelapor,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()      // harus ada huruf
                    ->mixedCase()    // harus ada huruf kapital
                    ->numbers()      // harus ada angka
            ],
        ]);

        $user = UserPelapor::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
    public function checkUsername(Request $request)
    {
        $exists = UserPelapor::where('username', $request->username)->exists();
        return response()->json(['exists' => $exists]);
    }
}
