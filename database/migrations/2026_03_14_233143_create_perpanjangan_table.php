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
        Schema::create('perpanjangan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_rahn_id')->constrained('transaksi_rahn')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->date('tanggal_perpanjangan');
            $table->enum('tambahan_tenor_hari', ['30', '60', '90']);
            $table->date('tanggal_jatuh_tempo_baru');
            $table->decimal('ujrah_dibayar', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perpanjangan');
    }
};
