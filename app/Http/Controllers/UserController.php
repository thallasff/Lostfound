<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PelaporProfile;


class UserController extends Controller
{
    // TAMPILKAN HALAMAN PROFILE
    public function profile()
    {
        $user = Auth::user();

        // ambil data pelapor profile (1 user = 1 profile)
$pelapor = PelaporProfile::firstOrCreate(
    ['pelapor_id' => $user->id],
    [
        'nama_lengkap' => $user->name,
    ]
);


        return view('profile.edit', compact('user', 'pelapor'));
    }

    // UPDATE PROFILE (data diri + foto nanti)
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            // field lain nanti nyusul
        ]);

        $user->name = $request->name;

        // kalau email diganti → verifikasi ulang
        if ($user->email !== $request->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with('success', 'Profile berhasil diperbarui');
    }
}
