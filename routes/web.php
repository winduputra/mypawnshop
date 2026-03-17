<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiRahnController;
use App\Http\Controllers\PerpanjanganController;
use App\Http\Controllers\PelunasanController;
use App\Http\Controllers\LelangController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\LaporanController;
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Modules
    Route::resource('nasabah', NasabahController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('transaksi', TransaksiRahnController::class);

    // Extensions & Settlement
    Route::post('transaksi/{transaksi}/perpanjang', [PerpanjanganController::class, 'store'])->name('transaksi.perpanjang');
    Route::post('transaksi/{transaksi}/pelunasan', [PelunasanController::class, 'store'])->name('transaksi.pelunasan');
    Route::get('transaksi/{transaksi}/kontrak-pdf', [TransaksiRahnController::class, 'cetakKontrak'])->name('transaksi.kontrak-pdf');
    Route::get('transaksi/{transaksi}/nota-lunas', [TransaksiRahnController::class, 'cetakNotaLunas'])->name('transaksi.nota-lunas');
    
    // Angsuran (Cicilan)
    Route::post('transaksi/{transaksi}/angsuran', [TransaksiRahnController::class, 'bayarAngsuran'])->name('transaksi.angsuran');
    Route::get('transaksi/{transaksi}/angsuran/{angsuran}/cetak', [TransaksiRahnController::class, 'cetakBuktiAngsuran'])->name('transaksi.angsuran.cetak');
    Route::get('transaksi/{transaksi}/perpanjangan/{perpanjangan}/cetak', [PerpanjanganController::class, 'cetakNota'])->name('transaksi.perpanjangan.cetak');

    Route::post('lelang', [\App\Http\Controllers\LelangController::class, 'store'])->name('lelang.store');
    Route::get('lelang', [\App\Http\Controllers\LelangController::class, 'index'])->name('lelang.index');
    Route::get('lelang/{lelang}', [\App\Http\Controllers\LelangController::class, 'show'])->name('lelang.show');
    Route::get('lelang/{lelang}/hasil', [\App\Http\Controllers\LelangController::class, 'hasil'])->name('lelang.hasil');
    Route::get('lelang/{lelang}/cetak-pdf', [\App\Http\Controllers\LelangController::class, 'cetakPdf'])->name('lelang.cetak-pdf');

    // API for transaction create
    Route::get('/api/settings', [SettingController::class, 'getSettings'])->name('api.settings');

    // Admin-only routes
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::resource('cabang', \App\Http\Controllers\CabangController::class);
        Route::resource('tarif-ujrah', \App\Http\Controllers\TarifUjrahController::class)->except(['index', 'create', 'show', 'edit']);
        Route::get('pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
        Route::put('pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
        Route::resource('kasir', KasirController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
