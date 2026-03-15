<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksi_rahn', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('nasabah_id')->constrained('nasabah');
            $table->foreignId('user_id')->constrained('users'); // Kasir/Admin
            $table->date('tanggal_transaksi');
            $table->decimal('total_taksiran', 15, 2);
            $table->decimal('total_pinjaman', 15, 2); // Marhun Bih
            $table->decimal('biaya_admin', 15, 2);
            $table->decimal('ujrah_per_30hari', 15, 2);
            $table->enum('tenor_hari', ['30', '60', '90']);
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_batas_lelang'); // Jatuh tempo + 7 hari
            $table->enum('status', ['aktif', 'diperpanjang', 'lunas', 'lelang'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_rahn');
    }
};
