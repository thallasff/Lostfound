<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPelapor;
use App\Models\PelaporProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $rows = UserPelapor::query()
            ->with('profile')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('username', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                })
                ->orWhereHas('profile', function ($p) use ($q) {
                    $p->where('nama_lengkap', 'like', "%{$q}%")
                      ->orWhere('status', 'like', "%{$q}%")
                      ->orWhere('fakultas', 'like', "%{$q}%")
                      ->orWhere('jurusan', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('pelapor_id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('rows', 'q'));
    }

public function show(UserPelapor $user)
{
    $profile = $user->profile; // relasi hasOne

    return view('admin.users.show', compact('user', 'profile'));
}

    public function destroy(UserPelapor $user)
    {
        DB::transaction(function () use ($user) {

            // 1) hapus profile
            PelaporProfile::where('pelapor_id', $user->pelapor_id)->delete();

            /**
             * OPTIONAL (kalau tabel kamu punya relasi pelapor_id):
             * - barang_hilang
             * - penemuan_barang
             * - chat_threads, chat_messages
             * - claims
             *
             * Kalau kamu belum pasang FK cascade dan delete user error,
             * nanti kirim struktur kolom FK-nya, kita rapihin.
             */

            // 2) hapus akun pelapor
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
