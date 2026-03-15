<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiRahnController;
use App\Http\Controllers\PerpanjanganController;
use App\Http\Controllers\PelunasanController;
use App\Http\Controllers\LelangController;

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
    Route::resource('lelang', LelangController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
