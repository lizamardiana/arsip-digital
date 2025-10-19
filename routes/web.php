<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Auth::routes(['register' => false]);

// Public Routes
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Surat Masuk Routes
    Route::prefix('surat-masuk')->name('surat-masuk.')->group(function () {
        Route::get('/', [SuratMasukController::class, 'index'])->name('index');
        Route::get('/create', [SuratMasukController::class, 'create'])->name('create');
        Route::post('/', [SuratMasukController::class, 'store'])->name('store');
        Route::get('/{suratMasuk}', [SuratMasukController::class, 'show'])->name('show');
        Route::get('/{suratMasuk}/edit', [SuratMasukController::class, 'edit'])->name('edit');
        Route::put('/{suratMasuk}', [SuratMasukController::class, 'update'])->name('update');
        Route::delete('/{suratMasuk}', [SuratMasukController::class, 'destroy'])->name('destroy');
        // Ganti ini:
        Route::get('/{suratMasuk}/download', [SuratMasukController::class, 'downloadForce'])->name('download');
    });

    // Surat Keluar Routes
    Route::prefix('surat-keluar')->name('surat-keluar.')->group(function () {
        Route::get('/', [SuratKeluarController::class, 'index'])->name('index');
        Route::get('/create', [SuratKeluarController::class, 'create'])->name('create');
        Route::post('/', [SuratKeluarController::class, 'store'])->name('store');
        Route::get('/{suratKeluar}', [SuratKeluarController::class, 'show'])->name('show');
        Route::get('/{suratKeluar}/edit', [SuratKeluarController::class, 'edit'])->name('edit');
        Route::put('/{suratKeluar}', [SuratKeluarController::class, 'update'])->name('update');
        Route::delete('/{suratKeluar}', [SuratKeluarController::class, 'destroy'])->name('destroy');
        Route::get('/{suratKeluar}/download', [SuratKeluarController::class, 'download'])->name('download');
    });

    // Laporan Routes
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/generate', [LaporanController::class, 'generate'])->name('generate');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/quick-report', [LaporanController::class, 'quickReport'])->name('quick-report');
    });

    // Profile Routes
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password');

    // Admin Only Routes
    Route::middleware(['admin'])->group(function () {
        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

    });
});