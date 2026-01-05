<?php

use Illuminate\Support\Facades\Route;

// Controllers utama
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LostController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\UsernameCheckController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FoundController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PengambilanBarangController;
use App\Http\Controllers\ClaimController;

// Controllers admin
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\UserManagementController;

// OPTIONAL (bawaan Laravel untuk kirim email verifikasi)
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (TIDAK BUTUH LOGIN)
|--------------------------------------------------------------------------
*/

// Landing / home untuk guest & user
Route::get('/', [HomeController::class, 'index'])->name('home');

// List & detail barang hilang (bisa diakses guest)
Route::get('/cari', [LostController::class, 'index'])->name('lost.index');
Route::get('/barang/{item}', [LostController::class, 'show'])->name('lost.show');

// Login & Register (Laravel Breeze)
require __DIR__ . '/auth.php';

// AJAX cek username
Route::get('/check-username', [UsernameCheckController::class, 'check'])
    ->name('check.username')
    ->middleware('throttle:15,1');


Route::get('/map', [MapController::class, 'index'])->name('map.index');


/*
|--------------------------------------------------------------------------
| USER ROUTES (HARUS LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard user
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

// Chat
// Chat
Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');

// ✅ START CHAT DARI MAP/ITEM
Route::get('/chat/start/{type}/{id}', [ChatController::class, 'startFromItem'])
    ->where('type', 'hilang|temuan')
    ->whereNumber('id')
    ->name('chat.startFromItem');

// ✅ CLAIM CTA (auto kirim pesan template)
Route::get('/chat/claim/{type}/{id}', [ChatController::class, 'claimFromItem'])
    ->where('type', 'hilang|temuan')
    ->whereNumber('id')
    ->name('chat.claimFromItem');

Route::get('/chat/{thread}', [ChatController::class, 'show'])->name('chat.show');
Route::post('/chat/{thread}/send', [ChatController::class, 'send'])->name('chat.send');
Route::delete('/chat/{thread}', [ChatController::class, 'destroy'])->name('chat.destroy');
Route::post('/chat/{thread}/send-pickup-form', [ChatController::class, 'sendPickupForm'])
    ->name('chat.sendPickupForm');



    /*
    |--------------------------------------------------------------------------
    | Email Verification (recommended: bawaan Laravel/Breeze)
    |--------------------------------------------------------------------------
    | - verification.send : kirim email verifikasi
    | - verification.verify : hit link verifikasi
    | - verification.notice : halaman "please verify" (kalau kamu pakai middleware verified)
    */

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi.');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    // CRUD Lapor Barang
    Route::get('/lapor/kehilangan', [LostController::class, 'create'])->name('lost.create');
    Route::post('/lapor/kehilangan', [LostController::class, 'store'])->name('lost.store');

    Route::get('/lapor/temuan', [FoundController::class, 'create'])->name('found.create');
    Route::post('/lapor/temuan', [FoundController::class, 'store'])->name('found.store');

    // History user
    Route::get('/history', [LostController::class, 'history'])->name('user.history');

});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (LOGIN + ROLE ADMIN)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard admin
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Verifikasi laporan & penemuan
    Route::get('/verifikasi/laporan', [VerificationController::class, 'laporan'])->name('verify.laporan');
    Route::get('/verifikasi/penemuan', [VerificationController::class, 'penemuan'])->name('verify.penemuan');

    Route::get('/verifikasi/laporan/{item}', [VerificationController::class, 'showLaporan'])->name('verify.laporan.show');
    Route::post('/verifikasi/laporan/{item}/approve', [VerificationController::class, 'approveLaporan'])->name('verify.laporan.approve');
    Route::post('/verifikasi/laporan/{item}/reject', [VerificationController::class, 'rejectLaporan'])->name('verify.laporan.reject');

    // Manajemen User
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    // Moderasi Chat
    Route::get('/moderasi/chat', [AdminDashboardController::class, 'moderateChat'])->name('moderate.chat');

    // Barang sudah ditemukan
    Route::post('/items/{item}/mark-returned', [VerificationController::class, 'markReturned'])->name('items.markReturned');
});
