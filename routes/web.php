<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BidanController;
use App\Http\Controllers\KaderController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\IbuHamilController;
use App\Http\Controllers\JadwalPosyanduController;
use App\Http\Controllers\LaporanExportController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

Route::post('/logout-ajax', [LoginController::class, 'logoutAjax'])
    ->name('logout.ajax')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| AUTH CHECK (untuk JS cek session)
|--------------------------------------------------------------------------
*/
Route::get('/auth/check', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'user_id'       => Auth::id(),
    ]);
})->name('auth.check');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // ==========================================
    // A. KADER — Dashboard, Laporan, Jadwal CRUD
    // ==========================================
    Route::middleware('role:kader')->prefix('kader')->name('kader.')->group(function () {
        Route::get('/dashboard', [KaderController::class, 'dashboard'])->name('dashboard');
        Route::get('/laporan-posyandu', [KaderController::class, 'laporanPosyandu'])->name('laporan.posyandu');

        // ===== JADWAL POSYANDU — CRUD HANYA KADER =====
        Route::get('/jadwal', [JadwalPosyanduController::class, 'index'])->name('jadwal.index');
        Route::get('/jadwal/create', [JadwalPosyanduController::class, 'create'])->name('jadwal.create');
        Route::post('/jadwal', [JadwalPosyanduController::class, 'store'])->name('jadwal.store');
        Route::get('/jadwal/{id}/edit', [JadwalPosyanduController::class, 'edit'])->name('jadwal.edit');
        Route::put('/jadwal/{id}', [JadwalPosyanduController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{id}', [JadwalPosyanduController::class, 'destroy'])->name('jadwal.destroy');
    });

    // ==========================================
    // B. SHARED — Data Balita, Bumil, Riwayat Jadwal, Export
    //    (kader, bidan, admin)
    // ==========================================
    Route::middleware('role:kader,bidan,admin')->group(function () {

        // ---------- BALITA ----------
        Route::get('/balita/{id}/checkup', [BalitaController::class, 'checkup'])->name('balita.checkup');
        Route::post('/balita/{id}/checkup', [BalitaController::class, 'storeCheckup'])->name('balita.storeCheckup');
        Route::post('/balita/{id}/imunisasi', [BalitaController::class, 'simpanImunisasi'])->name('balita.imunisasi.store');
        Route::delete('/balita/pemeriksaan/{id}', [BalitaController::class, 'hapusPemeriksaan'])->name('balita.pemeriksaan.hapus');
        Route::resource('balita', BalitaController::class);

        // ---------- IBU HAMIL ----------
        Route::get('/ibu-hamil/{id}/checkup', [IbuHamilController::class, 'checkup'])->name('ibu-hamil.checkup');
        Route::post('/ibu-hamil/{id}/checkup', [IbuHamilController::class, 'storeCheckup'])->name('ibu-hamil.storeCheckup');
        Route::post('/ibu-hamil/{id}/imunisasi', [IbuHamilController::class, 'simpanImunisasi'])->name('ibu-hamil.imunisasi.store');
        Route::delete('/ibu-hamil/pemeriksaan/{id}', [IbuHamilController::class, 'hapusPemeriksaan'])->name('ibu-hamil.pemeriksaan.hapus');
        Route::resource('ibu-hamil', IbuHamilController::class);

        // ---------- RIWAYAT JADWAL (read-only untuk Bidan & Admin) ----------
        Route::get('/jadwal-riwayat', [JadwalPosyanduController::class, 'riwayat'])->name('jadwal.riwayat');

        // ---------- EXPORT LAPORAN ----------
        Route::get('/laporan-posyandu/pdf', [LaporanExportController::class, 'pdf'])->name('laporan-posyandu.pdf');
        Route::get('/laporan-posyandu/excel', [LaporanExportController::class, 'excel'])->name('laporan-posyandu.excel');
    });

    // ==========================================
    // C. BIDAN
    // ==========================================
    Route::middleware('role:bidan')->prefix('bidan')->name('bidan.')->group(function () {
        Route::get('/dashboard', [BidanController::class, 'dashboard'])->name('dashboard');
        Route::get('/validasi/balita', [BidanController::class, 'validasiBalita'])->name('validasi.balita');
        Route::post('/validasi/balita/bulk', [BidanController::class, 'prosesValidasiBalitaBulk'])->name('validasi.balita.bulk');
        Route::post('/validasi/balita/{id}', [BidanController::class, 'prosesValidasiBalita'])->name('validasi.balita.proses');
        Route::get('/validasi/bumil', [BidanController::class, 'validasiBumil'])->name('validasi.bumil');
        Route::post('/validasi/bumil/bulk', [BidanController::class, 'prosesValidasiBumilBulk'])->name('validasi.bumil.bulk');
        Route::post('/validasi/bumil/{id}', [BidanController::class, 'prosesValidasiBumil'])->name('validasi.bumil.proses');
        Route::get('/darurat', [BidanController::class, 'pemeriksaanDarurat'])->name('darurat');
        Route::post('/darurat', [BidanController::class, 'storePemeriksaanDarurat'])->name('darurat.store');
        Route::get('/laporan/posyandu', [BidanController::class, 'laporanPosyandu'])->name('laporan.posyandu');
        Route::get('/laporan/darurat', [BidanController::class, 'laporanDarurat'])->name('laporan.darurat');
    });

    // ==========================================
    // D. ADMIN
    // ==========================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/kelola-akun', [AdminController::class, 'kelolaAkun'])->name('kelola-akun');
        Route::post('/tambah-user', [AdminController::class, 'tambahUser'])->name('tambah-user');
        Route::get('/edit-user/{id}', [AdminController::class, 'editUser'])->name('edit-user');
        Route::put('/update-user/{id}', [AdminController::class, 'updateUser'])->name('update-user');
        Route::delete('/hapus-user/{id}', [AdminController::class, 'hapusUser'])->name('hapus-user');
        Route::post('/toggle-status/{id}', [AdminController::class, 'toggleStatusUser'])->name('toggle-status');
        Route::get('/laporan-posyandu', [AdminController::class, 'laporanPosyandu'])->name('laporan-posyandu');
        Route::get('/laporan-darurat', [AdminController::class, 'laporanDarurat'])->name('laporan-darurat');
    });

    Route::get('/login-success', [LoginController::class, 'loginSuccess'])
    ->name('login.success')
    ->middleware('auth');
});