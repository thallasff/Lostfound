<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsernameCheckController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
        ]);

        $username = $request->query('username');

        // Ganti 'pelapor' dengan nama tabelmu kalau berbeda
        $exists = DB::table('pelapor')
                    ->where('username', $username)
                    ->exists();

        return response()->json([
            'exists' =>  $exists,
        ]);
    }
}
