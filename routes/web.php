<?php

use App\Http\Controllers\GoogleController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request; // ✔️ Benar


// ======================
// 👤 GUEST ROUTES
// ======================
Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/login-sso', 'auth.login-sso')->name('login-sso');
    Volt::route('/register', 'auth.register');
    Volt::route('/forgot-password', 'auth.forgot-password')->name('password.request');
    Volt::route('/reset-password/{token}', 'auth.password-reset')->name('password.reset');
    Route::get('/auth-google-redirect', [GoogleController::class, 'google_redirect'])->name('google-redirect');
    Route::get('/auth-google-callback', [GoogleController::class, 'google_callback'])->name('google-callback');
    
    Route::get('/sso/callback', function (Request $request) {
        $token = $request->query('token');

        if (!$token) {
            return redirect('/login')->withErrors(['Token SSO tidak ditemukan.']);
        }

        // Ambil data user dari server SSO (sekarang: 127.0.0.1:8000)
        $response = Http::withToken($token)->get('http://127.0.0.1:8001/api/me');

        if (!$response->successful()) {
            return redirect('/login')->withErrors(['SSO gagal. Token tidak valid.']);
        }

        $userData = $response->json();

        if (empty($userData['email']) || empty($userData['name'])) {
            return redirect('/login')->withErrors(['Data user dari SSO tidak lengkap.']);
        }

        $user = User::where('email', $userData['email'])->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'User tidak terdaftar di sistem ini.',
            ]);
        }

        Auth::login($user);
        session()->regenerate();

        // Redirect berdasarkan role
        if ($user->role_id == 4) {
            return back();
        }

        return redirect('/');
    });
});

// ======================
// 🔓 LOGOUT
// ======================
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

// ======================
// 🔐 AUTHENTICATED ROUTES
// ======================
Route::middleware('auth')->group(function () {
    
    Volt::route('/dashboard-sso', 'dashboard-sso')->name('dashboard-sso');
    // 📧 EMAIL VERIFICATION
    Volt::route('/profile', 'auth.ubahprofile');
    Volt::route('/email/verify', 'auth.verify-email')->middleware('throttle:6,1')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/')->with('success', 'Email berhasil diverifikasi!');
    })->middleware('signed')->name('verification.verify');

        Volt::route('/', 'index');

    // ======================
    // 🛡️ ADMIN ROUTES - akses penuh
    // ======================
    Route::middleware('role:1')->group(function () {
        // User Management
        Volt::route('/users', 'users.index');
        Volt::route('/users/create', 'users.create');
        Volt::route('/users/{user}/edit', 'users.edit');

        Volt::route('/roles', 'roles.index');
    });

    // ======================
    // 🛡️ MANAGER ROUTES - barang, satuan, jenis
    // ======================
    Route::middleware('role:1,2')->group(function () {
        Volt::route('/satuans', 'satuans.index');
        Volt::route('/jenisbarangs', 'jenisbarangs.index');

        Volt::route('/barangs', 'barangs.index');
        Volt::route('/barangs/create', 'barangs.create');
        Volt::route('/barangs/{barang}/edit', 'barangs.edit');
    });

    // ======================
    // 🛡️ KASIR ROUTES - barang masuk & keluar
    // ======================
    Route::middleware('role:1,2')->group(function () {
        Volt::route('/barangmasuks', 'barangmasuks.index');
        Volt::route('/barangmasuks/create', 'barangmasuks.create');
        Volt::route('/barangmasuks/{masuk}/edit', 'barangmasuks.edit');

        Volt::route('/barangkeluars', 'barangkeluars.index');
        Volt::route('/barangkeluars/create', 'barangkeluars.create');
        Volt::route('/barangkeluars/{keluar}/edit', 'barangkeluars.edit');
    });

});
