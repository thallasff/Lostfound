<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MapApiController;
use App\Http\Controllers\Api\SearchApiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dapatkan semua active items (untuk tampilkan marker di peta)
Route::get('/map/items', [MapApiController::class, 'items']); // public or auth based

// Cari barang (filter nama, lokasi_text, tanggal)
Route::get('/search/items', [SearchApiController::class, 'search']);

// Ambil detail item (json)
Route::get('/items/{item}', [MapApiController::class, 'show']);
