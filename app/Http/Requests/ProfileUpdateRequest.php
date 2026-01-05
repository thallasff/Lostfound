<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->user()?->getKey(); // aman untuk PK custom

        return [
            // username & email kita tampilkan readonly di view, tapi tetap aman kalau mau divalidasi
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('pelapor', 'email')->ignore($userId, 'pelapor_id'),
            ],

            // data profile tambahan (pelapor_profiles)
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'status'       => ['nullable', 'string', 'max:100'],
            'fakultas'     => ['nullable', 'string', 'max:100'],
            'jurusan'      => ['nullable', 'string', 'max:100'],
            'no_ponsel'    => ['nullable', 'string', 'max:30'],
            'foto_profil'  => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
