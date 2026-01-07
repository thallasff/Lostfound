<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminProfileController extends Controller
{
    public function edit()
    {
        $admin = auth('admin')->user();
        return view('admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = auth('admin')->user();

        $data = $request->validate([
            'username' => 'required|string|max:50|unique:admin,username,' . $admin->admin_id . ',admin_id',
            'nama'     => 'required|string|max:100',
        ]);

        $admin->update($data);

        return back()->with('success', 'Profil admin berhasil diperbarui.');
    }
}
