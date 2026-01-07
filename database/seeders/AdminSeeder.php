<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAdmin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        UserAdmin::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Admin Default',
                'password' => Hash::make('admin'),
            ]
        );
    }
}
