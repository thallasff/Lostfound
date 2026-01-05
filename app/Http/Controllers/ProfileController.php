<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\PelaporProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();                 // UserPelapor
        $profile = $user->profile;            // PelaporProfile (bisa null)

        return view('profile.edit', compact('user', 'profile'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // kalau kamu mau email bisa diubah, biarkan ini:
        // kalau mau 100% readonly, hapus 2 baris ini.
        $user->email = $request->email;
        $user->save();

        // handle upload foto_profil (optional)
        $fotoPath = null;
        if ($request->hasFile('foto_profil')) {
            // hapus foto lama kalau ada
            if ($user->profile?->foto_profil) {
                Storage::disk('public')->delete($user->profile->foto_profil);
            }

            $fotoPath = $request->file('foto_profil')->store('foto_profil', 'public');
        }

        // create/update pelapor_profiles
        PelaporProfile::updateOrCreate(
            ['pelapor_id' => $user->pelapor_id],
            [
                'nama_lengkap' => $request->nama_lengkap,
                'status'       => $request->status,
                'fakultas'     => $request->fakultas,
                'jurusan'      => $request->jurusan,
                'no_ponsel'    => $request->no_ponsel,
                'foto_profil'  => $fotoPath ?? ($user->profile->foto_profil ?? null),
            ]
        );

        return back()->with('status', 'Profile berhasil diperbarui.');
    }
}
